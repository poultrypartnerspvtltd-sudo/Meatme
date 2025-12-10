<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(isset($title) ? $title . ' - ' : '') ?>MeatMe Admin</title>
    
    <!-- MDBootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .sidebar {
            background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 0;
            transition: all 0.3s ease;
        }
        
        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="p-4">
            <h4 class="text-white fw-bold mb-0">
                <i class="fas fa-drumstick-bite me-2"></i>MeatMe
            </h4>
            <small class="text-white-50">Admin Panel</small>
        </div>
        
        <ul class="nav flex-column sidebar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="<?= e(\App\Core\View::url('admin/dashboard')) ?>">
                    <i class="fas fa-tachometer-alt me-3"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= e(\App\Core\View::url('admin/products')) ?>">
                    <i class="fas fa-box me-3"></i>Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= e(\App\Core\View::url('admin/categories')) ?>">
                    <i class="fas fa-tags me-3"></i>Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= e(\App\Core\View::url('admin/orders')) ?>">
                    <i class="fas fa-shopping-cart me-3"></i>Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= e(\App\Core\View::url('admin/users')) ?>">
                    <i class="fas fa-users me-3"></i>Users
                </a>
            </li>
            <!-- Coupons menu removed -->
            <li class="nav-item">
                <a class="nav-link" href="<?= e(\App\Core\View::url('admin/reviews')) ?>">
                    <i class="fas fa-star me-3"></i>Reviews
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= e(\App\Core\View::url('admin/reports/sales')) ?>">
                    <i class="fas fa-chart-bar me-3"></i>Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= e(\App\Core\View::url('admin/settings')) ?>">
                    <i class="fas fa-cog me-3"></i>Settings
                </a>
            </li>
        </ul>
        
        <div class="mt-auto p-4">
            <div class="d-flex align-items-center text-white mb-3">
                <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <div class="fw-bold"><?= e(\App\Core\View::escape($user['name'])) ?></div>
                    <small class="text-white-50">Administrator</small>
                </div>
            </div>
            <a href="<?= e(\App\Core\View::url('admin/logout')) ?>" class="btn btn-outline-light btn-sm w-100">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Dashboard</h2>
                <p class="text-muted mb-0">Welcome back, <?= e(\App\Core\View::escape($user['name'])) ?>!</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary d-md-none" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="<?= e(\App\Core\View::url()) ?>" class="btn btn-outline-success" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>View Website
                </a>
            </div>
        </div>
        
        <!-- Flash Messages -->
        <?php if (\App\Core\Session::hasFlash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= e(\App\Core\Session::flash('success')) ?>
                <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary me-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?= e(number_format($stats['total_users'])) ?></h3>
                            <p class="text-muted mb-0">Total Users</p>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> +<?= e($stats['recent_users']) ?> this week
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success me-3">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?= e(number_format($stats['total_products'])) ?></h3>
                            <p class="text-muted mb-0">Total Products</p>
                            <small class="text-info">
                                <?= e($stats['active_products']) ?> active
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning me-3">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?= e(number_format($stats['total_orders'])) ?></h3>
                            <p class="text-muted mb-0">Total Orders</p>
                            <small class="text-warning">
                                <?= e($stats['pending_orders']) ?> pending
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info me-3">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Rs. <?= e(number_format($stats['total_revenue'])) ?></h3>
                            <p class="text-muted mb-0">Total Revenue</p>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> This month
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts and Recent Activity -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Sales Overview</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?= e(\App\Core\View::url('admin/products/create')) ?>" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Add New Product
                            </a>
                            <a href="<?= e(\App\Core\View::url('admin/categories')) ?>" class="btn btn-primary">
                                <i class="fas fa-tags me-2"></i>Manage Categories
                            </a>
                            <a href="<?= e(\App\Core\View::url('admin/orders')) ?>" class="btn btn-warning">
                                <i class="fas fa-shopping-cart me-2"></i>View Orders
                            </a>
                            <a href="<?= e(\App\Core\View::url('admin/users')) ?>" class="btn btn-info">
                                <i class="fas fa-users me-2"></i>Manage Users
                            </a>
                        </div>
                        
                        <hr>
                        
                        <h6 class="mb-3">System Status</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Database</span>
                            <span class="badge bg-success">Connected</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Storage</span>
                            <span class="badge bg-success">Available</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Cache</span>
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success rounded-circle p-2 me-3">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <p class="mb-0">New user registered: John Doe</p>
                                    <small class="text-muted">2 minutes ago</small>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary rounded-circle p-2 me-3">
                                    <i class="fas fa-box text-white"></i>
                                </div>
                                <div>
                                    <p class="mb-0">Product "Fresh Whole Chicken" updated</p>
                                    <small class="text-muted">1 hour ago</small>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded-circle p-2 me-3">
                                    <i class="fas fa-shopping-cart text-white"></i>
                                </div>
                                <div>
                                    <p class="mb-0">Database setup completed successfully</p>
                                    <small class="text-muted">Today</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- MDBootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    
    <script>
        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Sales (Rs.)',
                    data: [12000, 19000, 15000, 25000, 22000, 30000],
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Toggle sidebar for mobile
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.querySelector('[onclick="toggleSidebar()"]');
            
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    </script>
</body>
</html>
