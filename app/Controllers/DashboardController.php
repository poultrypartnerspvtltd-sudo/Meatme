<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Session::flash('error', 'Please login to access your dashboard.');
            $this->redirect('login');
        }
        
        $user = Auth::user();
        $db = Database::getInstance();
        
        // Get user statistics
        $stats = $this->getUserStats($user['id']);
        
        // Get recent orders
        $recentOrders = $this->getRecentOrders($user['id']);
        
        // Get pending orders
        $pendingOrders = $this->getPendingOrders($user['id']);
        
        // Get wishlist count
        $wishlistCount = $this->getWishlistCount($user['id']);
        
        $data = [
            'title' => 'My Dashboard',
            'user' => $user,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'pendingOrders' => $pendingOrders,
            'wishlistCount' => $wishlistCount
        ];
        
        $this->render('dashboard.index', $data);
    }
    
    private function getUserStats($userId)
    {
        $db = Database::getInstance();
        $stats = [
            'total_orders' => 0,
            'total_spent' => 0,
            'pending_orders' => 0,
            'completed_orders' => 0
        ];
        
        try {
            // Total orders
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stats['total_orders'] = $result ? $result['count'] : 0;
            
            // Total spent
            $stmt = $db->prepare("SELECT SUM(total_amount) as total FROM orders WHERE user_id = ? AND status = 'completed'");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stats['total_spent'] = $result && $result['total'] ? $result['total'] : 0;
            
            // Pending orders
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ? AND status IN ('pending', 'processing')");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stats['pending_orders'] = $result ? $result['count'] : 0;
            
            // Completed orders
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ? AND status = 'completed'");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stats['completed_orders'] = $result ? $result['count'] : 0;
            
        } catch (\PDOException $e) {
            Database::logError("Dashboard stats error: " . $e->getMessage(), ['user_id' => $userId]);
            // Return default stats if database error
        } catch (\Exception $e) {
            Database::logError("Dashboard DB error: " . $e->getMessage(), ['user_id' => $userId]);
            // Return default stats if database connection error
        }
        
        return $stats;
    }
    
    private function getRecentOrders($userId, $limit = 5)
    {
        $db = Database::getInstance();
        $orders = [];
        
        try {
            $stmt = $db->prepare("
                SELECT o.*, 
                       DATE_FORMAT(o.created_at, '%M %d, %Y') as formatted_date,
                       DATE_FORMAT(o.created_at, '%h:%i %p') as formatted_time
                FROM orders o 
                WHERE o.user_id = ? 
                ORDER BY o.created_at DESC 
                LIMIT ?
            ");
            $stmt->execute([$userId, $limit]);
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            Database::logError("Recent orders error: " . $e->getMessage(), ['user_id' => $userId, 'limit' => $limit]);
            // Return sample data if orders table doesn't exist yet
            $orders = $this->getSampleOrders($limit);
        }
        
        return $orders;
    }
    
    private function getPendingOrders($userId)
    {
        $db = Database::getInstance();
        $orders = [];
        
        try {
            $stmt = $db->prepare("
                SELECT o.*, 
                       DATE_FORMAT(o.created_at, '%M %d, %Y') as formatted_date
                FROM orders o 
                WHERE o.user_id = ? AND o.status IN ('pending', 'processing', 'shipped') 
                ORDER BY o.created_at DESC
            ");
            $stmt->execute([$userId]);
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            Database::logError("Pending orders error: " . $e->getMessage(), ['user_id' => $userId]);
            // Return sample data if orders table doesn't exist yet
            $orders = $this->getSamplePendingOrders();
        }
        
        return $orders;
    }
    
    private function getWishlistCount($userId)
    {
        $db = Database::getInstance();
        
        try {
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM wishlists WHERE user_id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ? $result['count'] : 0;
            
        } catch (\PDOException $e) {
            Database::logError("Wishlist count error: " . $e->getMessage(), ['user_id' => $userId]);
            return 0;
        }
    }
    
    private function getSampleOrders($limit = 5)
    {
        return [
            [
                'id' => 1,
                'order_number' => 'ORD-2024-001',
                'total_amount' => 1250.00,
                'status' => 'completed',
                'formatted_date' => 'October 20, 2024',
                'formatted_time' => '2:30 PM'
            ],
            [
                'id' => 2,
                'order_number' => 'ORD-2024-002',
                'total_amount' => 850.00,
                'status' => 'processing',
                'formatted_date' => 'October 22, 2024',
                'formatted_time' => '10:15 AM'
            ]
        ];
    }
    
    private function getSamplePendingOrders()
    {
        return [
            [
                'id' => 2,
                'order_number' => 'ORD-2024-002',
                'total_amount' => 850.00,
                'status' => 'processing',
                'formatted_date' => 'October 22, 2024'
            ]
        ];
    }
}
?>
