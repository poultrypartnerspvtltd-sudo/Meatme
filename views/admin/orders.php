<form id="csrf-token-form" style="display:none;">
    <?= \App\Core\CSRF::field() ?>
</form>
<!-- Admin Orders Management -->
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Order Management</h1>
                    <p class="text-muted mb-0">Manage customer orders and track delivery status</p>
                </div>
                <div>
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        <i class="fas fa-shopping-cart me-1"></i>
                        <?= e($stats['total_orders'] ?? 0) ?> Total Orders
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= e($stats['total_orders'] ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= e($stats['pending_orders'] ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Delivered Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= e($stats['delivered_orders'] ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. <?= e(number_format($stats['total_revenue'] ?? 0, 2)) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-rupee-sign fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Orders</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Delivery Type</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                        <p>No orders found</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>
                                        <strong class="text-primary">#<?= htmlspecialchars($order['order_number']) ?></strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <i class="fas fa-envelope text-muted me-1"></i>
                                            <?= htmlspecialchars($order['customer_email'] ?? 'N/A') ?>
                                        </div>
                                        <?php if (!empty($order['customer_phone'])): ?>
                                            <div class="small">
                                                <i class="fas fa-phone text-muted me-1"></i>
                                                <?= htmlspecialchars($order['customer_phone']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($order['delivery_type'] === 'self_pickup'): ?>
                                            <span class="badge bg-info">
                                                <i class="fas fa-store me-1"></i>Self Pickup
                                            </span>
                                        <?php elseif ($order['delivery_type'] === 'home_delivery'): ?>
                                            <span class="badge bg-primary">
                                                <i class="fas fa-truck me-1"></i>Home Delivery
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($order['items_summary'] ?? 'N/A') ?>
                                        </small>
                                    </td>
                                    <td>
                                        <strong>Rs. <?= e(number_format($order['total_amount'], 2)) ?></strong>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = '';
                                        $statusIcon = '';
                                        switch ($order['status']) {
                                            case 'pending':
                                                $statusClass = 'warning';
                                                $statusIcon = 'clock';
                                                break;
                                            case 'confirmed':
                                                $statusClass = 'info';
                                                $statusIcon = 'check';
                                                break;
                                            case 'preparing':
                                                $statusClass = 'primary';
                                                $statusIcon = 'cog';
                                                break;
                                            case 'out_for_delivery':
                                                $statusClass = 'info';
                                                $statusIcon = 'truck';
                                                break;
                                            case 'delivered':
                                                $statusClass = 'success';
                                                $statusIcon = 'check-circle';
                                                break;
                                            case 'cancelled':
                                                $statusClass = 'danger';
                                                $statusIcon = 'times-circle';
                                                break;
                                            default:
                                                $statusClass = 'secondary';
                                                $statusIcon = 'question-circle';
                                        }
                                        ?>
                                        <span class="badge bg-<?= e($statusClass) ?>">
                                            <i class="fas fa-<?= e($statusIcon) ?> me-1"></i>
                                            <?= e(ucfirst(str_replace('_', ' ', $order['status']))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($order['formatted_date']) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= e(\App\Core\View::url('admin/orders/' . $order['id'])) ?>" class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" title="Update Status">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item status-update" href="#" data-order-id="<?= e($order['id']) ?>" data-status="confirmed">
                                                        <i class="fas fa-check text-info me-2"></i>Mark as Confirmed
                                                    </a></li>
                                                    <li><a class="dropdown-item status-update" href="#" data-order-id="<?= e($order['id']) ?>" data-status="preparing">
                                                        <i class="fas fa-cog text-primary me-2"></i>Mark as Preparing
                                                    </a></li>
                                                    <li><a class="dropdown-item status-update" href="#" data-order-id="<?= e($order['id']) ?>" data-status="out_for_delivery">
                                                        <i class="fas fa-truck text-info me-2"></i>Out for Delivery
                                                    </a></li>
                                                    <li><a class="dropdown-item status-update" href="#" data-order-id="<?= e($order['id']) ?>" data-status="delivered">
                                                        <i class="fas fa-check-circle text-success me-2"></i>Mark as Delivered
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item status-update text-danger" href="#" data-order-id="<?= e($order['id']) ?>" data-status="cancelled">
                                                        <i class="fas fa-times-circle text-danger me-2"></i>Cancel Order
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.text-primary {
    color: #5a5c69 !important;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle status updates
    document.querySelectorAll('.status-update').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const orderId = this.getAttribute('data-order-id');
            const newStatus = this.getAttribute('data-status');
            const statusText = this.textContent.trim();

            if (confirm(`Are you sure you want to ${statusText.toLowerCase()}?`)) {
                // Show loading state
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;

                // Make API call
                const csrfToken = document.querySelector('#csrf-token-form input[name="csrf_token"]')?.value 
                    || window.MeatMe?.config?.csrfToken 
                    || '';

                const params = new URLSearchParams();
                if (csrfToken) {
                    params.append('csrf_token', csrfToken);
                }
                params.append('status', newStatus);
                params.append('_method', 'PUT');

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
                        showToast('Status updated successfully!', 'success');

                        // Reload page to show updated status
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showToast(data.message || 'Failed to update status', 'error');
                        // Restore button
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred while updating status', 'error');
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
