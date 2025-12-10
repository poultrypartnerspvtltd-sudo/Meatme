<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'status', 
        'profile_image', 'date_of_birth', 'gender'
    ];
    protected $hidden = ['password'];
    
    public function getFullName()
    {
        return $this->name;
    }
    
    public function isActive()
    {
        $user = $this->find($this->id);
        return $user && $user['status'] === 'active';
    }
    
    public function isAdmin()
    {
        $user = $this->find($this->id);
        return $user && in_array($user['role'], ['admin', 'super_admin']);
    }
    
    public function orders()
    {
        return $this->db->fetchAll(
            "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC",
            ['user_id' => $this->id]
        );
    }
    
    public function addresses()
    {
        return $this->db->fetchAll(
            "SELECT * FROM user_addresses WHERE user_id = :user_id ORDER BY is_default DESC, created_at DESC",
            ['user_id' => $this->id]
        );
    }
    
    public function defaultAddress()
    {
        return $this->db->fetch(
            "SELECT * FROM user_addresses WHERE user_id = :user_id AND is_default = 1 LIMIT 1",
            ['user_id' => $this->id]
        );
    }
    
    public function wishlist()
    {
        return $this->db->fetchAll(
            "SELECT w.*, p.name, p.slug, p.price, p.compare_price, 
                    (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
             FROM wishlists w 
             JOIN products p ON w.product_id = p.id 
             WHERE w.user_id = :user_id AND p.is_active = 1
             ORDER BY w.created_at DESC",
            ['user_id' => $this->id]
        );
    }
    
    public function reviews()
    {
        return $this->db->fetchAll(
            "SELECT r.*, p.name as product_name, p.slug as product_slug
             FROM reviews r 
             JOIN products p ON r.product_id = p.id 
             WHERE r.user_id = :user_id 
             ORDER BY r.created_at DESC",
            ['user_id' => $this->id]
        );
    }
    
    public function totalSpent()
    {
        $result = $this->db->fetch(
            "SELECT SUM(total_amount) as total FROM orders WHERE user_id = :user_id AND status = 'delivered'",
            ['user_id' => $this->id]
        );
        
        return $result['total'] ?? 0;
    }
    
    public function totalOrders()
    {
        $result = $this->db->fetch(
            "SELECT COUNT(*) as count FROM orders WHERE user_id = :user_id",
            ['user_id' => $this->id]
        );
        
        return $result['count'] ?? 0;
    }
    
    public static function findByEmail($email)
    {
        $instance = new self();
        return $instance->findBy('email', $email);
    }
    
    public static function createUser($data)
    {
        $instance = new self();
        
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return $instance->create($data);
    }
    
    public function updatePassword($newPassword)
    {
        return $this->update($this->id, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
    }
    
    public function verifyPassword($password)
    {
        $user = $this->find($this->id);
        return $user && password_verify($password, $user['password']);
    }
}
?>
