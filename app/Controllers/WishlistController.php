<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\CSRF;
use App\Core\Database;
use App\Models\User;
use App\Models\Product;

class WishlistController extends Controller
{
    public function index()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Session::flash('error', 'Please login to view your wishlist.');
            $this->redirect('/login');
        }
        
        $user = Auth::user();
        $db = Database::getInstance();
        
        // Get user's wishlist items
        $wishlistItems = [];
        try {
            $stmt = $db->prepare("
                SELECT w.*, p.id as product_id, p.name, p.slug, p.price, p.compare_price, 
                       p.short_description, p.stock_quantity, p.unit, p.is_active,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
                FROM wishlists w 
                JOIN products p ON w.product_id = p.id 
                WHERE w.user_id = ? AND p.is_active = 1
                ORDER BY w.created_at DESC
            ");
            $stmt->execute([$user['id']]);
            $wishlistItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Wishlist fetch error: " . $e->getMessage());
        }
        
        $data = [
            'title' => 'My Wishlist',
            'wishlistItems' => $wishlistItems,
            'user' => $user
        ];
        
        $this->render('wishlist.index', $data);
    }
    
    public function add()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            $this->jsonResponse(['success' => false, 'message' => 'Please login first.']);
        }
        
        // Validate CSRF token
        if (!CSRF::verify($this->input('csrf_token'))) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid security token.']);
        }
        
        $productId = (int)$this->input('product_id');
        $user = Auth::user();
        
        if (!$productId) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid product.']);
        }
        
        $db = Database::getInstance();
        
        try {
            // Check if product exists and is active
            $stmt = $db->prepare("SELECT id, name FROM products WHERE id = ? AND is_active = 1");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$product) {
                $this->jsonResponse(['success' => false, 'message' => 'Product not found.']);
            }
            
            // Check if already in wishlist
            $stmt = $db->prepare("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user['id'], $productId]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                $this->jsonResponse(['success' => false, 'message' => 'Product already in wishlist.']);
            }
            
            // Add to wishlist
            $stmt = $db->prepare("INSERT INTO wishlists (user_id, product_id, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$user['id'], $productId]);
            
            $this->jsonResponse([
                'success' => true, 
                'message' => $product['name'] . ' added to wishlist!'
            ]);
            
        } catch (\PDOException $e) {
            error_log("Wishlist add error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to add to wishlist.']);
        }
    }
    
    public function remove()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            $this->jsonResponse(['success' => false, 'message' => 'Please login first.']);
        }
        
        // Validate CSRF token
        if (!CSRF::verify($this->input('csrf_token'))) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid security token.']);
        }
        
        $productId = (int)$this->input('product_id');
        $user = Auth::user();
        
        if (!$productId) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid product.']);
        }
        
        $db = Database::getInstance();
        
        try {
            // Get product name for response
            $stmt = $db->prepare("
                SELECT p.name 
                FROM products p 
                JOIN wishlists w ON p.id = w.product_id 
                WHERE w.user_id = ? AND w.product_id = ?
            ");
            $stmt->execute([$user['id'], $productId]);
            $product = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$product) {
                $this->jsonResponse(['success' => false, 'message' => 'Product not in wishlist.']);
            }
            
            // Remove from wishlist
            $stmt = $db->prepare("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user['id'], $productId]);
            
            $this->jsonResponse([
                'success' => true, 
                'message' => $product['name'] . ' removed from wishlist!'
            ]);
            
        } catch (\PDOException $e) {
            error_log("Wishlist remove error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to remove from wishlist.']);
        }
    }
    
    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>
