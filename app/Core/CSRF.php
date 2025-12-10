<?php

namespace App\Core;

class CSRF
{
    private static $tokenKey = 'csrf_token';
    private static $tokenExpiry = 'csrf_token_expiry';
    
    public static function generateToken()
    {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + (int)($_ENV['CSRF_TOKEN_EXPIRE'] ?? 3600); // 1 hour default
        
        Session::set(self::$tokenKey, $token);
        Session::set(self::$tokenExpiry, $expiry);
        
        return $token;
    }
    
    public static function getToken()
    {
        $token = Session::get(self::$tokenKey);
        $expiry = Session::get(self::$tokenExpiry);
        
        // Generate new token if none exists or expired
        if (!$token || !$expiry || time() > $expiry) {
            return self::generateToken();
        }
        
        return $token;
    }
    
    public static function verifyToken($token)
    {
        $sessionToken = Session::get(self::$tokenKey);
        $expiry = Session::get(self::$tokenExpiry);
        
        if (!$sessionToken || !$expiry || time() > $expiry) {
            return false;
        }
        
        return hash_equals($sessionToken, $token);
    }
    
    public static function verify($token)
    {
        return self::verifyToken($token);
    }
    
    public static function field()
    {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    public static function meta()
    {
        $token = self::getToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token) . '">';
    }
    
    public static function validate()
    {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        
        if (!$token || !self::verifyToken($token)) {
            http_response_code(419);
            die('CSRF token mismatch');
        }
        
        return true;
    }
    
    public static function middleware()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            self::validate();
        }
        
        return true;
    }
}
?>
