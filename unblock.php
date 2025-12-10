<?php
/**
 * Temporary unblock utility.
 * Usage (browser): /Meatme/unblock.php?id=123&token=SECRET
 * Usage (CLI): php unblock.php 123
 *
 * IMPORTANT: This file is intentionally minimal. Remove it after use.
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/app/Core/Database.php';

// Load env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

if (php_sapi_name() === 'cli') {
    $id = $argv[1] ?? null;
    if (!$id) {
        echo "Usage: php unblock.php <user_id>\n";
        exit(1);
    }
} else {
    $id = $_GET['id'] ?? null;
    $token = $_GET['token'] ?? null;

    $expected = $_ENV['ADMIN_UNLOCK_TOKEN'] ?? null;
    if (!$expected || $token !== $expected) {
        http_response_code(403);
        echo "Forbidden: missing or invalid token";
        exit;
    }
}

if (!$id) {
    echo "No user id provided\n";
    exit(1);
}

$db = App\Core\Database::getInstance();
try {
    $db->update('users', ['status' => 'active'], 'id = :id', ['id' => $id]);
    echo "User {$id} set to active. Remove this file after use.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
