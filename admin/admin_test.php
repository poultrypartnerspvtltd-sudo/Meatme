<?php
/**
 * Admin Self-Test Page
 * Tests database connection, table existence, and data counts
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Try to load helpers if available (non-critical)
if (file_exists(__DIR__ . '/../app/helpers.php')) {
    require_once __DIR__ . '/../app/helpers.php';
}

// Check if admin is logged in (optional - allow access for testing)
$requireAuth = false; // Set to true if you want to require login

if ($requireAuth && (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true)) {
    header('Location: login.php');
    exit;
}

$page_title = 'Admin System Test';

// Database configuration
$host = 'localhost';
$dbname = 'meatme_db';
$username = 'root';
$password = '';

$testResults = [];
$overallStatus = true;

// Test 1: Database Connection
$dbConnected = false;
$pdo = null;
$connectionError = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbConnected = true;
    $testResults[] = [
        'test' => 'Database Connection',
        'status' => 'success',
        'message' => 'Successfully connected to database: ' . $dbname
    ];
} catch (PDOException $e) {
    $connectionError = $e->getMessage();
    $overallStatus = false;
    $testResults[] = [
        'test' => 'Database Connection',
        'status' => 'error',
        'message' => 'Failed to connect: ' . htmlspecialchars($connectionError)
    ];
}

// Test 2-4: Table Existence and Data Counts
if ($dbConnected && $pdo) {
    $tablesToCheck = [
        'products' => 'Products',
        'categories' => 'Categories',
        'orders' => 'Orders'
    ];
    
    foreach ($tablesToCheck as $tableName => $displayName) {
        try {
            // Check if table exists
            $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
            $tableExists = $stmt->rowCount() > 0;
            
            if ($tableExists) {
                // Get row count
                $countStmt = $pdo->query("SELECT COUNT(*) as count FROM `$tableName`");
                $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
                $rowCount = $countResult['count'] ?? 0;
                
                $testResults[] = [
                    'test' => "$displayName Table",
                    'status' => 'success',
                    'message' => "Table exists with $rowCount record(s)"
                ];
            } else {
                $overallStatus = false;
                $testResults[] = [
                    'test' => "$displayName Table",
                    'status' => 'error',
                    'message' => "Table '$tableName' does not exist"
                ];
            }
        } catch (PDOException $e) {
            $overallStatus = false;
            $testResults[] = [
                'test' => "$displayName Table",
                'status' => 'error',
                'message' => 'Error checking table: ' . htmlspecialchars($e->getMessage())
            ];
        }
    }
    
    // Test 5: Additional critical tables
    $additionalTables = [
        'users' => 'Users',
        'cart_items' => 'Cart Items',
        'order_items' => 'Order Items'
    ];
    
    foreach ($additionalTables as $tableName => $displayName) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
            $tableExists = $stmt->rowCount() > 0;
            
            if ($tableExists) {
                $countStmt = $pdo->query("SELECT COUNT(*) as count FROM `$tableName`");
                $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
                $rowCount = $countResult['count'] ?? 0;
                
                $testResults[] = [
                    'test' => "$displayName Table",
                    'status' => 'success',
                    'message' => "Table exists with $rowCount record(s)"
                ];
            } else {
                $testResults[] = [
                    'test' => "$displayName Table",
                    'status' => 'warning',
                    'message' => "Table '$tableName' does not exist (may be optional)"
                ];
            }
        } catch (PDOException $e) {
            $testResults[] = [
                'test' => "$displayName Table",
                'status' => 'warning',
                'message' => 'Error checking table: ' . htmlspecialchars($e->getMessage())
            ];
        }
    }
} else {
    // If database connection failed, mark all table tests as skipped
    $testResults[] = [
        'test' => 'Products Table',
        'status' => 'error',
        'message' => 'Skipped - Database connection failed'
    ];
    $testResults[] = [
        'test' => 'Categories Table',
        'status' => 'error',
        'message' => 'Skipped - Database connection failed'
    ];
    $testResults[] = [
        'test' => 'Orders Table',
        'status' => 'error',
        'message' => 'Skipped - Database connection failed'
    ];
}

// Count successful tests
$successCount = count(array_filter($testResults, function($result) {
    return $result['status'] === 'success';
}));
$totalTests = count($testResults);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Admin System Test') ?> - MeatMe Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    .test-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem;
    }
    .test-header {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        color: white;
        padding: 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        text-align: center;
    }
    .test-table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .test-table table {
        margin: 0;
    }
    .test-table thead {
        background: #f8f9fa;
    }
    .status-success {
        color: #28a745;
        font-weight: bold;
    }
    .status-error {
        color: #dc3545;
        font-weight: bold;
    }
    .status-warning {
        color: #ffc107;
        font-weight: bold;
    }
    .status-icon {
        font-size: 1.2rem;
        margin-right: 0.5rem;
    }
    .summary-box {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
        margin-top: 2rem;
        text-align: center;
    }
    .summary-box.success {
        background: #d4edda;
        border: 2px solid #28a745;
    }
    .summary-box.error {
        background: #f8d7da;
        border: 2px solid #dc3545;
    }
    .btn-rerun {
        margin-top: 1rem;
    }
    .timestamp {
        color: #6c757d;
        font-size: 0.9rem;
        margin-top: 1rem;
    }
</style>
</head>
<body style="background-color: #f8f9fa; padding: 2rem 0;">
<div class="test-container">
    <div class="test-header">
        <h1 class="mb-2">
            <i class="fas fa-vial me-2"></i>Admin System Health Check
        </h1>
        <p class="mb-0">Database Connection & Table Verification</p>
    </div>

    <div class="test-table">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width: 30%;">Test Item</th>
                    <th style="width: 15%;">Status</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($testResults as $result): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($result['test']) ?></strong></td>
                        <td>
                            <?php if ($result['status'] === 'success'): ?>
                                <span class="status-success">
                                    <i class="fas fa-check-circle status-icon"></i>✅ Pass
                                </span>
                            <?php elseif ($result['status'] === 'error'): ?>
                                <span class="status-error">
                                    <i class="fas fa-times-circle status-icon"></i>❌ Fail
                                </span>
                            <?php else: ?>
                                <span class="status-warning">
                                    <i class="fas fa-exclamation-triangle status-icon"></i>⚠️ Warning
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($result['message']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="summary-box <?= $overallStatus ? 'success' : 'error' ?>">
        <h4 class="mb-3">
            <?php if ($overallStatus): ?>
                <i class="fas fa-check-circle text-success me-2"></i>System Health: Good
            <?php else: ?>
                <i class="fas fa-exclamation-triangle text-danger me-2"></i>System Health: Issues Detected
            <?php endif; ?>
        </h4>
        <p class="mb-2">
            <strong><?= $successCount ?> of <?= $totalTests ?></strong> tests passed successfully.
        </p>
        <?php if (!$overallStatus): ?>
            <p class="mb-0 text-danger">
                <i class="fas fa-info-circle me-2"></i>Please review the failed tests above and fix any issues.
            </p>
        <?php endif; ?>
        
        <a href="admin_test.php" class="btn btn-primary btn-rerun">
            <i class="fas fa-sync-alt me-2"></i>Re-run Test
        </a>
        
        <div class="timestamp">
            <i class="far fa-clock me-2"></i>
            ✅ Admin Self-Test completed on <?= date('Y-m-d H:i:s') ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

