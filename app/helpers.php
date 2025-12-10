<?php
/**
 * Global Helper Functions
 */

// Include email helper functions
require_once __DIR__ . '/helpers/email.php';

if (!function_exists('redirect')) {
    /**
     * Redirect to a URL within the application
     */
    function redirect($path = '', $status = 302) {
        \App\Core\Helpers::redirect($path, $status);
    }
}

if (!function_exists('url')) {
    /**
     * Generate a URL relative to the application base
     */
    function url($path = '') {
        return \App\Core\Helpers::url($path);
    }
}

if (!function_exists('asset')) {
    /**
     * Generate asset URL
     */
    function asset($path) {
        return \App\Core\Helpers::asset($path);
    }
}

if (!function_exists('base_url')) {
    /**
     * Get the base URL for the application
     */
    function base_url() {
        return \App\Core\Helpers::getBaseUrl();
    }
}

if (!function_exists('back')) {
    /**
     * Redirect back to the previous page
     */
    function back($fallback = '') {
        \App\Core\Helpers::back($fallback);
    }
}

if (!function_exists('setFlash')) {
    /**
     * Set a flash message
     */
    function setFlash($type, $message) {
        \App\Core\Session::flash($type, $message);
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate CSRF token field
     */
    function csrf_field() {
        return \App\Core\CSRF::field();
    }
}

if (!function_exists('old')) {
    /**
     * Get old input value
     */
    function old($key, $default = '') {
        return $_SESSION['old'][$key] ?? $default;
    }
}

if (!function_exists('e')) {
    /**
     * Escape output for safe HTML rendering (global alias)
     */
    function e($string) {
        return \App\Core\e($string);
    }
}
?>
