<?php

namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'name', 'slug', 'description', 'image', 'parent_id', 'sort_order', 'is_active'
    ];
    
    public function products($limit = null)
    {
        $sql = "SELECT p.*, 
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
                FROM products p 
                WHERE p.category_id = :category_id AND p.is_active = 1 
                ORDER BY p.name ASC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
            return $this->db->fetchAll($sql, ['category_id' => $this->id, 'limit' => $limit]);
        }
        
        return $this->db->fetchAll($sql, ['category_id' => $this->id]);
    }
    
    public function productCount()
    {
        $result = $this->db->fetch(
            "SELECT COUNT(*) as count FROM products WHERE category_id = :category_id AND is_active = 1",
            ['category_id' => $this->id]
        );
        
        return $result['count'] ?? 0;
    }
    
    public function parent()
    {
        if (!$this->parent_id) return null;
        
        return $this->db->fetch(
            "SELECT * FROM categories WHERE id = :id LIMIT 1",
            ['id' => $this->parent_id]
        );
    }
    
    public function children()
    {
        return $this->db->fetchAll(
            "SELECT * FROM categories WHERE parent_id = :parent_id ORDER BY name ASC",
            ['parent_id' => $this->id]
        );
    }
    
    public static function active($parentId = null)
    {
        $instance = new self();
        
        if ($parentId !== null) {
            return $instance->db->fetchAll(
                "SELECT * FROM categories WHERE parent_id = :parent_id ORDER BY name ASC",
                ['parent_id' => $parentId]
            );
        }
        
        return $instance->db->fetchAll(
            "SELECT * FROM categories ORDER BY name ASC"
        );
    }
    
    public static function mainCategories()
    {
        $instance = new self();
        return $instance->db->fetchAll(
            "SELECT c.*, 
                    (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) as product_count
             FROM categories c 
             WHERE c.parent_id IS NULL 
             ORDER BY c.name ASC"
        );
    }
    
    public static function findBySlug($slug)
    {
        $instance = new self();
        return $instance->findBy('slug', $slug);
    }
    
    public static function withProductCounts()
    {
        $instance = new self();
        return $instance->db->fetchAll(
            "SELECT c.*, 
                    (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) as product_count
             FROM categories c 
             ORDER BY c.name ASC"
        );
    }
}
?>
