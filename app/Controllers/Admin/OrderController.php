<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Order;
use App\Models\User;

class OrderController extends Controller
{
    public function __construct()
{
    // Initialize parent controller (VERY IMPORTANT)
    parent::__construct();

    // Check if user is admin
    if (!Auth::check() || !Auth::isAdmin()) {
        $this->redirect('/admin/login');
    }
}

    public function index()
    {
        try {
            // Get all orders with user information
            $orders = $this->getAllOrders();

            $data = [
                'title' => 'Order Management',
                'orders' => $orders,
                'user' => Auth::user(),
                'stats' => $this->getOrderStats()
            ];

            $this->render('admin.orders', $data);
        } catch (\Exception $e) {
            error_log("Admin orders error: " . $e->getMessage());
            $this->redirect('/admin/dashboard');
        }
    }

    public function show($orderId)
    {
        try {
            $order = $this->getOrderById($orderId);
            if (!$order) {
                $this->redirect('/admin/orders');
            }

            $data = [
                'title' => 'Order Details - ' . $order['order_number'],
                'order' => $order,
                'user' => Auth::user()
            ];

            $this->render('admin.order_detail', $data);
        } catch (\Exception $e) {
            error_log("Admin order detail error: " . $e->getMessage());
            $this->redirect('/admin/orders');
        }
    }

    public function updateStatus($orderId)
    {
        try {
            $newStatus = $this->input('status');
            $notes = $this->input('notes', '');

            if (!$newStatus || !in_array($newStatus, ['pending', 'confirmed', 'preparing', 'out_for_delivery', 'delivered', 'cancelled'])) {
                $this->json(['success' => false, 'message' => 'Invalid status']);
            }

            // Verify order exists using direct DB fetch (safer in case Model::find behaves unexpectedly)
            $db = \App\Core\Database::getInstance();
            $existing = $db->fetch("SELECT * FROM orders WHERE id = :id LIMIT 1", ['id' => $orderId]);

            if (!$existing) {
                $this->json(['success' => false, 'message' => 'Order not found']);
            }

            // Prepare update data
            $updateData = ['status' => $newStatus];
            if ($newStatus === 'delivered') {
                $updateData['delivered_at'] = date('Y-m-d H:i:s');
            } elseif ($newStatus === 'cancelled') {
                $updateData['cancelled_at'] = date('Y-m-d H:i:s');
                if ($notes) {
                    $updateData['cancellation_reason'] = $notes;
                }
            }

            // Use model update for fillable handling
            $orderModel = new Order();
            $success = $orderModel->update($orderId, $updateData);

            if ($success) {
                $this->json(['success' => true, 'message' => 'Order status updated successfully']);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to update order status']);
            }

        } catch (\Exception $e) {
            error_log("Admin update status error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while updating order status']);
        }
    }

    private function getAllOrders()
    {
        try {
            $db = \App\Core\Database::getInstance();

            $query = "
                SELECT
                    o.*,
                    u.name as customer_name,
                    u.email as customer_email,
                    u.phone as customer_phone,
                    DATE_FORMAT(o.created_at, '%M %d, %Y %h:%i %p') as formatted_date,
                    (
                        SELECT GROUP_CONCAT(
                            CONCAT(oi.quantity, 'x ', p.name, ' (', p.unit, ')')
                            SEPARATOR ', '
                        )
                        FROM order_items oi
                        JOIN products p ON oi.product_id = p.id
                        WHERE oi.order_id = o.id
                    ) as items_summary
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
            ";

            return $db->fetchAll($query);
        } catch (\Exception $e) {
            error_log("Get all orders error: " . $e->getMessage());
            return [];
        }
    }

    private function getOrderById($orderId)
    {
        try {
            $db = \App\Core\Database::getInstance();

            $query = "
                SELECT
                    o.*,
                    u.name as customer_name,
                    u.email as customer_email,
                    u.phone as customer_phone,
                    DATE_FORMAT(o.created_at, '%M %d, %Y %h:%i %p') as formatted_date,
                    DATE_FORMAT(o.updated_at, '%M %d, %Y %h:%i %p') as formatted_updated_date
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = ?
            ";

            $order = $db->fetch($query, [$orderId]);

            if ($order) {
                // Get order items
                $itemsQuery = "
                    SELECT
                        oi.*,
                        p.name as product_name,
                        p.sku as product_sku,
                        p.unit as product_unit
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?
                    ORDER BY oi.id ASC
                ";

                $order['items'] = $db->fetchAll($itemsQuery, [$orderId]);

                // Parse delivery address JSON
                if ($order['delivery_address']) {
                    $order['delivery_address_parsed'] = json_decode($order['delivery_address'], true);
                }
            }

            return $order;
        } catch (\Exception $e) {
            error_log("Get order by ID error: " . $e->getMessage());
            return null;
        }
    }

    private function getOrderStats()
    {
        try {
            $db = \App\Core\Database::getInstance();

            $stats = [
                'total_orders' => 0,
                'pending_orders' => 0,
                'delivered_orders' => 0,
                'cancelled_orders' => 0,
                'total_revenue' => 0,
                'self_pickup_orders' => 0,
                'home_delivery_orders' => 0
            ];

            // Get order counts by status
            $statusQuery = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
            $statusResults = $db->fetchAll($statusQuery);

            foreach ($statusResults as $result) {
                $stats['total_orders'] += $result['count'];

                switch ($result['status']) {
                    case 'pending':
                        $stats['pending_orders'] = $result['count'];
                        break;
                    case 'delivered':
                        $stats['delivered_orders'] = $result['count'];
                        break;
                    case 'cancelled':
                        $stats['cancelled_orders'] = $result['count'];
                        break;
                }
            }

            // Get delivery type counts
            $deliveryQuery = "SELECT delivery_type, COUNT(*) as count FROM orders GROUP BY delivery_type";
            $deliveryResults = $db->fetchAll($deliveryQuery);

            foreach ($deliveryResults as $result) {
                if ($result['delivery_type'] === 'self_pickup') {
                    $stats['self_pickup_orders'] = $result['count'];
                } elseif ($result['delivery_type'] === 'home_delivery') {
                    $stats['home_delivery_orders'] = $result['count'];
                }
            }

            // Get total revenue
            $revenueQuery = "SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'";
            $revenueResult = $db->fetch($revenueQuery);
            $stats['total_revenue'] = $revenueResult['total'] ?? 0;

            return $stats;
        } catch (\Exception $e) {
            error_log("Get order stats error: " . $e->getMessage());
            return [
                'total_orders' => 0,
                'pending_orders' => 0,
                'delivered_orders' => 0,
                'cancelled_orders' => 0,
                'total_revenue' => 0,
                'self_pickup_orders' => 0,
                'home_delivery_orders' => 0
            ];
        }
    }
}
?>
