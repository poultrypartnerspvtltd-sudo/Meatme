<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$page_title = 'Orders Management';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    $payment_status = $_POST['payment_status'] ?? null;

    try {
        if ($payment_status) {
            $stmt = $pdo->prepare("UPDATE orders SET status = ?, payment_status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$new_status, $payment_status, $order_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$new_status, $order_id]);
        }

        $success_message = "Order status updated successfully!";
    } catch (PDOException $e) {
        $error_message = "Failed to update order status: " . $e->getMessage();
    }
}

// Get orders statistics
try {
    $stats = [
        'total_orders' => 0,
        'pending_orders' => 0,
        'processing_orders' => 0,
        'completed_orders' => 0,
        'total_revenue' => 0
    ];

    // Get order counts by status
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
    $statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($statusCounts as $count) {
        $stats['total_orders'] += $count['count'];
        switch ($count['status']) {
            case 'pending':
                $stats['pending_orders'] = $count['count'];
                break;
            case 'confirmed':
            case 'preparing':
                $stats['processing_orders'] += $count['count'];
                break;
            case 'out_for_delivery':
            case 'delivered':
            case 'completed':
                $stats['completed_orders'] += $count['count'];
                break;
        }
    }

    // Get total revenue
    $stmt = $pdo->query("SELECT SUM(total_amount) as revenue FROM orders WHERE payment_status = 'paid'");
    $revenue = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_revenue'] = $revenue['revenue'] ?? 0;

} catch (PDOException $e) {
    $stats = [
        'total_orders' => 0,
        'pending_orders' => 0,
        'processing_orders' => 0,
        'completed_orders' => 0,
        'total_revenue' => 0
    ];
}

// Get orders with customer info
try {
    $orders = [];
    $stmt = $pdo->prepare("
        SELECT
            o.*,
            u.name as customer_name,
            u.email as customer_email,
            u.phone as customer_phone,
            DATE_FORMAT(o.created_at, '%M %d, %Y') as formatted_date,
            DATE_FORMAT(o.created_at, '%h:%i %p') as formatted_time
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $orders = [];
    $error_message = "Failed to load orders: " . $e->getMessage();
}

error_log('[Admin] Orders page loaded: ' . count($orders) . ' orders');

include 'includes/header.php';
include 'includes/sidebar.phtml';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-2">Orders Management</h2>
            <p class="text-muted mb-0">Track and manage customer orders</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <button class="btn btn-outline-primary">
                    <i class="fas fa-download me-2"></i>Export
                </button>
            </div>
        </div>
    </div>
</div>

<?php if (isset($success_message)): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success_message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error_message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Order Statistics -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-warning mx-auto mb-2" style="width: 50px; height: 50px;">
                    <i class="fas fa-clock"></i>
                </div>
                <h4 class="fw-bold mb-1"><?= e($stats['pending_orders'] + $stats['processing_orders']) ?></h4>
                <p class="text-muted mb-0">Active Orders</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-info mx-auto mb-2" style="width: 50px; height: 50px;">
                    <i class="fas fa-cog"></i>
                </div>
                <h4 class="fw-bold mb-1"><?= e($stats['processing_orders']) ?></h4>
                <p class="text-muted mb-0">Processing</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-success mx-auto mb-2" style="width: 50px; height: 50px;">
                    <i class="fas fa-check"></i>
                </div>
                <h4 class="fw-bold mb-1"><?= e($stats['completed_orders']) ?></h4>
                <p class="text-muted mb-0">Completed</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-primary mx-auto mb-2" style="width: 50px; height: 50px;">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <h4 class="fw-bold mb-1">Rs. <?= e(number_format($stats['total_revenue'], 0)) ?></h4>
                <p class="text-muted mb-0">Total Revenue</p>
            </div>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Recent Orders (<?= e(count($orders)) ?>)</h5>
            </div>
            <div class="col-auto">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-secondary active">All</button>
                    <button class="btn btn-outline-secondary">Pending</button>
                    <button class="btn btn-outline-secondary">Processing</button>
                    <button class="btn btn-outline-secondary">Completed</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">No Orders Yet</h4>
                <p class="text-muted">Orders will appear here once customers start placing orders.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Payment</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($order['order_number']) ?></strong>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0">
                                            <?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?>
                                        </h6>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($order['customer_email'] ?? $order['delivery_address'] ? json_decode($order['delivery_address'], true)['email'] ?? '' : '') ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($order['customer_phone'] ?? $order['delivery_address'] ? json_decode($order['delivery_address'], true)['phone'] ?? '' : '') ?>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge bg-<?= e($order['payment_method'] === 'eSewa' ? 'success' : 'warning') ?> mb-1">
                                            <?= htmlspecialchars($order['payment_method']) ?>
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            <?= e($order['payment_status'] === 'paid' ? 'Paid' : 'Unpaid') ?>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <strong>Rs. <?= e(number_format($order['total_amount'], 2)) ?></strong>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'confirmed' => 'info',
                                        'preparing' => 'primary',
                                        'out_for_delivery' => 'info',
                                        'delivered' => 'success',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $color = $statusColors[$order['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= e($color) ?>">
                                        <?= e(ucfirst(str_replace('_', ' ', $order['status']))) ?>
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        <?= htmlspecialchars($order['formatted_date']) ?><br>
                                        <?= htmlspecialchars($order['formatted_time']) ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="View Details"
                                                onclick="viewOrderDetails(<?= e($order['id']) ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <?php if ($order['status'] !== 'delivered' && $order['status'] !== 'cancelled'): ?>
                                        <button class="btn btn-success btn-sm" title="Mark as Completed"
                                                onclick="quickCompleteOrder(<?= e($order['id']) ?>)">
                                            <i class="fas fa-check"></i> Complete
                                        </button>
                                        <?php endif; ?>

                                        <button class="btn btn-outline-secondary" title="Update Status"
                                                onclick="updateOrderStatus(<?= e($order['id']) ?>, '<?= e($order['status']) ?>', '<?= e($order['payment_status']) ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="btn btn-outline-info" title="Print Invoice"
                                                onclick="printInvoice(<?= e($order['id']) ?>)">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" name="order_id" id="update_order_id">
                    <div class="mb-3">
                        <label for="status" class="form-label">Order Status</label>
                        <select class="form-select" name="status" id="update_status" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="preparing">Preparing</option>
                            <option value="out_for_delivery">Out for Delivery</option>
                            <option value="delivered">Delivered</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="payment_status" class="form-label">Payment Status</label>
                        <select class="form-select" name="payment_status" id="update_payment_status">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_status" class="btn btn-success">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// View order details
function viewOrderDetails(orderId) {
    // This would load order details via AJAX
    // For now, redirect to a detailed view
    window.location.href = 'order_details.php?id=' + orderId;
}

// Quick complete order
function quickCompleteOrder(orderId) {
    if (confirm('Are you sure you want to mark this order as completed?')) {
        // Show loading state
        const button = event.target.closest('button');
        const originalHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;

        // Create form data
        const formData = new FormData();
        formData.append('update_status', '1');
        formData.append('order_id', orderId);
        formData.append('status', 'completed');
        formData.append('payment_status', 'paid');
        const csrfToken =
            document.querySelector('#updateStatusModal input[name="csrf_token"]')?.value ||
            document.querySelector('#csrf-token-form input[name="csrf_token"]')?.value ||
            window.MeatMe?.config?.csrfToken ||
            '';
        if (csrfToken) {
            formData.append('csrf_token', csrfToken);
        }

        // Submit via fetch
        fetch(window.location.href, {
            method: 'POST',
            headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {},
            body: formData
        })
        .then(response => {
            const refreshedToken = response.headers.get('X-CSRF-TOKEN');
            if (refreshedToken) {
                const modalInput = document.querySelector('#updateStatusModal input[name="csrf_token"]');
                if (modalInput) {
                    modalInput.value = refreshedToken;
                }
                const globalInput = document.querySelector('#csrf-token-form input[name="csrf_token"]');
                if (globalInput) {
                    globalInput.value = refreshedToken;
                }
                window.csrfToken = refreshedToken;
            }
            return response.text();
        })
        .then(data => {
            // Reload page to show updated status
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update order status. Please try again.');
            button.innerHTML = originalHtml;
            button.disabled = false;
        });
    }
}

// Update order status (opens modal)
function updateOrderStatus(orderId, currentStatus, currentPaymentStatus) {
    document.getElementById('update_order_id').value = orderId;
    document.getElementById('update_status').value = currentStatus;
    document.getElementById('update_payment_status').value = currentPaymentStatus;

    const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
    modal.show();
}

// Handle modal form submission
document.addEventListener('DOMContentLoaded', function() {
    const updateForm = document.querySelector('#updateStatusModal form');
    if (updateForm) {
        updateForm.addEventListener('submit', function(e) {
            // Let the form submit normally, but we'll handle the response
            // The page will reload due to PHP processing
        });
    }
});

// Print invoice
function printInvoice(orderId) {
    window.open('print_invoice.php?id=' + orderId, '_blank');
}
</script>

        </div> <!-- End container-fluid -->
    </div> <!-- End main-content -->

<!-- MDBootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

</body>
</html>
