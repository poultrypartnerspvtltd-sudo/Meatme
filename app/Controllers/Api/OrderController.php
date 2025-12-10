<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;

class OrderController extends Controller
{
    /**
     * Get user's order history (API endpoint)
     * GET /api/orders
     */
    public function index()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // Always return JSON, even for unauthenticated requests
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Authentication required'
            ]);
            exit;
        }
        
        $user = Auth::user();
        $db = Database::getInstance()->getConnection();
        
        try {
            // Get user's orders
            $stmt = $db->prepare("
                SELECT 
                    o.id,
                    o.order_number,
                    o.total_amount,
                    o.status,
                    o.payment_status,
                    o.shipping_type,
                    o.created_at,
                    COUNT(oi.id) as item_count
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.user_id = :user_id
                GROUP BY o.id
                ORDER BY o.created_at DESC
            ");
            
            $stmt->bindValue(':user_id', $user['id'], \PDO::PARAM_INT);
            $stmt->execute();
            
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Format orders for API response
            $formattedOrders = [];
            foreach ($orders as $order) {
                $formattedOrders[] = [
                    'id' => (int)$order['id'],
                    'order_number' => $order['order_number'],
                    'total_amount' => (float)$order['total_amount'],
                    'status' => $order['status'],
                    'payment_status' => $order['payment_status'],
                    'shipping_type' => $order['shipping_type'],
                    'item_count' => (int)$order['item_count'],
                    'created_at' => $order['created_at'],
                    'url' => \App\Core\View::url('orders/' . $order['id'])
                ];
            }
            
            return $this->json([
                'success' => true,
                'orders' => $formattedOrders,
                'count' => count($formattedOrders)
            ]);
            
        } catch (\Exception $e) {
            error_log("Orders API error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Failed to retrieve orders',
                'orders' => []
            ], 500);
        }
    }
    
    /**
     * Get order details by ID (API endpoint)
     * GET /api/orders/{id}
     */
    public function show($id)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return $this->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }
        
        $user = Auth::user();
        $db = Database::getInstance()->getConnection();
        
        try {
            // Get order details
            $stmt = $db->prepare("
                SELECT 
                    o.*,
                    oi.id as item_id,
                    oi.product_id,
                    oi.product_name,
                    oi.quantity,
                    oi.unit_price,
                    oi.total_price,
                    p.slug as product_slug
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE o.id = :order_id AND o.user_id = :user_id
                ORDER BY oi.id ASC
            ");
            
            $stmt->bindValue(':order_id', $id, \PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user['id'], \PDO::PARAM_INT);
            $stmt->execute();
            
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            if (empty($rows)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }
            
            // Build order object
            $order = [
                'id' => (int)$rows[0]['id'],
                'order_number' => $rows[0]['order_number'],
                'total_amount' => (float)$rows[0]['total_amount'],
                'subtotal' => (float)$rows[0]['subtotal'],
                'delivery_fee' => (float)$rows[0]['delivery_fee'],
                'status' => $rows[0]['status'],
                'payment_status' => $rows[0]['payment_status'],
                'payment_method' => $rows[0]['payment_method'],
                'shipping_type' => $rows[0]['shipping_type'],
                'delivery_address' => json_decode($rows[0]['delivery_address'], true),
                'notes' => $rows[0]['notes'],
                'created_at' => $rows[0]['created_at'],
                'updated_at' => $rows[0]['updated_at'],
                'items' => []
            ];
            
            // Add order items
            foreach ($rows as $row) {
                if ($row['item_id']) {
                    $order['items'][] = [
                        'id' => (int)$row['item_id'],
                        'product_id' => (int)$row['product_id'],
                        'product_name' => $row['product_name'],
                        'product_slug' => $row['product_slug'],
                        'quantity' => (float)$row['quantity'],
                        'unit_price' => (float)$row['unit_price'],
                        'total_price' => (float)$row['total_price'],
                        'product_url' => $row['product_slug'] ? \App\Core\View::url('products/' . $row['product_slug']) : null
                    ];
                }
            }
            
            return $this->json([
                'success' => true,
                'order' => $order
            ]);
            
        } catch (\Exception $e) {
            error_log("Order details API error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Failed to retrieve order details'
            ], 500);
        }
    }
}

