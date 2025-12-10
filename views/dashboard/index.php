<style>
/* Dashboard Welcome Header - Always visible in both light and dark modes */
.bg-gradient-success,
.container-fluid.bg-gradient-success {
    background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%) !important;
    background-color: #2e7d32 !important;
}

.bg-gradient-success *,
.bg-gradient-success h1,
.bg-gradient-success h2,
.bg-gradient-success h3,
.bg-gradient-success h4,
.bg-gradient-success h5,
.bg-gradient-success h6,
.bg-gradient-success p,
.bg-gradient-success span,
.bg-gradient-success small,
.bg-gradient-success div,
.bg-gradient-success i,
.bg-gradient-success .text-white,
.bg-gradient-success .text-white-50 {
    color: #ffffff !important;
}

.bg-gradient-success .text-white-50 {
    color: rgba(255, 255, 255, 0.85) !important;
}

/* Dark mode */
[data-theme="dark"] .bg-gradient-success,
[data-theme="dark"] .container-fluid.bg-gradient-success {
    background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%) !important;
    background-color: #1b5e20 !important;
}

[data-theme="dark"] .bg-gradient-success *,
[data-theme="dark"] .bg-gradient-success h1,
[data-theme="dark"] .bg-gradient-success h2,
[data-theme="dark"] .bg-gradient-success h3,
[data-theme="dark"] .bg-gradient-success h4,
[data-theme="dark"] .bg-gradient-success h5,
[data-theme="dark"] .bg-gradient-success h6,
[data-theme="dark"] .bg-gradient-success p,
[data-theme="dark"] .bg-gradient-success span,
[data-theme="dark"] .bg-gradient-success small,
[data-theme="dark"] .bg-gradient-success div,
[data-theme="dark"] .bg-gradient-success i,
[data-theme="dark"] .bg-gradient-success .text-white,
[data-theme="dark"] .bg-gradient-success .text-white-50 {
    color: #ffffff !important;
}
</style>

<!-- Dashboard Header -->
<div class="container-fluid bg-gradient-success py-5" style="background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%) !important; background-color: #2e7d32 !important;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold text-white mb-2" style="color: #ffffff !important;">
                    Welcome back, <?= e(\App\Core\View::escape($user['name'])) ?>! 👋
                </h1>
                <p class="text-white-50 mb-0" style="color: rgba(255, 255, 255, 0.85) !important;">
                    Manage your orders, profile, and explore fresh chicken products
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="d-flex align-items-center justify-content-md-end text-white" style="color: #ffffff !important;">
                    <div class="me-3">
                        <i class="fas fa-user-circle fa-3x" style="color: #ffffff !important;"></i>
                    </div>
                    <div style="color: #ffffff !important;">
                        <div class="fw-bold" style="color: #ffffff !important;">Member since</div>
                        <small style="color: rgba(255, 255, 255, 0.85) !important;"><?= e(date('M Y', strtotime($user['created_at']))) ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Navigation -->
<div class="container-fluid bg-white border-bottom">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light p-0">
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active fw-bold" href="<?= e(\App\Core\View::url('dashboard')) ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= e(\App\Core\View::url('orders')) ?>">
                            <i class="fas fa-box me-2"></i>My Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= e(\App\Core\View::url('profile')) ?>">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= e(\App\Core\View::url('wishlist')) ?>">
                            <i class="fas fa-heart me-2"></i>Wishlist
                            <?php if ($wishlistCount > 0): ?>
                                <span class="badge bg-danger rounded-pill"><?= e($wishlistCount) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="<?= e(\App\Core\View::url('logout')) ?>">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>

<!-- Dashboard Content -->
<div class="container py-5">
    <!-- Statistics Cards -->
    <div class="row mb-5">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-shopping-cart text-white fa-lg"></i>
                    </div>
                    <h3 class="fw-bold mb-1"><?= e($stats['total_orders']) ?></h3>
                    <p class="text-muted mb-0">Total Orders</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-dollar-sign text-white fa-lg"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Rs. <?= e(number_format($stats['total_spent'], 2)) ?></h3>
                    <p class="text-muted mb-0">Total Spent</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-clock text-white fa-lg"></i>
                    </div>
                    <h3 class="fw-bold mb-1"><?= e($stats['pending_orders']) ?></h3>
                    <p class="text-muted mb-0">Pending Orders</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-heart text-white fa-lg"></i>
                    </div>
                    <h3 class="fw-bold mb-1"><?= e($wishlistCount) ?></h3>
                    <p class="text-muted mb-0">Wishlist Items</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Profile Overview -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>Profile Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x text-success"></i>
                        </div>
                        <h5 class="fw-bold"><?= e(\App\Core\View::escape($user['name'])) ?></h5>
                        <p class="text-muted mb-0"><?= e(\App\Core\View::escape($user['email'])) ?></p>
                        <?php if (isset($user['phone'])): ?>
                            <small class="text-muted"><?= e(\App\Core\View::escape($user['phone'])) ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="<?= e(\App\Core\View::url('profile')) ?>" class="btn btn-outline-success">
                            <i class="fas fa-edit me-2"></i>Update Profile
                        </a>
                        <a href="<?= e(\App\Core\View::url('profile/password')) ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-lock me-2"></i>Change Password
                        </a>
                    </div>
                    
                    <hr>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold text-success"><?= e($stats['completed_orders']) ?></div>
                            <small class="text-muted">Completed</small>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-warning"><?= e($stats['pending_orders']) ?></div>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>Recent Orders
                    </h5>
                    <a href="<?= e(\App\Core\View::url('orders')) ?>" class="btn btn-outline-primary btn-sm">
                        View All Orders
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($recentOrders)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                            <h6>No orders yet</h6>
                            <p class="text-muted mb-3">Start shopping to see your orders here!</p>
                            <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-success">
                                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td>
                                                <strong><?= e(\App\Core\View::escape($order['order_number'])) ?></strong>
                                            </td>
                                            <td>
                                                <div><?= e($order['formatted_date']) ?></div>
                                                <small class="text-muted"><?= e($order['formatted_time']) ?></small>
                                            </td>
                                            <td>
                                                <strong>Rs. <?= e(number_format($order['total_amount'], 2)) ?></strong>
                                            </td>
                                            <td>
                                                <?php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'processing' => 'info',
                                                    'shipped' => 'primary',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $color = $statusColors[$order['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= e($color) ?>">
                                                    <?= e(ucfirst($order['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= e(\App\Core\View::url('orders/' . $order['id'])) ?>" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Active Deliveries / Pending Orders -->
    <?php if (!empty($pendingOrders)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-truck me-2"></i>Active Deliveries & Pending Orders
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($pendingOrders as $order): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-start border-warning border-4">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="fw-bold mb-0"><?= e(\App\Core\View::escape($order['order_number'])) ?></h6>
                                                <span class="badge bg-<?= e($statusColors[$order['status']] ?? 'secondary') ?>">
                                                    <?= e(ucfirst($order['status'])) ?>
                                                </span>
                                            </div>
                                            <p class="text-muted mb-2">
                                                <small><i class="fas fa-calendar me-1"></i><?= e($order['formatted_date']) ?></small>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong class="text-success">Rs. <?= e(number_format($order['total_amount'], 2)) ?></strong>
                                                <a href="<?= e(\App\Core\View::url('orders/' . $order['id'])) ?>" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    Track
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Quick Actions -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-shopping-bag fa-2x mb-2"></i>
                                <span>Shop Products</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="<?= e(\App\Core\View::url('orders')) ?>" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-history fa-2x mb-2"></i>
                                <span>Order History</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="<?= e(\App\Core\View::url('wishlist')) ?>" class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-heart fa-2x mb-2"></i>
                                <span>My Wishlist</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="<?= e(\App\Core\View::url('profile')) ?>" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-user-edit fa-2x mb-2"></i>
                                <span>Edit Profile</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
