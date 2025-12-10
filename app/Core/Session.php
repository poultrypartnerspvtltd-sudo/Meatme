<?php

namespace App\Core;

use App\Services\Cart;

class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Configure secure session settings
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_samesite', 'Strict');
            
            // Enable secure flag if using HTTPS
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', 1);
            }
            
            // Set session lifetime (1 hour)
            ini_set('session.gc_maxlifetime', 3600);
            ini_set('session.cookie_lifetime', 3600);
            
            session_start();
        }
    }
    
    public static function set($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    public static function has($key)
    {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    public static function remove($key)
    {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    public static function flash($key, $value = null)
    {
        self::start();
        
        if ($value === null) {
            // Get flash message
            $message = $_SESSION['flash'][$key] ?? null;
            if (isset($_SESSION['flash'][$key])) {
                unset($_SESSION['flash'][$key]);
            }
            return $message;
        } else {
            // Set flash message
            $_SESSION['flash'][$key] = $value;
        }
    }
    
    public static function hasFlash($key)
    {
        self::start();
        return isset($_SESSION['flash'][$key]);
    }
    
    public static function all()
    {
        self::start();
        return $_SESSION;
    }
    
    public static function clear()
    {
        self::start();
        $_SESSION = [];
    }
    
    public static function destroy()
    {
        self::start();
        session_destroy();
        $_SESSION = [];
    }
    
    public static function regenerate($deleteOld = true)
    {
        self::start();
        session_regenerate_id($deleteOld);
    }
    
    public static function getId()
    {
        self::start();
        return session_id();
    }
    
    public static function setId($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_id($id);
            session_start();
        }
    }
    
    public static function isActive()
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }
    
    // Shopping cart methods
    public static function addToCart($productId, $quantity = 1, $price = 0)
    {
        self::start();
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = [
                'quantity' => $quantity,
                'price' => $price,
                'added_at' => time()
            ];
        }
    }
    
    public static function updateCartItem($productId, $quantity)
    {
        self::start();
        
        if (isset($_SESSION['cart'][$productId])) {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$productId]);
            } else {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            }
        }
    }
    
    public static function removeFromCart($productId)
    {
        self::start();
        
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
    }
    
    public static function getCart()
    {
        self::start();
        return $_SESSION['cart'] ?? [];
    }
    
    public static function getCartCount()
    {
        $cart = self::getCart();
        $count = 0;
        
        foreach ($cart as $item) {
            $count += $item['quantity'];
        }
        
        return $count;
    }
    
    public static function getCartTotal()
    {
        $cart = self::getCart();
        $total = 0;
        
        foreach ($cart as $item) {
            $total += $item['quantity'] * $item['price'];
        }
        
        return $total;
    }
    
    public static function clearCart()
    {
        self::start();
        unset($_SESSION['cart']);
    }
    
    // Form validation methods
    public static function setErrors($errors)
    {
        self::start();
        $_SESSION['errors'] = $errors;
    }
    
    public static function hasError($field)
    {
        self::start();
        return isset($_SESSION['errors'][$field]);
    }
    
    public static function getError($field)
    {
        self::start();
        return $_SESSION['errors'][$field] ?? [];
    }
    
    public static function getErrors()
    {
        self::start();
        return $_SESSION['errors'] ?? [];
    }
    
    public static function clearErrors()
    {
        self::start();
        unset($_SESSION['errors']);
    }
    
    public static function setOldInput($data)
    {
        self::start();
        $_SESSION['old_input'] = $data;
    }
    
    public static function old($field, $default = '')
    {
        self::start();
        return $_SESSION['old_input'][$field] ?? $default;
    }
    
    public static function getOldInput()
    {
        self::start();
        return $_SESSION['old_input'] ?? [];
    }
    
    public static function clearOldInput()
    {
        self::start();
        unset($_SESSION['old_input']);
    }
    
    public static function flashInput($data)
    {
        self::setOldInput($data);
    }
}
?>
