<?php

namespace App\Models;

use App\Core\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'order_number', 'user_id', 'status', 'payment_status', 'payment_method', 'payment_id',
        'subtotal', 'delivery_fee', 'discount_amount', 'total_amount', 'currency',
        'delivery_type', 'delivery_slot', 'delivery_date', 'delivery_address', 'notes', 'coupon_code',
        'estimated_delivery', 'delivered_at', 'cancelled_at', 'cancellation_reason'
    ];
    
    public function user()
    {
        return $this->db->fetch(
            "SELECT * FROM users WHERE id = :id LIMIT 1",
            ['id' => $this->user_id]
        );
    }
    
    public function items()
    {
        return $this->db->fetchAll(
            "SELECT oi.*, p.slug as product_slug, p.stock_quantity,
                    (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as product_image
             FROM order_items oi 
             LEFT JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = :order_id 
             ORDER BY oi.id ASC",
            ['order_id' => $this->id]
        );
    }
    
    public function itemCount()
    {
        $result = $this->db->fetch(
            "SELECT COUNT(*) as count FROM order_items WHERE order_id = :order_id",
            ['order_id' => $this->id]
        );
        
        return $result['count'] ?? 0;
    }
    
    public function totalQuantity()
    {
        $result = $this->db->fetch(
            "SELECT SUM(quantity) as total FROM order_items WHERE order_id = :order_id",
            ['order_id' => $this->id]
        );
        
        return $result['total'] ?? 0;
    }
    
    public function canCancel()
    {
        $order = $this->find($this->id);
        return $order && in_array($order['status'], ['pending', 'confirmed']);
    }
    
    public function canRefund()
    {
        $order = $this->find($this->id);
        return $order && $order['status'] === 'delivered' && $order['payment_status'] === 'paid';
    }
    
    public function updateStatus($status, $notes = null)
    {
        $data = ['status' => $status];
        
        if ($status === 'delivered') {
            $data['delivered_at'] = date('Y-m-d H:i:s');
        } elseif ($status === 'cancelled') {
            $data['cancelled_at'] = date('Y-m-d H:i:s');
            if ($notes) {
                $data['cancellation_reason'] = $notes;
            }
        }
        
        return $this->update($this->id, $data);
    }
    
    public function updatePaymentStatus($status, $paymentId = null)
    {
        $data = ['payment_status' => $status];
        
        if ($paymentId) {
            $data['payment_id'] = $paymentId;
        }
        
        return $this->update($this->id, $data);
    }
    
    public static function generateOrderNumber()
    {
        return 'MM' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
    
    public static function createOrder($data)
    {
        $instance = new self();
        
        // Generate unique order number
        do {
            $orderNumber = self::generateOrderNumber();
            $existing = $instance->findBy('order_number', $orderNumber);
        } while ($existing);
        
        $data['order_number'] = $orderNumber;
        
        return $instance->create($data);
    }
    
    public static function findByOrderNumber($orderNumber)
    {
        $instance = new self();
        return $instance->findBy('order_number', $orderNumber);
    }
    
    public static function userOrders($userId, $limit = 10, $offset = 0)
    {
        $instance = new self();
        return $instance->db->fetchAll(
            "SELECT o.*, 
                    (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count,
                    (SELECT SUM(quantity) FROM order_items WHERE order_id = o.id) as total_quantity
             FROM orders o 
             WHERE o.user_id = :user_id 
             ORDER BY o.created_at DESC 
             LIMIT :limit OFFSET :offset",
            ['user_id' => $userId, 'limit' => $limit, 'offset' => $offset]
        );
    }
    
    public static function recentOrders($limit = 10)
    {
        $instance = new self();
        return $instance->db->fetchAll(
            "SELECT o.*, u.name as user_name, u.email as user_email,
                    (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
             FROM orders o 
             JOIN users u ON o.user_id = u.id 
             ORDER BY o.created_at DESC 
             LIMIT :limit",
            ['limit' => $limit]
        );
    }
    
    public static function salesReport($startDate, $endDate)
    {
        $instance = new self();
        return $instance->db->fetchAll(
            "SELECT 
                DATE(created_at) as date,
                COUNT(*) as order_count,
                SUM(total_amount) as total_sales,
                AVG(total_amount) as average_order_value
             FROM orders 
             WHERE created_at BETWEEN :start_date AND :end_date 
             AND status != 'cancelled'
             GROUP BY DATE(created_at) 
             ORDER BY date DESC",
            ['start_date' => $startDate, 'end_date' => $endDate]
        );
    }
}
?>
