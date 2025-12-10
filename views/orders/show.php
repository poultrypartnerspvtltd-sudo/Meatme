<!-- Page Header -->
<div class="container-fluid bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url()) ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url('dashboard')) ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url('orders')) ?>">My Orders</a></li>
                        <li class="breadcrumb-item active">Order Details</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-bold mb-0">Order Details</h1>
                <p class="text-muted mb-0">Order #<?= e(\App\Core\View::escape($order['order_number'])) ?></p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="<?= e(\App\Core\View::url('orders')) ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Content -->
<div class="container py-5">
    <div class="row">
        <!-- Order Summary -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-receipt me-2"></i>Order Summary
                    </h5>
                    <?= e($order['status_badge']) ?>
                </div>
                <div class="card-body">
                    <!-- Order Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <small class="text-muted d-block">Order Number</small>
                                <strong class="fs-5">#<?= e(\App\Core\View::escape($order['order_number'])) ?></strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Order Date</small>
                                <strong><?= e(\App\Core\View::escape($order['formatted_date'])) ?></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <small class="text-muted d-block">Payment Method</small>
                                <strong class="text-uppercase"><?= e(\App\Core\View::escape($order['payment_method'])) ?></strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Payment Status</small>
                                <span class="badge bg-<?= e($order['payment_status'] === 'paid' ? 'success' : 'warning') ?>">
                                    <?= e(ucfirst($order['payment_status'])) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Order Items -->
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-shopping-bag me-2"></i>Order Items
                    </h6>

                    <?php foreach ($orderItems as $item): ?>
                        <div class="d-flex align-items-center mb-3 p-3 border rounded">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold"><?= e(\App\Core\View::escape($item['product_name'])) ?></h6>
                                <small class="text-muted">SKU: <?= e(\App\Core\View::escape($item['product_sku'] ?? 'N/A')) ?></small>
                            </div>
                            <div class="text-end">
                                <div class="mb-1">
                                    <span class="fw-bold">Qty: <?= e((int)$item['quantity']) ?></span>
                                </div>
                                <div class="text-success fw-bold">
                                    Rs. <?= e(number_format((float)$item['total_price'], 2)) ?>
                                </div>
                                <small class="text-muted">
                                    Rs. <?= e(number_format((float)$item['unit_price'], 2)) ?> each
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <hr>

                    <!-- Order Totals -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <small class="text-muted">Subtotal</small>
                                <div class="fw-bold">Rs. <?= e(number_format((float)$order['subtotal'], 2)) ?></div>
                            </div>
                            <?php if ((float)$order['delivery_fee'] > 0): ?>
                                <div class="mb-2">
                                    <small class="text-muted">Delivery Fee</small>
                                    <div class="fw-bold">Rs. <?= e(number_format((float)$order['delivery_fee'], 2)) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded text-end">
                                <small class="text-muted d-block">Total Amount</small>
                                <div class="h4 fw-bold text-success mb-0">
                                    Rs. <?= e(number_format((float)$order['total_amount'], 2)) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status & Actions -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-info-circle me-2"></i>Order Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-3">
                            <div class="status-indicator me-3">
                                <?php if ($order['status'] === 'pending'): ?>
                                    <i class="fas fa-clock text-warning fa-2x"></i>
                                <?php elseif ($order['status'] === 'confirmed'): ?>
                                    <i class="fas fa-check text-info fa-2x"></i>
                                <?php elseif ($order['status'] === 'preparing'): ?>
                                    <i class="fas fa-cog text-primary fa-2x"></i>
                                <?php elseif ($order['status'] === 'out_for_delivery'): ?>
                                    <i class="fas fa-truck text-primary fa-2x"></i>
                                <?php elseif ($order['status'] === 'delivered' || $order['status'] === 'completed'): ?>
                                    <i class="fas fa-check text-success fa-2x"></i>
                                <?php else: ?>
                                    <i class="fas fa-times text-danger fa-2x"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">
                                    <?= e(ucfirst(str_replace('_', ' ', $order['status']))) ?>
                                </h6>
                                <small class="text-muted">
                                    <?php if ($order['status'] === 'pending'): ?>
                                        Waiting for confirmation
                                    <?php elseif ($order['status'] === 'confirmed'): ?>
                                        Order confirmed
                                    <?php elseif ($order['status'] === 'preparing'): ?>
                                        Your order is being prepared
                                    <?php elseif ($order['status'] === 'out_for_delivery'): ?>
                                        Out for delivery
                                    <?php elseif ($order['status'] === 'delivered' || $order['status'] === 'completed'): ?>
                                        Successfully delivered
                                    <?php else: ?>
                                        Order cancelled
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    </div>

                    <?php if ($order['status'] === 'out_for_delivery'): ?>
                        <button class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>Track Order
                        </button>
                    <?php endif; ?>

                    <button class="btn btn-outline-secondary w-100">
                        <i class="fas fa-envelope me-2"></i>Contact Support
                    </button>
                </div>
            </div>

            <!-- Delivery Information -->
            <?php if (!empty($order['delivery_address_parsed'])): ?>
                <?php $deliveryAddress = $order['delivery_address_parsed']; ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-map-marker-alt me-2"></i>Delivery Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Delivery Address</small>
                            <strong>
                                <?= e(\App\Core\View::escape($deliveryAddress['name'] ?? '')) ?><br>
                                <?= e(\App\Core\View::escape($deliveryAddress['address'] ?? '')) ?><br>
                                Phone: <?= e(\App\Core\View::escape($deliveryAddress['phone'] ?? '')) ?>
                            </strong>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Shipping Method</small>
                            <strong>
                                <?php if (($deliveryAddress['shipping_type'] ?? '') === 'self_pickup'): ?>
                                    <i class="fas fa-store text-primary me-1"></i>Self Pickup
                                <?php else: ?>
                                    <i class="fas fa-truck text-primary me-1"></i>Home Delivery
                                <?php endif; ?>
                            </strong>
                        </div>

                        <?php if (!empty($order['notes'])): ?>
                            <div class="mb-0">
                                <small class="text-muted d-block">Special Instructions</small>
                                <em><?= e(\App\Core\View::escape($order['notes'])) ?></em>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
