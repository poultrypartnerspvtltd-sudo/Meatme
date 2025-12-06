<?php
require_once __DIR__ . '/../app/Core/Helpers.php';
/**
 * Setup Admin Database Table and Default Admin User
 */

// Database configuration
$host = 'localhost';
$dbname = 'meatme_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Setting up Admin Database...</h2>";
    
    // Create admins table
    $createAdminsTable = "
        CREATE TABLE IF NOT EXISTS `admins` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(50) NOT NULL UNIQUE,
            `email` varchar(100) NOT NULL UNIQUE,
            `password_hash` varchar(255) NOT NULL,
            `role` enum('admin','super_admin') DEFAULT 'admin',
            `full_name` varchar(100) DEFAULT NULL,
            `is_active` tinyint(1) DEFAULT 1,
            `last_login` timestamp NULL DEFAULT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";
    
    $pdo->exec($createAdminsTable);
    echo "✓ Admins table created successfully<br>";
    
    // Check if default admin exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ?");
    $stmt->execute(['admin']);
    $adminExists = $stmt->fetchColumn();
    
    if (!$adminExists) {
        // Create default admin user
        $defaultPassword = 'admin123';
        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO admins (username, email, password_hash, role, full_name, is_active) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            'admin',
            'admin@meatme.com',
            $hashedPassword,
            'super_admin',
            'System Administrator',
            1
        ]);
        
        echo "✓ Default admin user created<br>";
        echo "<strong>Username:</strong> admin<br>";
        echo "<strong>Password:</strong> admin123<br>";
    } else {
        echo "✓ Admin user already exists<br>";
    }
    
    // Create additional sample admin if needed
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ?");
    $stmt->execute(['manager']);
    $managerExists = $stmt->fetchColumn();
    
    if (!$managerExists) {
        $managerPassword = password_hash('manager123', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO admins (username, email, password_hash, role, full_name, is_active) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            'manager',
            'manager@meatme.com',
            $managerPassword,
            'admin',
            'Store Manager',
            1
        ]);
        
        echo "✓ Manager user created<br>";
        echo "<strong>Username:</strong> manager<br>";
        echo "<strong>Password:</strong> manager123<br>";
    }
    
    echo "<br><div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Admin Setup Complete!</h3>";
    echo "<p><strong>You can now access the admin panel:</strong></p>";
    echo "<ul>";
    echo "<li><a href='login.php' target='_blank'>🔐 Admin Login</a></li>";
    echo "<li><a href='index.php' target='_blank'>📊 Admin Dashboard</a></li>";
    echo "</ul>";
    echo "<p><strong>Login Credentials:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Super Admin:</strong> admin / admin123</li>";
    echo "<li><strong>Manager:</strong> manager / manager123</li>";
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
    <title>MeatMe - Admin Database Setup</title>
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
        .success { 
            color: #155724; 
            background: #d4edda; 
            padding: 10px; 
            border-radius: 5px; 
            margin: 10px 0;
        }
        .error { 
            color: #721c24; 
            background: #f8d7da; 
            padding: 10px; 
            border-radius: 5px; 
            margin: 10px 0;
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
        <h1>🐔 MeatMe Admin Database Setup</h1>
        <p>This script sets up the admin database table and creates default admin users.</p>
        <hr>
    </div>
</body>
</html>
