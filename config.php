<?php
/**
 * Configuration File
 * Database connection and application settings
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'meatme_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Base URL
define('BASE_URL', 'http://localhost/Meatme/');

// Create mysqli connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

// Set charset to utf8
$mysqli->set_charset("utf8");

// Also provide PDO connection for backward compatibility during transition
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // PDO connection failed, but mysqli is primary
    $pdo = null;
}

