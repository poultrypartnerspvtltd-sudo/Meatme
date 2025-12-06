<?php
require_once __DIR__ . '/../app/Core/Helpers.php';
/**
 * Setup Orders Table for Reports
 */

// Database connection
$host = 'localhost';
$dbname = 'meatme_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Setting up Orders Table for Reports...</h2>";
    
    // Check if orders table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'orders'");
    if ($stmt->rowCount() == 0) {
        echo "Creating orders table...<br>";
        
        // Create orders table
        $createOrdersTable = "
            CREATE TABLE `orders` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `order_number` varchar(50) NOT NULL,
                `total_amount` decimal(10,2) NOT NULL,
                `status` varchar(50) NOT NULL DEFAULT 'pending',
                `payment_status` varchar(50) NOT NULL DEFAULT 'pending',
                `payment_method` varchar(50) DEFAULT NULL,
                `shipping_address` text,
                `billing_address` text,
                `notes` text,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `order_number` (`order_number`),
                KEY `idx_user_id` (`user_id`),
                KEY `idx_status` (`status`),
                KEY `idx_created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($createOrdersTable);
        echo "✅ Orders table created successfully<br>";
    } else {
        echo "✅ Orders table already exists<br>";
    }
    
    // Check if order_items table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'order_items'");
    if ($stmt->rowCount() == 0) {
        echo "Creating order_items table...<br>";
        
        // Create order_items table
        $createOrderItemsTable = "
            CREATE TABLE `order_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `order_id` int(11) NOT NULL,
                `product_id` int(11) NOT NULL,
                `product_name` varchar(255) NOT NULL,
                `quantity` int(11) NOT NULL,
                `price` decimal(10,2) NOT NULL,
                `total` decimal(10,2) NOT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_order_id` (`order_id`),
                KEY `idx_product_id` (`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($createOrderItemsTable);
        echo "✅ Order items table created successfully<br>";
    } else {
        echo "✅ Order items table already exists<br>";
    }
    
    // Check current order count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM orders");
    $orderCount = $stmt->fetch()['count'];
    echo "Current orders in database: {$orderCount}<br>";
    
    // If no orders exist, create sample data
    if ($orderCount == 0) {
        echo "<br>Creating sample orders for testing...<br>";
        
        // Create sample orders for the last 3 months
        $sampleOrders = [
            // This month
            ['user_id' => 1, 'total_amount' => 1250.00, 'status' => 'completed', 'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))],
            ['user_id' => 2, 'total_amount' => 890.50, 'status' => 'completed', 'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))],
            ['user_id' => 3, 'total_amount' => 2100.00, 'status' => 'completed', 'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))],
            ['user_id' => 1, 'total_amount' => 750.00, 'status' => 'completed', 'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))],
            ['user_id' => 4, 'total_amount' => 1450.00, 'status' => 'pending', 'created_at' => date('Y-m-d H:i:s')],
            
            // Last month
            ['user_id' => 2, 'total_amount' => 980.00, 'status' => 'completed', 'created_at' => date('Y-m-d H:i:s', strtotime('-35 days'))],
            ['user_id' => 3, 'total_amount' => 1200.00, 'status' => 'completed', 'created_at' => date('Y-m-d H:i:s', strtotime('-32 days'))],
            ['user_id' => 1, 'total_amount' => 850.00, 'status' => 'completed', 'created_at' => date('Y-m-d H:i:s', strtotime('-28 days'))],
            ['user_id' => 4, 'total_amount' => 1100.00, 'status' => 'completed', 'created_at' => date('Y-m-d H:i:s', strtotime('-25 days'))],
            
            // Two months ago
            ['user_id' => 1, 'total_amount' => 650.00, 'status' => 'completed', 'created_at' => date('Y-m-d H:i:s', strtotime('-65 days'))],
            ['user_id' => 2, 'total_amount' => 920.00, 'status' => 'completed', 'created_at' => date('Y-m-d H:i:s', strtotime('-60 days'))],
            ['user_id' => 3, 'total_amount' => 1350.00, 'status' => 'completed', 'created_at' => date('Y-m-d H:i:s', strtotime('-58 days'))],
        ];
        
        $insertStmt = $pdo->prepare("
            INSERT INTO orders (user_id, order_number, total_amount, status, created_at) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($sampleOrders as $index => $order) {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            $insertStmt->execute([
                $order['user_id'],
                $orderNumber,
                $order['total_amount'],
                $order['status'],
                $order['created_at']
            ]);
        }
        
        echo "✅ Created " . count($sampleOrders) . " sample orders<br>";
    }
    
    // Create sample order items if they don't exist
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM order_items");
    $itemCount = $stmt->fetch()['count'];
    
    if ($itemCount == 0) {
        echo "Creating sample order items...<br>";
        
        // Get all orders
        $stmt = $pdo->query("SELECT id FROM orders");
        $orders = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $products = [
            ['name' => 'Fresh Whole Chicken', 'price' => 450.00],
            ['name' => 'Chicken Breast', 'price' => 650.00],
            ['name' => 'Chicken Drumsticks', 'price' => 380.00],
            ['name' => 'Chicken Wings', 'price' => 320.00],
            ['name' => 'Chicken Thighs', 'price' => 420.00]
        ];
        
        $insertItemStmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, quantity, price, total) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($orders as $orderId) {
            // Add 1-3 random products to each order
            $numItems = rand(1, 3);
            for ($i = 0; $i < $numItems; $i++) {
                $product = $products[array_rand($products)];
                $quantity = rand(1, 3);
                $total = $product['price'] * $quantity;
                
                $insertItemStmt->execute([
                    $orderId,
                    rand(1, 5), // product_id
                    $product['name'],
                    $quantity,
                    $product['price'],
                    $total
                ]);
            }
        }
        
        echo "✅ Created sample order items<br>";
    }
    
    echo "<br><div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
    echo "<h3>✅ Orders Table Setup Complete!</h3>";
    echo "<p><strong>Database is ready for reports:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Orders table with proper structure</li>";
    echo "<li>✅ Order items table for product details</li>";
    echo "<li>✅ Sample data for testing reports</li>";
    echo "</ul>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ul>";
    echo "<li><a href='reports.php' target='_blank'>📊 View Updated Reports</a></li>";
    echo "<li><a href='test_reports_data.php' target='_blank'>🧪 Test Reports Data</a></li>";
    echo "</ul>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<h3>❌ Error: " . $e->getMessage() . "</h3>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>MeatMe - Setup Orders Table</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px; 
            margin: 50px auto; 
            padding: 20px; 
            background: #f8f9fa;
        }
        h1, h2 {
            color: #2e7d32;
        }
        a {
            color: #4caf50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>🔧 MeatMe Orders Table Setup</h1>
    <p>This script creates the orders and order_items tables needed for the reports system.</p>
    <hr>
</body>
</html>
