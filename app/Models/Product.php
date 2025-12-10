<?php

namespace App\Models;

use App\Core\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'name', 'slug', 'description', 'short_description', 'sku', 'category_id',
        'price', 'compare_price', 'cost_price', 'stock_quantity', 'min_quantity', 
        'max_quantity', 'unit', 'weight', 'dimensions', 'is_featured', 'is_active',
        'meta_title', 'meta_description', 'nutritional_info', 'processing_time',
        'freshness_indicator', 'farm_source'
    ];
    
    public function category()
    {
        // Check if category_id property exists and is not null
        if (!isset($this->category_id) || $this->category_id === null) {
            return null;
        }
        
        return $this->db->fetch(
            "SELECT * FROM categories WHERE id = :id LIMIT 1",
            ['id' => $this->category_id]
        );
    }
    
    public function images()
    {
        return $this->db->fetchAll(
            "SELECT * FROM product_images WHERE product_id = :product_id ORDER BY is_primary DESC, sort_order ASC",
            ['product_id' => $this->id]
        );
    }
    
    public function primaryImage()
    {
        $image = $this->db->fetch(
            "SELECT * FROM product_images WHERE product_id = :product_id AND is_primary = 1 LIMIT 1",
            ['product_id' => $this->id]
        );
        
        if (!$image) {
            $image = $this->db->fetch(
                "SELECT * FROM product_images WHERE product_id = :product_id ORDER BY sort_order ASC LIMIT 1",
                ['product_id' => $this->id]
            );
        }
        
        return $image;
    }
    
    public function reviews()
    {
        return $this->db->fetchAll(
            "SELECT r.*, u.name as user_name 
             FROM reviews r 
             JOIN users u ON r.user_id = u.id 
             WHERE r.product_id = :product_id AND r.is_approved = 1 
             ORDER BY r.created_at DESC",
            ['product_id' => $this->id]
        );
    }
    
    public function averageRating()
    {
        $result = $this->db->fetch(
            "SELECT AVG(rating) as average, COUNT(*) as count 
             FROM reviews 
             WHERE product_id = :product_id AND is_approved = 1",
            ['product_id' => $this->id]
        );
        
        return [
            'average' => round($result['average'] ?? 0, 1),
            'count' => $result['count'] ?? 0
        ];
    }
    
    public function isInStock()
    {
        $product = $this->find($this->id);
        return $product && $product['stock_quantity'] > 0;
    }
    
    public function canOrder($quantity)
    {
        $product = $this->find($this->id);
        if (!$product) return false;
        
        return $quantity >= $product['min_quantity'] && 
               $quantity <= $product['max_quantity'] && 
               $quantity <= $product['stock_quantity'];
    }
    
    public function reduceStock($quantity)
    {
        return $this->db->query(
            "UPDATE products SET stock_quantity = stock_quantity - :quantity WHERE id = :id",
            ['quantity' => $quantity, 'id' => $this->id]
        );
    }
    
    public function increaseStock($quantity)
    {
        return $this->db->query(
            "UPDATE products SET stock_quantity = stock_quantity + :quantity WHERE id = :id",
            ['quantity' => $quantity, 'id' => $this->id]
        );
    }
    
    public static function featured($limit = 6)
    {
        $instance = new self();
        return $instance->db->fetchAll(
            "SELECT p.*, c.name as category_name,
                    (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.is_featured = 1 AND p.is_active = 1 
             ORDER BY p.created_at DESC 
             LIMIT :limit",
            ['limit' => $limit]
        );
    }
    
    /**
     * Search products by query and optional category filter
     *
     * @param string $query Search query
     * @param int|null $categoryId Optional category ID to filter by
     * @param int $limit Number of results to return
     * @param int $offset Offset for pagination
     * @return array Array of matching products with additional data
     */
    public static function search($query, $categoryId = null, $limit = 20, $offset = 0)
    {
        $instance = new self();
        $query = trim(strip_tags($query)); // Sanitize input

        error_log("Product::search called with query: '$query', categoryId: " . ($categoryId ?? 'null') . ", limit: $limit, offset: $offset");

        if (empty($query)) {
            error_log("Empty query provided to search");
            return [];
        }

        try {
            // Simple and robust search implementation
            $searchTerm = '%' . $query . '%';

            $sql = "SELECT p.*,
                           c.name as category_name,
                           c.slug as category_slug,
                           (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.is_active = 1
                      AND (p.name LIKE :search
                           OR p.description LIKE :search
                           OR p.short_description LIKE :search)";

            $params = ['search' => $searchTerm];

            // Add category filter if provided
            if ($categoryId) {
                $sql .= " AND p.category_id = :category_id";
                $params['category_id'] = (int)$categoryId;
            }

            $sql .= " ORDER BY p.name ASC LIMIT :limit OFFSET :offset";
            $params['limit'] = (int)$limit;
            $params['offset'] = (int)$offset;

            error_log("Executing search SQL: " . $sql);
            error_log("Search params: " . json_encode($params));

            $results = $instance->db->fetchAll($sql, $params);
            error_log("Search returned " . count($results) . " results");

            return $results;

        } catch (\Exception $e) {
            error_log("Product::search error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            // Return empty array instead of throwing to prevent fatal errors
            return [];
        }
    }
    
    public static function findBySlug($slug)
    {
        $instance = new self();
        return $instance->findBy('slug', $slug);
    }
    
    public static function byCategory($categoryId, $limit = 20, $offset = 0)
    {
        $instance = new self();
        return $instance->db->fetchAll(
            "SELECT p.*, c.name as category_name,
                    (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.category_id = :category_id AND p.is_active = 1 
             ORDER BY p.name ASC 
             LIMIT :limit OFFSET :offset",
            ['category_id' => $categoryId, 'limit' => $limit, 'offset' => $offset]
        );
    }
    
    public static function related($productId, $categoryId, $limit = 4)
    {
        $instance = new self();
        return $instance->db->fetchAll(
            "SELECT p.*, c.name as category_name,
                    (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.category_id = :category_id AND p.id != :product_id AND p.is_active = 1 
             ORDER BY RAND() 
             LIMIT :limit",
            ['category_id' => $categoryId, 'product_id' => $productId, 'limit' => $limit]
        );
    }
}
?>
