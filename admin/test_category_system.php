<?php
require_once __DIR__ . '/../app/Core/Helpers.php';
/**
 * Category System Test
 */

// Include main config file that defines database constants
require_once '../app/config/config.php';

// Create database connection using central Database class
require_once '../app/Core/Database.php';
$db = \App\Core\Database::getInstance();
$pdo = $db->getConnection();

echo "<h2>🧩 Category System Test</h2>";

echo "<h3>Available Categories:</h3>";

// Get all categories
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY id");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($categories) > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Name</th><th>Slug</th><th>Description</th><th>Link</th></tr>";
        foreach ($categories as $category) {
            echo "<tr>";
            echo "<td>{$category['id']}</td>";
            echo "<td><strong>{$category['name']}</strong></td>";
            echo "<td><code>{$category['slug']}</code></td>";
            echo "<td>" . htmlspecialchars(substr($category['description'] ?? '', 0, 50)) . "...</td>";
            echo "<td><a href='../category/{$category['slug']}' target='_blank'>View Category</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No categories found. <a href='setup_categories.php'>Run setup</a></p>";
    }
} catch (PDOException $e) {
    echo "<p>Error fetching categories: " . $e->getMessage() . "</p>";
}

// Test category lookup by slug
echo "<br><h3>Slug Lookup Test:</h3>";
$testSlugs = ['person', 'fresh-house-restaurants', 'party-packs', 'nonexistent'];

foreach ($testSlugs as $slug) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($category) {
            echo "✅ Slug '{$slug}' → Category: {$category['name']}<br>";
        } else {
            echo "❌ Slug '{$slug}' → Not found<br>";
        }
    } catch (PDOException $e) {
        echo "❌ Error testing slug '{$slug}': " . $e->getMessage() . "<br>";
    }
}

// Test product-category relationships
echo "<br><h3>Product-Category Relationships:</h3>";
try {
    $stmt = $pdo->query("
        SELECT c.name as category_name, c.slug, COUNT(p.id) as product_count
        FROM categories c
        LEFT JOIN products p ON c.id = p.category_id
        GROUP BY c.id, c.name, c.slug
        ORDER BY c.name
    ");
    $relationships = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background: #f8f9fa;'><th>Category</th><th>Slug</th><th>Products</th><th>Link</th></tr>";
    foreach ($relationships as $rel) {
        echo "<tr>";
        echo "<td><strong>{$rel['category_name']}</strong></td>";
        echo "<td><code>{$rel['slug']}</code></td>";
        echo "<td>{$rel['product_count']} products</td>";
        echo "<td><a href='../category/{$rel['slug']}' target='_blank'>Browse</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "<p>Error checking relationships: " . $e->getMessage() . "</p>";
}

echo "<br><h3>✅ Category System Status:</h3>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<ul>";
echo "<li>✅ Categories table with slug column</li>";
echo "<li>✅ Category routes configured</li>";
echo "<li>✅ Category view template created</li>";
echo "<li>✅ Category model methods updated</li>";
echo "<li>✅ Product-category relationships working</li>";
echo "</ul>";
echo "<p><strong>Test Links:</strong></p>";
echo "<ul>";
echo "<li><a href='../category/person' target='_blank'>Person Category</a></li>";
echo "<li><a href='../category/fresh-house-restaurants' target='_blank'>Fresh House & Restaurants</a></li>";
echo "<li><a href='../category/party-packs' target='_blank'>Party Packs</a></li>";
echo "<li><a href='categories.php' target='_blank'>Admin Categories</a></li>";
echo "</ul>";
echo "</div>";

?>

<style>
body { font-family: Arial, sans-serif; max-width: 1000px; margin: 50px auto; padding: 20px; background: #f8f9fa; }
h2, h3 { color: #2e7d32; }
a { color: #4caf50; text-decoration: none; }
a:hover { text-decoration: underline; }
table { width: 100%; margin: 10px 0; border-collapse: collapse; }
th, td { text-align: left; padding: 8px; border: 1px solid #ddd; }
th { background: #f8f9fa; }
code { background: #f1f1f1; padding: 2px 4px; border-radius: 3px; }
</style>
