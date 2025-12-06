<?php
require_once __DIR__ . '/../app/Core/Helpers.php';
/**
 * Setup Contact Messages Table
 */

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

echo "<h2>Setting up Contact Messages Table</h2>";

// Database connection
$host = 'localhost';
$dbname = 'meatme_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection successful<br>";
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

try {
    // Create contact_messages table
    $sql = "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        subject VARCHAR(255),
        message TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_created_at (created_at),
        INDEX idx_is_read (is_read)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "✅ Contact messages table created successfully<br>";
    
    // Check if table exists and show structure
    $stmt = $pdo->query("DESCRIBE contact_messages");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<br><h3>Table Structure:</h3>";
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f8f9fa;'><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td><strong>" . $column['Field'] . "</strong></td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><h3>✅ Setup Complete!</h3>";
    echo "<p>The contact messages table has been created successfully. Users can now:</p>";
    echo "<ul>";
    echo "<li>✅ Submit contact form messages</li>";
    echo "<li>✅ Messages will be stored in the database</li>";
    echo "<li>✅ Admin can view and manage messages</li>";
    echo "<li>✅ Email notifications can be implemented</li>";
    echo "</ul>";
    
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ul>";
    echo "<li><a href='../contact'>Test Contact Form</a></li>";
    echo "<li><a href='contact_messages.php'>View Contact Messages (if created)</a></li>";
    echo "<li><a href='../index.php'>Return to Website</a></li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "❌ Error creating table: " . $e->getMessage() . "<br>";
}

?>

<style>
body { 
    font-family: Arial, sans-serif; 
    max-width: 800px; 
    margin: 50px auto; 
    padding: 20px; 
    background: #f8f9fa;
}
h1, h2, h3 {
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
