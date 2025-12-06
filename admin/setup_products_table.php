<?php
require_once __DIR__ . '/../app/Core/Helpers.php';
/**
 * Setup Products Table for Admin Panel
 */

// Database connection
$host = 'localhost';
$dbname = 'meatme_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Setting up Products Table...</h2>";
    
    // Check if products table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'products'");
    if ($stmt->rowCount() == 0) {
        // Create products table
        $createProductsTable = "
            CREATE TABLE `products` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `slug` varchar(255) NOT NULL,
                `description` text,
                `price` decimal(10,2) NOT NULL,
                `compare_price` decimal(10,2) DEFAULT NULL,
                `category_id` int(11) DEFAULT NULL,
                `stock_quantity` int(11) DEFAULT 0,
                `is_active` tinyint(1) DEFAULT 1,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`),
                KEY `idx_category_id` (`category_id`),
                KEY `idx_is_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($createProductsTable);
        echo "✅ Products table created successfully<br>";
    } else {
        echo "✅ Products table already exists<br>";
    }
    
    // Check if categories table exists, create if needed
    $stmt = $pdo->query("SHOW TABLES LIKE 'categories'");
    if ($stmt->rowCount() == 0) {
        $createCategoriesTable = "
            CREATE TABLE `categories` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `slug` varchar(255) NOT NULL,
                `description` text,
                `image` varchar(255) DEFAULT NULL,
                `parent_id` int(11) DEFAULT NULL,
                `sort_order` int(11) DEFAULT 0,
                `is_active` tinyint(1) DEFAULT 1,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`),
                KEY `idx_parent_id` (`parent_id`),
                KEY `idx_is_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($createCategoriesTable);
        echo "✅ Categories table created successfully<br>";
        
        // Insert sample categories
        $sampleCategories = [
            ['name' => 'Whole Chicken', 'slug' => 'whole-chicken', 'description' => 'Fresh whole chickens'],
            ['name' => 'Chicken Breast', 'slug' => 'chicken-breast', 'description' => 'Boneless chicken breast'],
            ['name' => 'Chicken Legs', 'slug' => 'chicken-legs', 'description' => 'Chicken drumsticks and thighs'],
            ['name' => 'Boneless Cuts', 'slug' => 'boneless-cuts', 'description' => 'Various boneless chicken cuts']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, is_active) VALUES (?, ?, ?, 1)");
        foreach ($sampleCategories as $category) {
            $stmt->execute([$category['name'], $category['slug'], $category['description']]);
        }
        echo "✅ Sample categories added<br>";
    } else {
        echo "✅ Categories table already exists<br>";
    }
    
    // Check if product_images table exists, create if needed
    $stmt = $pdo->query("SHOW TABLES LIKE 'product_images'");
    if ($stmt->rowCount() == 0) {
        $createProductImagesTable = "
            CREATE TABLE `product_images` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `product_id` int(11) NOT NULL,
                `image_path` varchar(500) NOT NULL,
                `alt_text` varchar(255) DEFAULT NULL,
                `is_primary` tinyint(1) DEFAULT 0,
                `sort_order` int(11) DEFAULT 0,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_product_id` (`product_id`),
                KEY `idx_is_primary` (`is_primary`),
                FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($createProductImagesTable);
        echo "✅ Product images table created successfully<br>";
    } else {
        echo "✅ Product images table already exists<br>";
    }
    
    // Add some sample products if table is empty
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $productCount = $stmt->fetch()['count'];
    
    if ($productCount == 0) {
        $sampleProducts = [
            [
                'name' => 'Fresh Whole Chicken (1.5kg)',
                'slug' => 'fresh-whole-chicken-1-5kg',
                'description' => 'Farm-fresh whole chicken, cleaned and ready to cook. Perfect for roasting or making curry.',
                'price' => 850.00,
                'compare_price' => 950.00,
                'stock_quantity' => 25
            ],
            [
                'name' => 'Chicken Breast Boneless (500g)',
                'slug' => 'chicken-breast-boneless-500g',
                'description' => 'Premium boneless chicken breast, perfect for grilling, frying, or making healthy meals.',
                'price' => 650.00,
                'compare_price' => null,
                'stock_quantity' => 30
            ],
            [
                'name' => 'Chicken Drumsticks (1kg)',
                'slug' => 'chicken-drumsticks-1kg',
                'description' => 'Juicy chicken drumsticks, great for BBQ, curry, or fried chicken.',
                'price' => 550.00,
                'compare_price' => 600.00,
                'stock_quantity' => 20
            ]
        ];
        
        $stmt = $pdo->prepare("
            INSERT INTO products (name, slug, description, price, compare_price, stock_quantity, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, 1)
        ");
        
        foreach ($sampleProducts as $product) {
            $stmt->execute([
                $product['name'],
                $product['slug'],
                $product['description'],
                $product['price'],
                $product['compare_price'],
                $product['stock_quantity']
            ]);
        }
        echo "✅ Sample products added<br>";
    }
    
    // Create uploads directory
    $uploadDir = '../assets/uploads/products/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
        echo "✅ Upload directory created: {$uploadDir}<br>";
    } else {
        echo "✅ Upload directory already exists<br>";
    }
    
    echo "<br><div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Products Setup Complete!</h3>";
    echo "<p><strong>You can now:</strong></p>";
    echo "<ul>";
    echo "<li><a href='products.php' target='_blank'>📦 View Products Page</a></li>";
    echo "<li><a href='add_product.php' target='_blank'>➕ Add New Product</a></li>";
    echo "<li><a href='/Meatme/admin/' target='_blank'>🏠 Admin Dashboard</a></li>";
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
    echo "</ul>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>MeatMe - Products Setup</title>
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
        <h1>📦 MeatMe Products Setup</h1>
        <p>This script sets up the products management system for your MeatMe admin panel.</p>
        <hr>
    </div>
</body>
</html>
