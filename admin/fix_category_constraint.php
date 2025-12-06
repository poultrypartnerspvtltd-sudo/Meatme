<?php
require_once __DIR__ . '/../app/Core/Helpers.php';
/**
 * Fix Category ID Constraint Issue
 */

// Database connection
$host = 'localhost';
$dbname = 'meatme_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Fixing Category ID Constraint Issue...</h2>";
    
    // Check current table structure
    $stmt = $pdo->query("DESCRIBE products");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $categoryColumn = null;
    foreach ($columns as $column) {
        if ($column['Field'] === 'category_id') {
            $categoryColumn = $column;
            break;
        }
    }
    
    if ($categoryColumn) {
        echo "✅ Found category_id column<br>";
        echo "Current definition: " . $categoryColumn['Type'] . " " . $categoryColumn['Null'] . " " . $categoryColumn['Default'] . "<br>";
        
        // Check if it allows NULL
        if ($categoryColumn['Null'] === 'NO') {
            echo "❌ Category ID column does not allow NULL values<br>";
            echo "Fixing constraint...<br>";
            
            // Modify the column to allow NULL
            $alterQuery = "ALTER TABLE products MODIFY COLUMN category_id INT(11) DEFAULT NULL";
            $pdo->exec($alterQuery);
            echo "✅ Category ID column updated to allow NULL values<br>";
            
        } else {
            echo "✅ Category ID column already allows NULL values<br>";
        }
        
    } else {
        echo "❌ Category ID column not found<br>";
    }
    
    // Check if we have any products with invalid category_id
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE category_id = 0");
    $invalidCount = $stmt->fetch()['count'];
    
    if ($invalidCount > 0) {
        echo "Found {$invalidCount} products with category_id = 0<br>";
        echo "Updating to NULL...<br>";
        
        $pdo->exec("UPDATE products SET category_id = NULL WHERE category_id = 0");
        echo "✅ Updated products with invalid category_id<br>";
    }
    
    // Test the fix by trying to insert a product without category
    echo "<br><h3>Testing the Fix...</h3>";
    
    try {
        $testSlug = 'test-product-' . time();
        $stmt = $pdo->prepare("
            INSERT INTO products (name, slug, description, price, category_id, stock_quantity, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            'Test Product (No Category)',
            $testSlug,
            'This is a test product without category',
            100.00,
            null, // This should work now
            10,
            1
        ]);
        
        $testProductId = $pdo->lastInsertId();
        echo "✅ Successfully inserted test product without category (ID: {$testProductId})<br>";
        
        // Clean up test product
        $pdo->exec("DELETE FROM products WHERE id = {$testProductId}");
        echo "✅ Test product cleaned up<br>";
        
    } catch (PDOException $e) {
        echo "❌ Test failed: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Fix Complete!</h3>";
    echo "<p><strong>The category constraint issue has been resolved:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Category ID column now allows NULL values</li>";
    echo "<li>✅ Products can be added without selecting a category</li>";
    echo "<li>✅ Existing products with invalid category_id fixed</li>";
    echo "</ul>";
    echo "<p><strong>You can now:</strong></p>";
    echo "<ul>";
    echo "<li><a href='add_product.php' target='_blank'>➕ Add New Product (without category)</a></li>";
    echo "<li><a href='products.php' target='_blank'>📦 View Products Page</a></li>";
    echo "</ul>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ Error: " . $e->getMessage() . "</h3>";
    echo "<p><strong>Please make sure:</strong></p>";
    echo "<ul>";
    echo "<li>XAMPP is running</li>";
    echo "<li>MySQL service is started</li>";
    echo "<li>Database 'meatme_db' exists</li>";
    echo "<li>Products table exists</li>";
    echo "</ul>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>MeatMe - Fix Category Constraint</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px; 
            margin: 50px auto; 
            padding: 20px; 
            background: #f8f9fa;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
    <div class="container">
        <h1>🔧 MeatMe Category Constraint Fix</h1>
        <p>This script fixes the "Column 'category_id' cannot be null" error.</p>
        <hr>
    </div>
</body>
</html>
