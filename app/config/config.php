<?php
/**
 * Application Configuration with Database Constants
 * This file defines constants for database connection
 */

// Load Composer autoload if available (includes PHPMailer)
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

// Define database constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'meatme_db'); // replace if your database name differs
define('DB_USER', 'root');
define('DB_PASS', '');

// Define base URL
define('BASE_URL', 'http://localhost/Meatme/');
?>
