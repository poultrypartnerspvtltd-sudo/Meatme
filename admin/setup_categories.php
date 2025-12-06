<?php
require_once __DIR__ . '/../app/Core/Helpers.php';
/**
 * Categories Management System Setup
 * Creates categories table and updates products table
 */

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

echo "<h2>Categories Management System Setup</h2>";

// Include database config
require_once '../config/database.php';

// Load database configuration
$dbConfig = require '../config/database.php';

// Connect to database
try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}",
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['options']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection successful<br><br>";
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

// Create categories table
echo "<h3>Creating Categories Table...</h3>";
$createCategoriesTable = "
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $pdo->exec($createCategoriesTable);
    echo "✅ Categories table created successfully<br>";
} catch (PDOException $e) {
    echo "❌ Error creating categories table: " . $e->getMessage() . "<br>";
}

// Add category_id column to products table
echo "<br><h3>Updating Products Table...</h3>";
$alterProductsTable = "
ALTER TABLE products
ADD COLUMN category_id INT NULL,
ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL";

try {
    $pdo->exec($alterProductsTable);
    echo "✅ Products table updated with category_id column<br>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "ℹ️ Category column already exists in products table<br>";
    } else {
        echo "❌ Error updating products table: " . $e->getMessage() . "<br>";
    }
}

// Insert default categories
echo "<br><h3>Inserting Default Categories...</h3>";
$defaultCategories = [
    ['Person', 'Individual consumer products for personal use'],
    ['Fresh House, Restaurants', 'Fresh products for households and restaurant supplies'],
    ['Party Packs', 'Bulk packs and party-sized portions']
];

// Function to generate slug
function generateSlug($name) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
}

$insertStmt = $pdo->prepare("INSERT IGNORE INTO categories (name, slug, description) VALUES (?, ?, ?)");

foreach ($defaultCategories as $category) {
    $slug = generateSlug($category[0]);
    try {
        $insertStmt->execute([$category[0], $slug, $category[1]]);
        echo "✅ Category '{$category[0]}' added with slug '{$slug}'<br>";
    } catch (PDOException $e) {
        echo "ℹ️ Category '{$category[0]}' may already exist<br>";
    }
}

// Show current categories
echo "<br><h3>Current Categories:</h3>";
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY id");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($categories) > 0) {
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Name</th><th>Description</th><th>Created</th></tr>";
        foreach ($categories as $category) {
            echo "<tr>";
            echo "<td>{$category['id']}</td>";
            echo "<td><strong>{$category['name']}</strong></td>";
            echo "<td>{$category['description']}</td>";
            echo "<td>" . date('M j, Y H:i', strtotime($category['created_at'])) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No categories found<br>";
    }
} catch (PDOException $e) {
    echo "❌ Error fetching categories: " . $e->getMessage() . "<br>";
}

// Show products table structure
echo "<br><h3>Products Table Structure:</h3>";
try {
    $stmt = $pdo->query("DESCRIBE products");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' cellpadding='8' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background: #f8f9fa;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Check if category_id column exists
    $hasCategoryColumn = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'category_id') {
            $hasCategoryColumn = true;
            break;
        }
    }

    if ($hasCategoryColumn) {
        echo "✅ category_id column found in products table<br>";
    } else {
        echo "❌ category_id column not found in products table<br>";
    }

} catch (PDOException $e) {
    echo "❌ Error checking products table: " . $e->getMessage() . "<br>";
}

echo "<br><h3>✅ Setup Complete!</h3>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h4>Categories Management System Ready!</h4>";
echo "<p><strong>Features Implemented:</strong></p>";
echo "<ul>";
echo "<li>✅ Categories table created</li>";
echo "<li>✅ Products table updated with category_id</li>";
echo "<li>✅ 3 default categories added</li>";
echo "<li>✅ Foreign key relationship established</li>";
echo "</ul>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li><a href='categories.php'>Manage Categories</a></li>";
echo "<li><a href='add_product.php'>Add Product with Category</a></li>";
echo "<li><a href='../products'>View Products by Category</a></li>";
echo "</ul>";
echo "</div>";

?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1000px;
    margin: 50px auto;
    padding: 20px;
    background: #f8f9fa;
}
h2, h3 {
    color: #2e7d32;
}
a {
    color: #4caf50;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
table {
    width: 100%;
    margin: 10px 0;
}
th, td {
    text-align: left;
    padding: 8px;
}
th {
    background: #f8f9fa;
}
</style>
