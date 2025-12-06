<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Set page title
$page_title = 'Dashboard';

// Fetch dashboard statistics
function getDashboardStats($mysqli) {
    $stats = [
        'total_products' => 0,
        'total_orders' => 0,
        'total_revenue' => 0,
        'total_users' => 0,
        'recent_orders' => 0,
        'pending_orders' => 0,
        'active_products' => 0,
        'new_users_today' => 0
    ];
    
    // Total Products - with error handling
        $result = $mysqli->query("SELECT COUNT(*) as count FROM products");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_products'] = $row['count'] ?? 0;
        } else {
            error_log("Products count error: " . $mysqli->error);
            $stats['total_products'] = 0;
        }
        
        // Active Products - with error handling
        $result = $mysqli->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['active_products'] = $row['count'] ?? 0;
        } else {
            error_log("Active products count error: " . $mysqli->error);
            $stats['active_products'] = 0;
        }
        
        // Total Users - with error handling
        $result = $mysqli->query("SELECT COUNT(*) as count FROM users");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_users'] = $row['count'] ?? 0;
        } else {
            error_log("Users count error: " . $mysqli->error);
            $stats['total_users'] = 0;
        }
        
        // New Users Today - with error handling
        $result = $mysqli->query("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['new_users_today'] = $row['count'] ?? 0;
        } else {
            error_log("New users today error: " . $mysqli->error);
            $stats['new_users_today'] = 0;
        }
        
        // Check if orders table exists
        $result = $mysqli->query("SHOW TABLES LIKE 'orders'");
        if ($result && $result->num_rows > 0) {
            // Total Orders
            $result = $mysqli->query("SELECT COUNT(*) as count FROM orders");
            if ($result) {
                $row = $result->fetch_assoc();
                $stats['total_orders'] = $row['count'] ?? 0;
            } else {
                error_log("Orders count error: " . $mysqli->error);
                $stats['total_orders'] = 0;
            }
            
            // Total Revenue
            $result = $mysqli->query("SELECT SUM(total_amount) as revenue FROM orders WHERE status = 'completed'");
            if ($result) {
                $row = $result->fetch_assoc();
                $stats['total_revenue'] = $row['revenue'] ?? 0;
            } else {
                error_log("Revenue calculation error: " . $mysqli->error);
                $stats['total_revenue'] = 0;
            }
            
            // Recent Orders (last 7 days)
            $result = $mysqli->query("SELECT COUNT(*) as count FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
            if ($result) {
                $row = $result->fetch_assoc();
                $stats['recent_orders'] = $row['count'] ?? 0;
            } else {
                error_log("Recent orders error: " . $mysqli->error);
                $stats['recent_orders'] = 0;
            }
            
            // Pending Orders
            $result = $mysqli->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
            if ($result) {
                $row = $result->fetch_assoc();
                $stats['pending_orders'] = $row['count'] ?? 0;
            } else {
                error_log("Pending orders error: " . $mysqli->error);
                $stats['pending_orders'] = 0;
            }
        }
    
    return $stats;
}

// Get recent activities
function getRecentActivities($mysqli) {
    $activities = [];
    
    // Recent users
    $result = $mysqli->query("SELECT name, email, created_at FROM users ORDER BY created_at DESC LIMIT 3");
    if ($result) {
        while ($user = $result->fetch_assoc()) {
            $activities[] = [
                'type' => 'user',
                'icon' => 'fas fa-user',
                'color' => 'success',
                'message' => "New user registered: " . htmlspecialchars($user['name']),
                'time' => $user['created_at']
            ];
        }
    }
    
    // Recent products
    $result = $mysqli->query("SELECT name, created_at FROM products ORDER BY created_at DESC LIMIT 2");
    if ($result) {
        while ($product = $result->fetch_assoc()) {
            $activities[] = [
                'type' => 'product',
                'icon' => 'fas fa-box',
                'color' => 'primary',
                'message' => "Product added: " . htmlspecialchars($product['name']),
                'time' => $product['created_at']
            ];
        }
    }
    
    // Sort by time
    usort($activities, function($a, $b) {
        return strtotime($b['time']) - strtotime($a['time']);
    });
    
    return array_slice($activities, 0, 5);
}

$stats = getDashboardStats($mysqli);
$recent_activities = getRecentActivities($mysqli);

// Include header and sidebar
include 'includes/header.php';
include 'includes/sidebar.phtml';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-2">Welcome back, <?= htmlspecialchars($_SESSION['admin_username']) ?>!</h2>
            <p class="text-muted mb-0">Here's what's happening with your store today.</p>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="refreshDashboard()">
                    <i class="fas fa-sync-alt me-2"></i>Refresh
                </button>
                <a href="/Meatme/" class="btn btn-success" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>View Website
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <div class="stat-icon bg-primary mx-auto">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="fw-bold mb-1"><?= e(number_format($stats['total_users'])) ?></h3>
                <p class="text-muted mb-2">Total Users</p>
                <small class="text-success">
                    <i class="fas fa-arrow-up"></i> +<?= e($stats['new_users_today']) ?> today
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <div class="stat-icon bg-success mx-auto">
                    <i class="fas fa-box"></i>
                </div>
                <h3 class="fw-bold mb-1"><?= e(number_format($stats['total_products'])) ?></h3>
                <p class="text-muted mb-2">Total Products</p>
                <small class="text-info">
                    <?= e($stats['active_products']) ?> active
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <div class="stat-icon bg-warning mx-auto">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="fw-bold mb-1"><?= e(number_format($stats['total_orders'])) ?></h3>
                <p class="text-muted mb-2">Total Orders</p>
                <small class="text-warning">
                    <?= e($stats['pending_orders']) ?> pending
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <div class="stat-icon bg-info mx-auto">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <h3 class="fw-bold mb-1">Rs. <?= e(number_format($stats['total_revenue'])) ?></h3>
                <p class="text-muted mb-2">Total Revenue</p>
                <small class="text-success">
                    <i class="fas fa-arrow-up"></i> This month
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Quick Actions -->
<div class="row">
    <!-- Sales Chart -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Sales Overview</h5>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-mdb-toggle="dropdown">
                        Last 7 days
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Last 7 days</a></li>
                        <li><a class="dropdown-item" href="#">Last 30 days</a></li>
                        <li><a class="dropdown-item" href="#">Last 3 months</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="products.php?action=add" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Add New Product
                    </a>
                    <a href="orders.php" class="btn btn-warning">
                        <i class="fas fa-shopping-cart me-2"></i>View Orders
                        <?php if ($stats['pending_orders'] > 0): ?>
                            <span class="badge bg-danger ms-2"><?= e($stats['pending_orders']) ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="users.php" class="btn btn-info">
                        <i class="fas fa-users me-2"></i>Manage Users
                    </a>
                    <a href="coupons.php" class="btn btn-primary">
                        <i class="fas fa-ticket-alt me-2"></i>Create Coupon
                    </a>
                </div>
                
                <hr class="my-4">
                
                <!-- System Status -->
                <h6 class="mb-3">System Status</h6>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Database</span>
                    <span class="badge bg-success">Connected</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Storage</span>
                    <span class="badge bg-success">Available</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Cache</span>
                    <span class="badge bg-success">Active</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Backup</span>
                    <span class="badge bg-warning">Pending</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Activity</h5>
                <a href="#" class="btn btn-outline-primary btn-sm">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($recent_activities)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No recent activity to display.</p>
                    </div>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-<?= e($activity['color']) ?> rounded-circle p-2 me-3">
                                    <i class="<?= e($activity['icon']) ?> text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-0"><?= e($activity['message']) ?></p>
                                    <small class="text-muted"><?= e(date('M j, Y g:i A', strtotime($activity['time']))) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

        </div> <!-- End container-fluid -->
    </div> <!-- End main-content -->

<!-- MDBootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

<script>
// Sales Chart
const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'Sales (Rs.)',
            data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
            borderColor: '#4caf50',
            backgroundColor: 'rgba(76, 175, 80, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rs. ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Refresh Dashboard
function refreshDashboard() {
    location.reload();
}

// Auto-refresh every 5 minutes
setInterval(function() {
    // You can implement AJAX refresh here
    console.log('Auto-refreshing dashboard data...');
}, 300000);
</script>

</body>
</html>
