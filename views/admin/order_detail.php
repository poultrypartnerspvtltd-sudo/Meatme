<form id="csrf-token-form" style="display:none;">
    <?= \App\Core\CSRF::field() ?>
</form>
<!-- Admin Order Detail View -->
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url('admin/dashboard')) ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url('admin/orders')) ?>">Orders</a></li>
                            <li class="breadcrumb-item active">Order #<?= htmlspecialchars($order['order_number']) ?></li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-2">Order Details</h1>
                    <p class="text-muted mb-0">Order #<?= htmlspecialchars($order['order_number']) ?> - <?= htmlspecialchars($order['formatted_date']) ?></p>
                </div>
                <div>
                    <?php
                    $statusClass = '';
                    switch ($order['status']) {
                        case 'pending': $statusClass = 'warning'; break;
                        case 'confirmed': $statusClass = 'info'; break;
                        case 'preparing': $statusClass = 'primary'; break;
                        case 'out_for_delivery': $statusClass = 'info'; break;
                        case 'delivered': $statusClass = 'success'; break;
                        case 'cancelled': $statusClass = 'danger'; break;
                        default: $statusClass = 'secondary';
                    }
                    ?>
                    <span class="badge bg-<?= e($statusClass) ?> fs-6 px-3 py-2">
                        <i class="fas fa-circle me-1"></i>
                        <?= e(ucfirst(str_replace('_', ' ', $order['status']))) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Order Information -->
        <div class="col-lg-8 mb-4">
            <!-- Order Items -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Order Items
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($order['items'])): ?>
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                                    <small class="text-muted">
                                        SKU: <?= htmlspecialchars($item['product_sku'] ?? 'N/A') ?> |
                                        Quantity: <?= e($item['quantity']) ?> <?= htmlspecialchars($item['product_unit']) ?> |
                                        Unit Price: Rs. <?= e(number_format($item['unit_price'], 2)) ?>
                                    </small>
                                </div>
                                <div class="text-end">
                                    <strong class="text-primary">Rs. <?= e(number_format($item['total_price'], 2)) ?></strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">No items found for this order.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calculator me-2"></i>Order Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="mb-1"><strong>Subtotal:</strong> Rs. <?= e(number_format($order['subtotal'], 2)) ?></p>
                            <p class="mb-1"><strong>Delivery Fee:</strong> Rs. <?= e(number_format($order['delivery_fee'], 2)) ?></p>
                            <?php if ($order['tax_amount'] > 0): ?>
                                <p class="mb-1"><strong>Tax:</strong> Rs. <?= e(number_format($order['tax_amount'], 2)) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-6 text-end">
                            <h4 class="text-success mb-0">Total: Rs. <?= e(number_format($order['total_amount'], 2)) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer & Delivery Information -->
        <div class="col-lg-4">
            <!-- Customer Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>Customer Information
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></p>
                    <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($order['customer_email'] ?? 'N/A') ?></p>
                    <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone'] ?? 'N/A') ?></p>
                    <p class="mb-1"><strong>Order Date:</strong> <?= htmlspecialchars($order['formatted_date']) ?></p>
                    <p class="mb-0"><strong>Last Updated:</strong> <?= htmlspecialchars($order['formatted_updated_date']) ?></p>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-truck me-2"></i>Delivery Information
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Delivery Type:</strong>
                        <?php if ($order['delivery_type'] === 'self_pickup'): ?>
                            <span class="badge bg-info ms-2">
                                <i class="fas fa-store me-1"></i>Self Pickup
                            </span>
                        <?php elseif ($order['delivery_type'] === 'home_delivery'): ?>
                            <span class="badge bg-primary ms-2">
                                <i class="fas fa-truck me-1"></i>Home Delivery
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary ms-2">N/A</span>
                        <?php endif; ?>
                    </p>

                    <?php if (!empty($order['delivery_address_parsed'])): ?>
                        <?php $address = $order['delivery_address_parsed']; ?>
                        <div class="border-start border-primary border-3 ps-3">
                            <p class="mb-1"><strong>Recipient:</strong> <?= htmlspecialchars($address['name'] ?? $address['first_name'] . ' ' . $address['last_name']) ?></p>
                            <?php if (!empty($address['phone'])): ?>
                                <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($address['phone']) ?></p>
                            <?php endif; ?>

                            <?php if ($order['delivery_type'] === 'home_delivery'): ?>
                                <p class="mb-1"><strong>Address:</strong></p>
                                <address class="mb-0 small">
                                    <?= htmlspecialchars($address['address_line_1']) ?><br>
                                    <?php if (!empty($address['apartment'])): ?>
                                        <?= htmlspecialchars($address['apartment']) ?><br>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($address['city']) ?>, <?= htmlspecialchars($address['state']) ?> <?= htmlspecialchars($address['postal_code']) ?><br>
                                    <?= htmlspecialchars($address['country']) ?>
                                </address>
                            <?php elseif ($order['delivery_type'] === 'self_pickup'): ?>
                                <p class="mb-0"><strong>Pickup Location:</strong> Store Location</p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No delivery address information available.</p>
                    <?php endif; ?>

                    <?php if (!empty($order['notes'])): ?>
                        <hr class="my-3">
                        <p class="mb-1"><strong>Order Notes:</strong></p>
                        <p class="small text-muted mb-0"><?= htmlspecialchars($order['notes']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tasks me-2"></i>Order Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if ($order['status'] === 'pending'): ?>
                            <button class="btn btn-info btn-sm status-update" data-order-id="<?= e($order['id']) ?>" data-status="confirmed">
                                <i class="fas fa-check me-1"></i>Mark as Confirmed
                            </button>
                        <?php endif; ?>

                        <?php if (in_array($order['status'], ['pending', 'confirmed'])): ?>
                            <button class="btn btn-primary btn-sm status-update" data-order-id="<?= e($order['id']) ?>" data-status="preparing">
                                <i class="fas fa-cog me-1"></i>Mark as Preparing
                            </button>
                        <?php endif; ?>

                        <?php if ($order['status'] === 'preparing'): ?>
                            <button class="btn btn-info btn-sm status-update" data-order-id="<?= e($order['id']) ?>" data-status="out_for_delivery">
                                <i class="fas fa-truck me-1"></i>Out for Delivery
                            </button>
                        <?php endif; ?>

                        <?php if ($order['status'] === 'out_for_delivery'): ?>
                            <button class="btn btn-success btn-sm status-update" data-order-id="<?= e($order['id']) ?>" data-status="delivered">
                                <i class="fas fa-check-circle me-1"></i>Mark as Delivered
                            </button>
                        <?php endif; ?>

                        <?php if (!in_array($order['status'], ['delivered', 'cancelled'])): ?>
                            <hr class="my-2">
                            <button class="btn btn-danger btn-sm status-update" data-order-id="<?= e($order['id']) ?>" data-status="cancelled">
                                <i class="fas fa-times-circle me-1"></i>Cancel Order
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if ($order['status'] === 'cancelled' && !empty($order['cancellation_reason'])): ?>
                        <hr class="my-3">
                        <div class="alert alert-danger py-2">
                            <strong>Cancellation Reason:</strong><br>
                            <small><?= htmlspecialchars($order['cancellation_reason']) ?></small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle status updates
    document.querySelectorAll('.status-update').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const newStatus = this.getAttribute('data-status');

            let statusText = '';
            switch (newStatus) {
                case 'confirmed': statusText = 'confirm'; break;
                case 'preparing': statusText = 'mark as preparing'; break;
                case 'out_for_delivery': statusText = 'mark as out for delivery'; break;
                case 'delivered': statusText = 'mark as delivered'; break;
                case 'cancelled': statusText = 'cancel'; break;
                default: statusText = 'update';
            }

            if (confirm(`Are you sure you want to ${statusText} this order?`)) {
                // Show loading state
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
                this.disabled = true;

                const csrfToken = document.querySelector('#csrf-token-form input[name="csrf_token"]')?.value 
                    || window.MeatMe?.config?.csrfToken 
                    || '';

                const params = new URLSearchParams();
                if (csrfToken) {
                    params.append('csrf_token', csrfToken);
                }
                params.append('status', newStatus);
                params.append('_method', 'PUT');

                // Make API call to correct URL with base path
                fetch('<?= e(\App\Core\View::url('admin/orders')) ?>/' + orderId + '/status', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: params
                })
                .then(response => {
                    const refreshedToken = response.headers.get('X-CSRF-TOKEN');
                    if (refreshedToken) {
                        const globalInput = document.querySelector('#csrf-token-form input[name="csrf_token"]');
                        if (globalInput) {
                            globalInput.value = refreshedToken;
                        }
                        window.csrfToken = refreshedToken;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showToast('Order status updated successfully!', 'success');

                        // Reload page to show updated status
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showToast(data.message || 'Failed to update order status', 'error');
                        // Restore button
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred while updating order status', 'error');
                    // Restore button
                    this.innerHTML = originalText;
                    this.disabled = false;
                });
            }
        });
    });
});

function showToast(message, type) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(toast);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}
</script>
