<?php
/**
 * Helper Functions
 * Common utility functions for the application
 */

/**
 * Escape output for safe HTML rendering
 */
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

/**
 * CSRF Token Functions
 */

/**
 * Generate CSRF token
 */
function csrf_generate_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_expiry'] = time() + 3600; // 1 hour
    return $token;
}

/**
 * Get CSRF token (generate if not exists)
 */
function csrf_get_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if token exists and is not expired
    if (isset($_SESSION['csrf_token']) && isset($_SESSION['csrf_token_expiry'])) {
        if (time() < $_SESSION['csrf_token_expiry']) {
            return $_SESSION['csrf_token'];
        }
    }
    
    // Generate new token
    return csrf_generate_token();
}

/**
 * Verify CSRF token
 */
function csrf_verify_token($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expiry'])) {
        return false;
    }
    
    if (time() > $_SESSION['csrf_token_expiry']) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF field HTML
 */
function csrf_field() {
    $token = csrf_get_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Validate CSRF token from POST request
 */
function csrf_validate() {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    
    if (!$token || !csrf_verify_token($token)) {
        http_response_code(419);
        die('CSRF token mismatch');
    }
    
    return true;
}

/**
 * Get base URL
 */
function get_base_url() {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = '/Meatme';
    return $protocol . '://' . $host . $basePath;
}

/**
 * Generate URL
 */
function url($path = '') {
    $baseUrl = get_base_url();
    $path = ltrim($path, '/');
    return $baseUrl . ($path ? '/' . $path : '');
}

/**
 * Redirect to URL
 */
function redirect($path = '', $status = 302) {
    $url = url($path);
    http_response_code($status);
    header("Location: {$url}");
    exit;
}

