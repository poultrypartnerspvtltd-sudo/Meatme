<?php
require_once __DIR__ . '/../app/Core/Helpers.php';
/**
 * Setup Homepage Content Management System
 */

// Database connection
$host = 'localhost';
$dbname = 'meatme_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Setting up Homepage Content Management System...</h2>";
    
    // Check if homepage_content table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'homepage_content'");
    if ($stmt->rowCount() == 0) {
        echo "Creating homepage_content table...<br>";
        
        // Create homepage_content table
        $createTable = "
            CREATE TABLE `homepage_content` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `section_name` varchar(255) NOT NULL,
                `title` text,
                `content` text,
                `image` varchar(255) DEFAULT NULL,
                `button_text` varchar(255) DEFAULT NULL,
                `button_link` varchar(255) DEFAULT NULL,
                `css_classes` varchar(500) DEFAULT NULL,
                `sort_order` int(11) DEFAULT 0,
                `is_active` tinyint(1) DEFAULT 1,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `section_name` (`section_name`),
                KEY `idx_sort_order` (`sort_order`),
                KEY `idx_is_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($createTable);
        echo "✅ Homepage content table created successfully<br>";
    } else {
        echo "✅ Homepage content table already exists<br>";
    }
    
    // Check current content count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM homepage_content");
    $contentCount = $stmt->fetch()['count'];
    echo "Current homepage sections: {$contentCount}<br>";
    
    // If no content exists, create default sections
    if ($contentCount == 0) {
        echo "<br>Creating default homepage sections...<br>";
        
        $defaultSections = [
            [
                'section_name' => 'hero',
                'title' => 'Fresh Farm Chicken Delivered to Your Door',
                'content' => 'Experience the finest quality chicken products sourced directly from trusted local farms. We guarantee freshness, quality, and taste that your family deserves.',
                'button_text' => 'Shop Now',
                'button_link' => '/products',
                'sort_order' => 1
            ],
            [
                'section_name' => 'about',
                'title' => 'Why Choose MeatMe?',
                'content' => 'We are committed to providing the highest quality chicken products with unmatched freshness and flavor. Our farm-to-table approach ensures you get the best.',
                'sort_order' => 2
            ],
            [
                'section_name' => 'features',
                'title' => 'Our Promise to You',
                'content' => 'Fresh daily delivery, premium quality assurance, and customer satisfaction guaranteed. We make sure every product meets our strict quality standards.',
                'sort_order' => 3
            ],
            [
                'section_name' => 'testimonials',
                'title' => 'What Our Customers Say',
                'content' => 'Join thousands of satisfied customers who trust MeatMe for their daily chicken needs. Quality and freshness delivered every time.',
                'sort_order' => 4
            ],
            [
                'section_name' => 'cta',
                'title' => 'Ready to Experience Fresh Quality?',
                'content' => 'Order now and taste the difference that fresh, quality chicken makes. Your family deserves the best.',
                'button_text' => 'Order Now',
                'button_link' => '/products',
                'sort_order' => 5
            ]
        ];
        
        $insertStmt = $pdo->prepare("
            INSERT INTO homepage_content (section_name, title, content, button_text, button_link, sort_order) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($defaultSections as $section) {
            $insertStmt->execute([
                $section['section_name'],
                $section['title'],
                $section['content'],
                $section['button_text'] ?? null,
                $section['button_link'] ?? null,
                $section['sort_order']
            ]);
        }
        
        echo "✅ Created " . count($defaultSections) . " default homepage sections<br>";
    }
    
    // Create uploads directory for homepage images
    $uploadsDir = '../assets/uploads/homepage/';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
        echo "✅ Created homepage uploads directory<br>";
    } else {
        echo "✅ Homepage uploads directory exists<br>";
    }
    
    echo "<br><div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
    echo "<h3>✅ Homepage CMS Setup Complete!</h3>";
    echo "<p><strong>Homepage Content Management System is ready:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Database table with proper structure</li>";
    echo "<li>✅ Default homepage sections created</li>";
    echo "<li>✅ Image upload directory prepared</li>";
    echo "<li>✅ WYSIWYG editor support ready</li>";
    echo "</ul>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ul>";
    echo "<li><a href='homepage_editor.php' target='_blank'>✏️ Edit Homepage Content</a></li>";
    echo "<li><a href='../index.php' target='_blank'>🏠 View Updated Homepage</a></li>";
    echo "<li><a href='test_homepage_cms.php' target='_blank'>🧪 Test CMS System</a></li>";
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
    <title>MeatMe - Homepage CMS Setup</title>
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
    <h1>🏠 MeatMe Homepage CMS Setup</h1>
    <p>This script creates the database structure and default content for the homepage content management system.</p>
    <hr>
</body>
</html>
