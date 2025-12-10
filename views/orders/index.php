<!-- Page Header -->
<div class="container-fluid bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url()) ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url('dashboard')) ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Orders</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-bold mb-0">My Orders</h1>
                <p class="text-muted mb-0">Track and manage your orders</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Place New Order
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Orders Content -->
<div class="container py-5">
    <?php if (empty($orders)): ?>
        <!-- Empty Orders -->
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="py-5">
                    <i class="fas fa-box fa-5x text-muted mb-4"></i>
                    <h3 class="fw-bold mb-3">No orders yet</h3>
                    <p class="text-muted mb-4">
                        You haven't placed any orders yet. Start shopping for fresh chicken products!
                    </p>
                    <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-success btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Orders List -->
        <div class="row">
            <?php foreach ($orders as $order): ?>
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><?= e($order['order_number']) ?></h6>
                            <?= $order['status_badge'] ?>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <small class="text-muted">Order Date</small>
                                        <div class="fw-bold"><?= e($order['formatted_date']) ?></div>
                                        <small class="text-muted"><?= e($order['formatted_time']) ?></small>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="mb-2">
                                        <small class="text-muted">Total Amount</small>
                                        <div class="h5 fw-bold text-success mb-0">
                                            Rs. <?= e(number_format($order['total_amount'], 2)) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                                <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php if (in_array($order['status'], ['out_for_delivery', 'shipped'])): ?>
                                        <small class="text-primary">
                                            <i class="fas fa-truck me-1"></i>Out for delivery
                                        </small>
                                    <?php elseif (in_array($order['status'], ['preparing', 'processing', 'confirmed'])): ?>
                                        <small class="text-info">
                                            <i class="fas fa-cog me-1"></i>Being prepared
                                        </small>
                                    <?php elseif (in_array($order['status'], ['delivered', 'completed'])): ?>
                                        <small class="text-success">
                                            <i class="fas fa-check me-1"></i>Delivered
                                        </small>
                                    <?php elseif ($order['status'] === 'cancelled'): ?>
                                        <small class="text-danger">
                                            <i class="fas fa-times-circle me-1"></i>Cancelled
                                        </small>
                                    <?php else: ?>
                                        <small class="text-warning">
                                            <i class="fas fa-clock me-1"></i>Pending confirmation
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <a href="<?= e(\App\Core\View::url('orders/' . $order['id'])) ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                    <?php if ($order['status'] === 'shipped'): ?>
                                        <button class="btn btn-outline-info btn-sm ms-1">
                                            <i class="fas fa-map-marker-alt me-1"></i>Track
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Order Statistics -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Order Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-3">
                                <div class="h4 fw-bold text-primary"><?= e(count($orders)) ?></div>
                                <small class="text-muted">Total Orders</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="h4 fw-bold text-success">
                                    <?= e(count(array_filter($orders, function($o) { return in_array($o['status'], ['delivered', 'completed']); }))) ?>
                                </div>
                                <small class="text-muted">Completed</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="h4 fw-bold text-warning">
                                    <?= e(count(array_filter($orders, function($o) { return in_array($o['status'], ['pending', 'confirmed', 'preparing', 'out_for_delivery']); }))) ?>
                                </div>
                                <small class="text-muted">In Progress</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="h4 fw-bold text-info">
                                    Rs. <?= e(number_format(array_sum(array_column($orders, 'total_amount')), 2)) ?>
                                </div>
                                <small class="text-muted">Total Spent</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
