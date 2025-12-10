<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cart = Session::getCart();
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
        
        // Calculate delivery fee (no tax)
        $deliveryFee = $subtotal >= 1500 ? 0 : 10; // Standard delivery Rs. 10, free above Rs. 1500
        $total = $subtotal + $deliveryFee;
        
        $data = [
            'title' => 'Shopping Cart',
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
            'total' => $total,
            'cartCount' => Session::getCartCount()
        ];
        
        $this->render('cart.index', $data);
    }
    
    public function add()
    {
        $productId = (int)$this->input('product_id');
        $quantity = (float)$this->input('quantity', 1);
        $name = $this->input('name');
        $price = (float)$this->input('price');

        if (!$productId) {
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'message' => 'Invalid product']);
            } else {
                Session::flash('error', 'Invalid product');
                $this->redirect('products');
            }
        }

        // For form submissions, get product data from database
        if (!$name || !$price) {
            $product = (new Product())->find($productId);
            if (!$product || !$product['is_active']) {
                if ($this->isAjaxRequest()) {
                    $this->json(['success' => false, 'message' => 'Product not found']);
                } else {
                    Session::flash('error', 'Product not found');
                    $this->redirect('products');
                }
            }
            $name = $product['name'];
            $price = $product['price'];
        }

        if ($quantity <= 0) {
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'message' => 'Invalid quantity']);
            } else {
                Session::flash('error', 'Invalid quantity');
                $this->back();
            }
        }

        // Check stock and quantity limits (minimum is now 1)
        $productModel = new Product();
        $productModel->id = $productId;

        $minQuantity = 1; // Override to use 1 as minimum
        if ($quantity < $minQuantity) {
            $quantity = $minQuantity; // Force minimum quantity
        }

        // Add to cart
        Session::addToCart($productId, $quantity, $price);

        if ($this->isAjaxRequest()) {
            $this->json([
                'success' => true,
                'message' => 'Product added to cart',
                'cartCount' => Session::getCartCount()
            ]);
        } else {
            // Redirect to cart page with success message
            Session::flash('success', 'Product added to cart successfully!');
            $this->redirect('cart');
        }
    }
    
    public function update()
    {
        if (!$this->isAjaxRequest()) {
            $this->redirect('cart');
        }

        $productId = (int)$this->input('product_id');
        $quantity = (float)$this->input('quantity');

        if (!$productId) {
            return $this->json(['success' => false, 'message' => 'Invalid product'], 400);
        }

        $cart = Session::getCart();

        // If product not in cart, return error
        if (!isset($cart[$productId])) {
            return $this->json(['success' => false, 'message' => 'Product not found in cart'], 404);
        }

        // Handle remove if quantity is 0 or negative
        if ($quantity <= 0) {
            Session::removeFromCart($productId);
            return $this->json([
                'success' => true,
                'message' => '✅ Product removed from cart.',
                'cartCount' => Session::getCartCount(),
                'subtotal' => $this->calculateSubtotal(),
                'deliveryFee' => $this->calculateDeliveryFee(),
                'total' => $this->calculateTotal()
            ]);
        }

        $product = (new Product())->find($productId);

        if (!$product || !$product['is_active']) {
            Session::removeFromCart($productId);
            return $this->json([
                'success' => false,
                'message' => 'Product no longer available',
                'redirect' => url('cart')
            ]);
        }

        // Check quantity limits
        $minQuantity = $product['min_quantity'] ?? 0.5;
        $maxQuantity = min($product['max_quantity'] ?? 100, $product['stock_quantity'] ?? 100);

        if ($quantity < $minQuantity || $quantity > $maxQuantity) {
            return $this->json([
                'success' => false,
                'message' => "Quantity must be between {$minQuantity} and {$maxQuantity} {$product['unit']}",
                'maxQuantity' => $maxQuantity
            ]);
        }

        // Update cart with new quantity
        Session::updateCartItem($productId, $quantity);

        // Return updated cart totals
        $this->json([
            'success' => true,
            'message' => 'Cart updated',
            'cartCount' => Session::getCartCount(),
            'subtotal' => $this->calculateSubtotal(),
            'deliveryFee' => $this->calculateDeliveryFee(),
            'total' => $this->calculateTotal()
        ]);
    }
    
    public function remove()
    {
        $productId = (int)$this->input('product_id');
        
        if (!$productId) {
            if ($this->isAjaxRequest()) {
                return $this->json(['success' => false, 'message' => 'Invalid product'], 400);
            }
            Session::flash('error', 'Invalid product');
            $this->redirect('cart');
            return;
        }
        
        Session::removeFromCart($productId);
        
        if ($this->isAjaxRequest()) {
            return $this->json([
                'success' => true,
                'message' => '✅ Product removed from cart.',
                'cartCount' => Session::getCartCount(),
                'subtotal' => $this->calculateSubtotal(),
                'deliveryFee' => $this->calculateDeliveryFee(),
                'total' => $this->calculateTotal()
            ]);
        }
        
        Session::flash('success', '✅ Product removed from cart.');
        $this->redirect('cart');
    }
    
    /**
     * Clear the shopping cart
     */
    public function clear()
    {
        Session::clearCart();
        
        if ($this->isAjaxRequest()) {
            return $this->json([
                'success' => true,
                'message' => '🧺 Your cart has been cleared.',
                'cartCount' => 0,
                'subtotal' => 0,
                'deliveryFee' => 0,
                'total' => 0
            ]);
        }
        
        Session::flash('success', '🧺 Your cart has been cleared.');
        $this->redirect('cart');
    }
    
    /**
     * Calculate cart subtotal
     */
    private function calculateSubtotal()
    {
        $cart = Session::getCart();
        $subtotal = 0;
        
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        return $subtotal;
    }
    
    /**
     * Calculate delivery fee
     */
    private function calculateDeliveryFee($subtotal = null)
    {
        if ($subtotal === null) {
            $subtotal = $this->calculateSubtotal();
        }
        return $subtotal >= 1500 ? 0 : 10; // Standard delivery Rs. 10, free above Rs. 1500
    }
    
    
    /**
     * Calculate total amount (no VAT/tax)
     */
    private function calculateTotal($subtotal = null, $deliveryFee = null)
    {
        if ($subtotal === null) {
            $subtotal = $this->calculateSubtotal();
        }
        if ($deliveryFee === null) {
            $deliveryFee = $this->calculateDeliveryFee($subtotal);
        }

        return $subtotal + $deliveryFee;
    }
}
?>
