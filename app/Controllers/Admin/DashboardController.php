<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // Check if user is admin
        if (!Auth::check() || !Auth::isAdmin()) {
            $this->redirect('/admin/login');
        }
    }

    public function index()
    {
        // Get dashboard statistics
        $stats = $this->getDashboardStats();

        $data = [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'user' => Auth::user()
        ];

        return $this->render('admin.dashboard', $data);
    }

    public function analytics()
    {
        // Get detailed analytics
        $analytics = $this->getAnalyticsData();

        $data = [
            'title' => 'Analytics',
            'analytics' => $analytics,
            'user' => Auth::user()
        ];

        return $this->render('admin.analytics', $data);
    }

    private function getDashboardStats()
    {
        try {
            $totalUsers = (new User())->count();
            $totalProducts = (new Product())->count();
            $totalCategories = (new Category())->count();

            // Recent users (created in last 7 days)
            $recentUsers = (new User())->count(['created_at' => date('Y-m-d', strtotime('-7 days'))]);

            $activeProducts = (new Product())->count(['is_active' => 1]);

            return [
                'total_users' => $totalUsers,
                'total_products' => $totalProducts,
                'total_categories' => $totalCategories,
                'recent_users' => $recentUsers,
                'active_products' => $activeProducts,
                'total_orders' => 0,
                'total_revenue' => 0,
                'pending_orders' => 0
            ];
        } catch (\Exception $e) {
            return [
                'total_users' => 0,
                'total_products' => 0,
                'total_categories' => 0,
                'recent_users' => 0,
                'active_products' => 0,
                'total_orders' => 0,
                'total_revenue' => 0,
                'pending_orders' => 0
            ];
        }
    }

    private function getAnalyticsData()
    {
        return [
            'daily_sales' => [],
            'top_products' => [],
            'user_growth' => [],
            'revenue_trend' => []
        ];
    }
}
?>

