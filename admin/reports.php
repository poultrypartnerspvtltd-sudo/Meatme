<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$page_title = 'Reports & Analytics';

// Fetch real data from database
global $mysqli;

// Total completed orders
$result = $mysqli->query("SELECT COUNT(*) as total FROM orders WHERE status = 'completed'");
$row = $result->fetch_assoc();
$total_orders = $row['total'] ?? 0;

// Total revenue from completed orders
$result = $mysqli->query("SELECT SUM(total_amount) as revenue FROM orders WHERE status = 'completed'");
$row = $result->fetch_assoc();
$total_revenue = $row['revenue'] ?? 0;

// This month's orders and revenue
$result = $mysqli->query("
    SELECT COUNT(*) as count, SUM(total_amount) as revenue 
    FROM orders 
    WHERE status = 'completed' 
    AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
    AND YEAR(created_at) = YEAR(CURRENT_DATE())
");
$month_data = $result->fetch_assoc();
$month_orders = $month_data['count'] ?? 0;
$month_revenue = $month_data['revenue'] ?? 0;

// Last month's orders and revenue (for growth comparison)
$result = $mysqli->query("
    SELECT COUNT(*) as count, SUM(total_amount) as revenue 
    FROM orders 
    WHERE status = 'completed' 
    AND MONTH(created_at) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) 
    AND YEAR(created_at) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)
");
$last_month_data = $result->fetch_assoc();
$last_month_orders = $last_month_data['count'] ?? 0;
$last_month_revenue = $last_month_data['revenue'] ?? 0;

// Calculate growth percentages
$revenue_growth = ($last_month_revenue > 0) ? (($month_revenue - $last_month_revenue) / $last_month_revenue) * 100 : 0;
$orders_growth = ($last_month_orders > 0) ? (($month_orders - $last_month_orders) / $last_month_orders) * 100 : 0;

// New customers this month (assuming users table exists)
$result = $mysqli->query("
    SELECT COUNT(*) as count 
    FROM users 
    WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
    AND YEAR(created_at) = YEAR(CURRENT_DATE())
");
$row = $result->fetch_assoc();
$new_customers = $row['count'] ?? 0;

// Last month's new customers
$result = $mysqli->query("
    SELECT COUNT(*) as count 
    FROM users 
    WHERE MONTH(created_at) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) 
    AND YEAR(created_at) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)
");
$row = $result->fetch_assoc();
$last_month_customers = $row['count'] ?? 0;

$customers_growth = ($last_month_customers > 0) ? (($new_customers - $last_month_customers) / $last_month_customers) * 100 : 0;

// Average order value
$result = $mysqli->query("SELECT AVG(total_amount) as avg_order FROM orders WHERE status = 'completed'");
$row = $result->fetch_assoc();
$avg_order_value = $row['avg_order'] ?? 0;

// Daily sales for chart (last 7 days)
$result = $mysqli->query("
    SELECT DATE(created_at) as date, 
           COUNT(*) as orders,
           SUM(total_amount) as revenue
    FROM orders
    WHERE status = 'completed'
    AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date ASC
");
$daily_sales = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $daily_sales[] = $row;
    }
}

// Fill missing days with zero values
$chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $day_name = date('D', strtotime("-$i days"));
    
    $found = false;
    foreach ($daily_sales as $sale) {
        if ($sale['date'] == $date) {
            $chart_data[] = [
                'date' => $date,
                'day' => $day_name,
                'revenue' => (float)$sale['revenue'],
                'orders' => (int)$sale['orders']
            ];
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $chart_data[] = [
            'date' => $date,
            'day' => $day_name,
            'revenue' => 0,
            'orders' => 0
        ];
    }
}

// Top selling products
$result = $mysqli->query("
    SELECT oi.product_name,
           COUNT(*) as quantity_sold,
           SUM(oi.total_price) as total_revenue,
           AVG(oi.unit_price) as avg_price
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status = 'completed'
    AND o.created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)
    GROUP BY oi.product_name
    ORDER BY total_revenue DESC
    LIMIT 5
");
$top_products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $top_products[] = $row;
    }
}

// Order status distribution
$result = $mysqli->query("
    SELECT status, COUNT(*) as count
    FROM orders
    WHERE created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)
    GROUP BY status
    ORDER BY count DESC
");
$order_status = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $order_status[] = $row;
    }
}

include 'includes/header.php';
include 'includes/sidebar.phtml';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-2">Reports & Analytics</h2>
            <p class="text-muted mb-0">Real-time business insights and performance metrics</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <button class="btn btn-outline-primary" onclick="window.print()">
                    <i class="fas fa-download me-2"></i>Export Report
                </button>
                <a href="setup_orders_table.php" class="btn btn-success">
                    <i class="fas fa-database me-2"></i>Setup Data
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Report Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-success mx-auto mb-3">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="fw-bold mb-1">Rs. <?= e(number_format($month_revenue, 0)) ?></h3>
                <p class="text-muted mb-2">This Month Revenue</p>
                <small class="<?= e($revenue_growth >= 0 ? 'text-success' : 'text-danger') ?>">
                    <i class="fas fa-arrow-<?= e($revenue_growth >= 0 ? 'up' : 'down') ?>"></i> 
                    <?= e(abs(round($revenue_growth, 1))) ?>% from last month
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-primary mx-auto mb-3">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="fw-bold mb-1"><?= e($month_orders) ?></h3>
                <p class="text-muted mb-2">Orders This Month</p>
                <small class="<?= e($orders_growth >= 0 ? 'text-success' : 'text-danger') ?>">
                    <i class="fas fa-arrow-<?= e($orders_growth >= 0 ? 'up' : 'down') ?>"></i> 
                    <?= e(abs(round($orders_growth, 1))) ?>% from last month
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-info mx-auto mb-3">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="fw-bold mb-1"><?= e($new_customers) ?></h3>
                <p class="text-muted mb-2">New Customers</p>
                <small class="<?= e($customers_growth >= 0 ? 'text-success' : 'text-danger') ?>">
                    <i class="fas fa-arrow-<?= e($customers_growth >= 0 ? 'up' : 'down') ?>"></i> 
                    <?= e(abs(round($customers_growth, 1))) ?>% from last month
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-warning mx-auto mb-3">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <h3 class="fw-bold mb-1">Rs. <?= e(number_format($avg_order_value, 0)) ?></h3>
                <p class="text-muted mb-2">Average Order Value</p>
                <small class="text-info">
                    <i class="fas fa-calculator"></i> Based on <?= e($total_orders) ?> orders
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <!-- Revenue Chart -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Revenue Trend (Last 7 Days)</h5>
                    <div class="text-muted small">
                        Total: Rs. <?= e(number_format(array_sum(array_column($chart_data, 'revenue')), 0)) ?>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Top Products -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Top Selling Products (30 Days)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($top_products)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-box fa-2x mb-2"></i>
                        <p class="mb-0">No sales data available</p>
                        <small>Complete some orders to see top products</small>
                    </div>
                <?php else: ?>
                    <?php 
                    $max_revenue = max(array_column($top_products, 'total_revenue'));
                    foreach ($top_products as $index => $product): 
                        $percentage = $max_revenue > 0 ? ($product['total_revenue'] / $max_revenue) * 100 : 0;
                        $colors = ['success', 'primary', 'info', 'warning', 'secondary'];
                        $color = $colors[$index % count($colors)];
                    ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0"><?= htmlspecialchars($product['product_name']) ?></h6>
                                <small class="text-muted"><?= e($product['quantity_sold']) ?> sold</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">Rs. <?= e(number_format($product['total_revenue'], 0)) ?></div>
                                <div class="progress" style="height: 4px; width: 60px;">
                                    <div class="progress-bar bg-<?= e($color) ?>" style="width: <?= e($percentage) ?>%"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Row -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Order Status Distribution (30 Days)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($order_status)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-chart-pie fa-2x mb-2"></i>
                        <p class="mb-0">No order data available</p>
                    </div>
                <?php else: ?>
                    <canvas id="orderStatusChart" height="150"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Quick Stats</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h4 class="text-success mb-1"><?= e($total_orders) ?></h4>
                        <small class="text-muted">Total Completed Orders</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-primary mb-1">Rs. <?= e(number_format($total_revenue, 0)) ?></h4>
                        <small class="text-muted">Total Revenue</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info mb-1"><?= e(count($top_products)) ?></h4>
                        <small class="text-muted">Active Products</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning mb-1"><?= e(array_sum(array_column($order_status, 'count'))) ?></h4>
                        <small class="text-muted">Total Orders (30 Days)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

        </div> <!-- End container-fluid -->
    </div> <!-- End main-content -->

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Revenue Chart with real data
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const chartData = <?= json_encode($chart_data) ?>;

const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: chartData.map(item => item.day),
        datasets: [{
            label: 'Revenue (Rs.)',
            data: chartData.map(item => item.revenue),
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
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const dataIndex = context.dataIndex;
                        const revenue = chartData[dataIndex].revenue;
                        const orders = chartData[dataIndex].orders;
                        return [
                            `Revenue: Rs. ${revenue.toLocaleString()}`,
                            `Orders: ${orders}`
                        ];
                    }
                }
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

<?php if (!empty($order_status)): ?>
// Order Status Chart with real data
const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
const orderStatusData = <?= json_encode($order_status) ?>;

const orderStatusChart = new Chart(orderStatusCtx, {
    type: 'doughnut',
    data: {
        labels: orderStatusData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
        datasets: [{
            data: orderStatusData.map(item => item.count),
            backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#6f42c1', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return `${context.label}: ${context.parsed} (${percentage}%)`;
                    }
                }
            }
        }
    }
});
<?php endif; ?>
</script>

<!-- MDBootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

</body>
</html>
