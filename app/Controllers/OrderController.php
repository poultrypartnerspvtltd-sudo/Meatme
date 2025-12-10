<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;
use App\Models\Product;

class OrderController extends Controller
{
    public function index()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Session::flash('error', 'Please login to view your orders.');
            $this->redirect('login');
        }
        
        $user = Auth::user();
        $db = Database::getInstance();
        
        // Get user's orders
        $orders = $this->getUserOrders($user['id']);
        
        $data = [
            'title' => 'My Orders',
            'user' => $user,
            'orders' => $orders
        ];
        
        $this->render('orders.index', $data);
    }
    
    public function checkout()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Session::flash('error', 'Please login to proceed to checkout.');
            $this->redirect('login');
        }

        $cart = Session::getCart();

        if (empty($cart)) {
            Session::flash('error', 'Your cart is empty.');
            $this->redirect('cart');
        }
        
        $cartItems = [];
        $subtotal = 0;
        
        foreach ($cart as $productId => $item) {
            $product = (new Product())->find($productId);
            
            if ($product && $product['is_active']) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price']
                ];
                
                $subtotal += $item['quantity'] * $item['price'];
            }
        }
        
        // Calculate totals (no VAT/tax)
        $subtotalWithTax = $subtotal;
        
        $data = [
            'title' => 'Checkout',
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'subtotalWithTax' => $subtotalWithTax,
            'cartCount' => Session::getCartCount()
        ];
        
        $this->render('checkout', $data);
    }
    
    public function process()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            $this->json(['success' => false, 'message' => 'Please login to place an order.']);
        }

        $cart = Session::getCart();

        if (empty($cart)) {
            $this->json(['success' => false, 'message' => 'Your cart is empty.']);
        }
        
        $user = Auth::user();
        
        // Get form data
        $paymentMethod = $this->input('payment_method');
        $shippingType = $this->input('shipping_type');
        $firstName = $this->input('first_name');
        $lastName = $this->input('last_name');
        $name = $this->input('name') ?: ($firstName . ' ' . $lastName);
        $phone = $this->input('phone');
        $email = $this->input('email');
        $country = $this->input('country', 'Nepal');
        $address = $this->input('address');
        $addressLine1 = $this->input('address_line_1');
        $apartment = $this->input('apartment', '');
        $city = $this->input('city', '');
        $state = $this->input('state', '');
        $postalCode = $this->input('postal_code', '');
        $notes = $this->input('notes', '');

        // Validate required fields
        if (!$paymentMethod || !$shippingType || !$firstName || !$lastName || !$phone || !$email) {
            $this->json(['success' => false, 'message' => 'All required fields must be filled.']);
        }

        // For home delivery, use provided address or default to customer's location
        // Address fields are optional - if not provided, use a default address
        if ($shippingType === 'home_delivery') {
            // If address fields are not provided, use default values
            if (!$addressLine1) {
                $addressLine1 = 'Address to be confirmed';
            }
            if (!$city) {
                $city = 'Butwal';
            }
            if (!$state) {
                $state = 'Lumbini';
            }
            if (!$postalCode) {
                $postalCode = '32907';
            }
        }

        if (!in_array($paymentMethod, ['COD'])) {
            $this->json(['success' => false, 'message' => 'Invalid payment method.']);
        }

        if (!in_array($shippingType, ['self_pickup', 'home_delivery'])) {
            $this->json(['success' => false, 'message' => 'Invalid shipping method.']);
        }
        
        // Calculate totals
        $cartItems = [];
        $subtotal = 0;
        
        foreach ($cart as $productId => $item) {
            $product = (new Product())->find($productId);
            
            if ($product && $product['is_active']) {
                // Check stock availability
                if ($product['stock_quantity'] < $item['quantity']) {
                    $this->json(['success' => false, 'message' => 'Insufficient stock for ' . $product['name']]);
                }
                
                $cartItems[] = [
                    'product_id' => $productId,
                    'product_name' => $product['name'],
                    'product_sku' => $product['sku'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['quantity'] * $item['price']
                ];
                
                $subtotal += $item['quantity'] * $item['price'];
            }
        }
        
        // Calculate delivery fee based on shipping type (no VAT/tax)
        $deliveryFee = ($shippingType === 'home_delivery') ? 10 : 0; // Rs. 10 for home delivery, free for self pickup
        $totalAmount = $subtotal + $deliveryFee;
        
        // Set payment status (COD is always unpaid)
        $paymentStatus = 'unpaid';
        
        // Create delivery address JSON with all fields
        $deliveryAddress = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'country' => $country,
            'address' => $address ?: ($addressLine1 . ($apartment ? ', ' . $apartment : '') . ', ' . $city . ', ' . $state . ' ' . $postalCode),
            'address_line_1' => $addressLine1,
            'apartment' => $apartment,
            'city' => $city,
            'state' => $state,
            'postal_code' => $postalCode,
            'shipping_type' => $shippingType
        ];
        
        try {
            $db = Database::getInstance();
            $db->beginTransaction();

            // Debug: Log that we're starting order processing
            error_log("Starting order processing for user: " . $user['id']);
            
            // Generate order number
            $orderNumber = 'ORD-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Insert order
            $stmt = $db->prepare("
                INSERT INTO orders (
                    order_number, user_id, status, payment_status, payment_method,
                    subtotal, delivery_fee, total_amount, delivery_type,
                    delivery_address, notes, created_at
                ) VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $orderNumber,
                $user['id'],
                $paymentStatus,
                $paymentMethod,
                $subtotal,
                $deliveryFee,
                $totalAmount,
                $shippingType,
                json_encode($deliveryAddress),
                $notes
            ]);
            
            $orderId = $db->lastInsertId();
            
            // Insert order items and deduct stock
            $stmt = $db->prepare("
                INSERT INTO order_items (
                    order_id, product_id, product_name, product_sku,
                    quantity, unit_price, total_price, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            foreach ($cartItems as $item) {
                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['product_name'],
                    $item['product_sku'],
                    $item['quantity'],
                    $item['unit_price'],
                    $item['total_price']
                ]);
                
                // Deduct stock from product
                $updateStock = $db->prepare("
                    UPDATE products 
                    SET stock_quantity = stock_quantity - ? 
                    WHERE id = ? AND stock_quantity >= ?
                ");
                $updateStock->execute([
                    $item['quantity'],
                    $item['product_id'],
                    $item['quantity']
                ]);
            }
            
            $db->commit();
            
            // Clear cart
            Session::clearCart();
            
            // Send confirmation emails
            $this->sendOrderEmails($orderId, $user, $cartItems, $totalAmount, $paymentMethod, $deliveryAddress);

            $this->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $orderId,
                'order_number' => $orderNumber
            ]);

        } catch (\Exception $e) {
            $db->rollBack();
            error_log("Order processing error: " . $e->getMessage());
            error_log("Exception trace: " . $e->getTraceAsString());

            $this->json(['success' => false, 'message' => 'Failed to process order. Please try again.']);
        }
    }
    
    public function success()
    {
        $orderId = $this->input('order_id');
        $orderNumber = $this->input('order_number');
        
        $data = [
            'title' => 'Order Confirmed',
            'orderId' => $orderId,
            'orderNumber' => $orderNumber
        ];
        
        $this->render('order_success', $data);
    }

    private function getUserOrders($userId)
    {
        try {
            $db = Database::getInstance();

            // Get orders with order items
            $stmt = $db->prepare("
                SELECT
                    o.*,
                    GROUP_CONCAT(
                        CONCAT(oi.product_name, ' (', oi.quantity, ')')
                        SEPARATOR ', '
                    ) as order_items,
                    COUNT(oi.id) as total_items
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.user_id = ?
                GROUP BY o.id
                ORDER BY o.created_at DESC
            ");
            $stmt->execute([$userId]);
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Format the orders for display
            foreach ($orders as &$order) {
                $order['formatted_date'] = date('M d, Y', strtotime($order['created_at']));
                $order['formatted_time'] = date('H:i', strtotime($order['created_at']));
                $order['status_badge'] = $this->getStatusBadge($order['status']);
            }

            return $orders;

        } catch (\PDOException $e) {
            error_log("Error getting user orders: " . $e->getMessage());
            return [];
        }
    }

    private function getStatusBadge($status)
    {
        $statusColors = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'preparing' => 'primary',
            'out_for_delivery' => 'info',
            'delivered' => 'success',
            'completed' => 'success',
            'cancelled' => 'danger'
        ];

        $color = $statusColors[$status] ?? 'secondary';
        $statusText = ucfirst(str_replace('_', ' ', $status));

        return "<span class='badge bg-{$color}'>{$statusText}</span>";
    }

    public function show($id)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Session::flash('error', 'Please login to view your orders.');
            $this->redirect('login');
        }

        $user = Auth::user();
        $db = Database::getInstance();

        try {
            // Get order details
            $stmt = $db->prepare("
                SELECT o.*, u.name as customer_name, u.email as customer_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = ? AND o.user_id = ?
            ");
            $stmt->execute([$id, $user['id']]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order) {
                Session::flash('error', 'Order not found.');
                $this->redirect('orders');
            }

            // Get order items
            $stmt = $db->prepare("
                SELECT oi.*, p.image as product_image
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
                ORDER BY oi.id
            ");
            $stmt->execute([$id]);
            $orderItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Format order data
            $order['formatted_date'] = date('M d, Y', strtotime($order['created_at']));
            $order['formatted_time'] = date('H:i', strtotime($order['created_at']));
            $order['status_badge'] = $this->getStatusBadge($order['status']);

            // Parse delivery address if it exists
            if ($order['delivery_address']) {
                $order['delivery_address_parsed'] = json_decode($order['delivery_address'], true);
            }

            $data = [
                'title' => 'Order Details - ' . $order['order_number'],
                'order' => $order,
                'orderItems' => $orderItems,
                'user' => $user
            ];

            $this->render('orders/show', $data);

        } catch (\PDOException $e) {
            error_log("Error getting order details: " . $e->getMessage());
            Session::flash('error', 'Error loading order details.');
            $this->redirect('orders');
        }
    }

    private function sendOrderEmails($orderId, $user, $cartItems, $totalAmount, $paymentMethod, $deliveryAddress)
    {
        try {
            // Send customer confirmation email
            $this->sendCustomerEmail($orderId, $user, $cartItems, $totalAmount, $paymentMethod, $deliveryAddress);
            
            // Send admin notification email
            $this->sendAdminEmail($orderId, $user, $cartItems, $totalAmount, $paymentMethod, $deliveryAddress);
            
        } catch (\Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            // Don't fail the order if email fails
        }
    }
    
    private function sendCustomerEmail($orderId, $user, $cartItems, $totalAmount, $paymentMethod, $deliveryAddress)
    {
        // For now, just log the email instead of sending (to avoid PHPMailer issues)
        error_log("Would send customer email for order $orderId to {$user['email']}");
        return;
    }

    private function sendAdminEmail($orderId, $user, $cartItems, $totalAmount, $paymentMethod, $deliveryAddress)
    {
        // For now, just log the email instead of sending (to avoid PHPMailer issues)
        error_log("Would send admin email for order $orderId");
        return;
    }

}
?>
