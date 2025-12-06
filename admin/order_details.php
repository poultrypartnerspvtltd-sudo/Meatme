<?php
/**
 * Admin Order Details Page
 * Direct access version for viewing individual order details
 */

session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Get order ID from URL parameter
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get order details
if ($orderId <= 0) {
    // Invalid order ID
    $error = "Invalid order ID provided.";
} else {
    global $mysqli;
    // Get order details with customer information
    $orderQuery = "
        SELECT
            o.*,
            u.name as customer_name,
            u.email as customer_email,
            u.phone as customer_phone,
            DATE_FORMAT(o.created_at, '%M %d, %Y %h:%i %p') as formatted_date,
            DATE_FORMAT(o.updated_at, '%M %d, %Y %h:%i %p') as formatted_updated_date
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ";

    $stmt = $mysqli->prepare($orderQuery);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if (!$order) {
        $error = "Order not found.";
    } else {
        // Get order items
        $itemsQuery = "
            SELECT
                oi.*,
                p.name as product_name,
                p.sku as product_sku,
                p.unit as product_unit
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
            ORDER BY oi.id ASC
        ";

        $stmt = $mysqli->prepare($itemsQuery);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $orderItems = [];
        while ($row = $result->fetch_assoc()) {
            $orderItems[] = $row;
        }

        // Parse delivery address JSON if it exists
        $deliveryAddress = null;
        if (!empty($order['delivery_address'])) {
            $deliveryAddress = json_decode($order['delivery_address'], true);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .status-badge {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }
        .order-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        .btn-back {
            background: #6c757d;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Order Details</h1>
                <p class="text-muted mb-0">View complete order information</p>
            </div>
            <a href="orders.php" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Back to Orders
            </a>
        </div>

        <?php if (isset($error)): ?>
            <!-- Error Message -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h4 class="text-warning">Order Not Found</h4>
                    <p class="text-muted mb-0"><?= e($error) ?></p>
                    <a href="orders.php" class="btn btn-primary mt-3">
                        <i class="fas fa-list me-2"></i>View All Orders
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Order Header -->
            <div class="order-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-1">Order #<?= e($order['order_number']) ?></h2>
                        <p class="mb-2 opacity-75">
                            <i class="fas fa-calendar me-2"></i><?= e($order['formatted_date']) ?>
                        </p>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-white text-dark status-badge">
                                <i class="fas fa-user me-1"></i><?= e($order['customer_name'] ?? 'N/A') ?>
                            </span>
                            <span class="badge bg-white text-dark status-badge">
                                <i class="fas fa-envelope me-1"></i><?= e($order['customer_email'] ?? 'N/A') ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <?php
                        $statusClass = 'secondary';
                        $statusIcon = 'question-circle';
                        switch ($order['status']) {
                            case 'pending': $statusClass = 'warning'; $statusIcon = 'clock'; break;
                            case 'confirmed': $statusClass = 'info'; $statusIcon = 'check'; break;
                            case 'preparing': $statusClass = 'primary'; $statusIcon = 'cog'; break;
                            case 'out_for_delivery': $statusClass = 'info'; $statusIcon = 'truck'; break;
                            case 'delivered': $statusClass = 'success'; $statusIcon = 'check-circle'; break;
                            case 'completed': $statusClass = 'success'; $statusIcon = 'check-double'; break;
                            case 'cancelled': $statusClass = 'danger'; $statusIcon = 'times-circle'; break;
                        }
                        ?>
                        <span class="badge bg-white text-dark status-badge">
                            <i class="fas fa-<?= e($statusIcon) ?> me-1"></i>
                            <?= e(ucfirst(str_replace('_', ' ', $order['status']))) ?>
                        </span>
                        <div class="mt-2">
                            <strong class="text-white h4">Rs. <?= e(number_format($order['total_amount'], 2)) ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Order Items -->
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-shopping-cart me-2"></i>Order Items
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($orderItems)): ?>
                                <?php foreach ($orderItems as $item): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= e($item['product_name']) ?></h6>
                                            <small class="text-muted">
                                                SKU: <?= e($item['product_sku'] ?? 'N/A') ?> |
                                                Quantity: <?= e($item['quantity']) ?> <?= e($item['product_unit']) ?> |
                                                Unit Price: Rs. <?= e(number_format($item['unit_price'], 2)) ?>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <strong class="text-primary">Rs. <?= e(number_format($item['total_price'], 2)) ?></strong>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <!-- Order Summary -->
                                <div class="mt-4 pt-3 border-top">
                                    <div class="row">
                                        <div class="col-6">
                                            <p class="mb-1"><strong>Subtotal:</strong> Rs. <?= e(number_format($order['subtotal'], 2)) ?></p>
                                            <?php if ($order['delivery_fee'] > 0): ?>
                                                <p class="mb-1"><strong>Delivery Fee:</strong> Rs. <?= e(number_format($order['delivery_fee'], 2)) ?></p>
                                            <?php endif; ?>
                                            <?php if ($order['tax_amount'] > 0): ?>
                                                <p class="mb-1"><strong>Tax:</strong> Rs. <?= e(number_format($order['tax_amount'], 2)) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-6 text-end">
                                            <h4 class="text-success mb-0">Total: Rs. <?= e(number_format($order['total_amount'], 2)) ?></h4>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No items found for this order.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Customer & Delivery Information -->
                <div class="col-lg-4">
                    <!-- Customer Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>Customer Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>Name:</strong> <?= e($order['customer_name'] ?? 'N/A') ?></p>
                            <p class="mb-1"><strong>Email:</strong> <?= e($order['customer_email'] ?? 'N/A') ?></p>
                            <p class="mb-1"><strong>Phone:</strong> <?= e($order['customer_phone'] ?? 'N/A') ?></p>
                            <p class="mb-1"><strong>Payment Method:</strong> <?= e($order['payment_method'] ?? 'N/A') ?></p>
                            <p class="mb-0"><strong>Order Date:</strong> <?= e($order['formatted_date']) ?></p>
                        </div>
                    </div>

                    <!-- Delivery Information -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-truck me-2"></i>Delivery Information
                            </h5>
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

                            <?php if (!empty($deliveryAddress)): ?>
                                <div class="border-start border-primary border-3 ps-3">
                                    <p class="mb-1"><strong>Recipient:</strong> <?= e($deliveryAddress['name'] ?? $deliveryAddress['first_name'] . ' ' . $deliveryAddress['last_name']) ?></p>
                                    <?php if (!empty($deliveryAddress['phone'])): ?>
                                        <p class="mb-1"><strong>Phone:</strong> <?= e($deliveryAddress['phone']) ?></p>
                                    <?php endif; ?>

                                    <?php if ($order['delivery_type'] === 'home_delivery'): ?>
                                        <p class="mb-1"><strong>Address:</strong></p>
                                        <address class="mb-0 small">
                                            <?= e($deliveryAddress['address_line_1']) ?><br>
                                            <?php if (!empty($deliveryAddress['apartment'])): ?>
                                                <?= e($deliveryAddress['apartment']) ?><br>
                                            <?php endif; ?>
                                            <?= e($deliveryAddress['city']) ?>, <?= e($deliveryAddress['state']) ?> <?= e($deliveryAddress['postal_code']) ?><br>
                                            <?= e($deliveryAddress['country']) ?>
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
                                <p class="small text-muted mb-0"><?= e($order['notes']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
