<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Search products API endpoint
     */
    public function search()
    {
        $query = $_GET['q'] ?? '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

        // Log the incoming request for debugging
        error_log("Search API called with query: '$query', limit: $limit");

        if (empty($query)) {
            return $this->json([
                'success' => false,
                'message' => 'Query parameter is required',
                'products' => []
            ], 400);
        }

        try {
            // Check if Product class exists
            if (!class_exists('App\\Models\\Product')) {
                throw new \Exception('Product model class not found');
            }

            // Check if search method exists
            if (!method_exists('App\\Models\\Product', 'search')) {
                throw new \Exception('Product::search method not found');
            }

            $products = Product::search($query, null, $limit, 0);

            error_log("Search returned " . count($products) . " products");

            // Format products for API response
            $formattedProducts = [];
            foreach ($products as $product) {
                $formattedProducts[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'slug' => $product['slug'],
                    'price' => $product['price'],
                    'image' => $product['image'] ?? null,
                    'url' => \App\Core\View::url('products/' . $product['slug'])
                ];
            }

            return $this->json([
                'success' => true,
                'products' => $formattedProducts
            ]);
        } catch (\Exception $e) {
            error_log("Product search API error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return $this->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage(),
                'products' => []
            ], 500);
        }
    }
    
    /**
     * Get product suggestions for autocomplete
     */
    public function suggestions()
    {
        $query = $_GET['q'] ?? '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
        
        if (empty($query)) {
            return $this->json([
                'success' => true,
                'suggestions' => []
            ]);
        }
        
        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $searchTerm = '%' . $query . '%';
            
            $stmt = $db->prepare("
                SELECT p.id, p.name, p.slug, p.price,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image_path
                FROM products p
                WHERE (p.name LIKE :search OR p.description LIKE :search OR p.short_description LIKE :search)
                AND p.is_active = 1
                ORDER BY p.name ASC
                LIMIT :limit
            ");
            
            $stmt->bindValue(':search', $searchTerm, \PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            
            $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Format suggestions
            $suggestions = [];
            foreach ($products as $product) {
                $imageUrl = null;
                if ($product['image_path']) {
                    $imageUrl = \App\Core\View::asset($product['image_path']);
                }
                
                $suggestions[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'slug' => $product['slug'],
                    'price' => $product['price'],
                    'url' => \App\Core\View::url('products/' . $product['slug']),
                    'image' => $imageUrl
                ];
            }
            
            return $this->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);
        } catch (\Exception $e) {
            error_log("Product suggestions API error: " . $e->getMessage());
            return $this->json([
                'success' => true,
                'suggestions' => []
            ]);
        }
    }
}

