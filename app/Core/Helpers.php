<?php

namespace App\Core;

class Helpers
{
    /**
     * Get the base URL for the application
     */
    public static function getBaseUrl()
    {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];

        // Get the script directory path
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = dirname($scriptName);

        // Handle different deployment scenarios
        if ($basePath === '/' || $basePath === '\\') {
            $basePath = '';
        }

        // For MeatMe project, ensure we're in the correct directory
        if (strpos($scriptName, '/Meatme/') !== false) {
            // We're in the MeatMe subdirectory
            $basePath = '/Meatme';
        } elseif (basename(dirname(__DIR__, 2)) === 'Meatme') {
            // We're running from within MeatMe directory structure
            // Check if we're using PHP built-in server (for testing)
            if (php_sapi_name() === 'cli-server') {
                $basePath = '';
            } else {
                $basePath = '/Meatme';
            }
        }

        return $protocol . '://' . $host . $basePath;
    }

    /**
     * Generate a URL relative to the application base
     */
    public static function url($path = '')
    {
        $baseUrl = self::getBaseUrl();
        $path = ltrim($path, '/');

        return $baseUrl . ($path ? '/' . $path : '');
    }

    /**
     * Redirect to a URL within the application
     */
    public static function redirect($path = '', $status = 302)
    {
        $url = self::url($path);

        http_response_code($status);
        header("Location: {$url}");
        exit;
    }

    /**
     * Redirect back to the previous page or a fallback URL
     */                                                                                                                                                                                                                                                        
    public static function back($fallback = '')
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? null;

        // Check if referer is within our application
        if ($referer && strpos($referer, self::getBaseUrl()) === 0) {
            header("Location: {$referer}");
        } else {
            self::redirect($fallback);
        }

        exit;
    }

    /**
     * Get the current URL
     */
    public static function currentUrl()
    {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];

        return $protocol . '://' . $host . $uri;
    }

    /**
     * Check if the current request is HTTPS
     */
    public static function isHttps()
    {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    }

    /**
     * Generate asset URL
     */
    public static function asset($path)
    {
        return self::url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists(__NAMESPACE__ . '\e')) {
    /**
     * Escape output for safe HTML rendering
     */
    function e($string)
    {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}
