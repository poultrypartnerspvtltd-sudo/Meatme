<?php

namespace App\Core;

class Auth
{
    private static $user = null;
    
    public static function attempt($email, $password)
    {
        $db = Database::getInstance();
        
        $user = $db->fetch(
            "SELECT * FROM users WHERE email = :email AND status = 'active' LIMIT 1",
            ['email' => $email]
        );
        
        if ($user && password_verify($password, $user['password'])) {
            self::login($user);
            return true;
        }
        
        return false;
    }
    
    public static function login($user)
    {
        Session::set('user_id', $user['id']);
        Session::set('user_email', $user['email']);
        Session::set('user_name', $user['name']);
        Session::set('user_role', $user['role'] ?? 'user');
        Session::regenerate();
        
        self::$user = $user;
        
        // Update last login
        $db = Database::getInstance();
        $db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $user['id']]);
    }
    
    public static function logout()
    {
        Session::destroy();
        self::$user = null;
    }
    
    public static function check()
    {
        return Session::has('user_id');
    }
    
    public static function guest()
    {
        return !self::check();
    }
    
    public static function user()
    {
        if (self::$user === null && self::check()) {
            $db = Database::getInstance();
            self::$user = $db->fetch(
                "SELECT * FROM users WHERE id = :id LIMIT 1",
                ['id' => Session::get('user_id')]
            );
            
            // Remove password from user data
            if (self::$user) {
                unset(self::$user['password']);
            }
        }
        
        return self::$user;
    }
    
    public static function id()
    {
        return Session::get('user_id');
    }
    
    public static function isAdmin()
    {
        $user = self::user();
        return $user && in_array($user['role'], ['admin', 'super_admin']);
    }
    
    public static function isSuperAdmin()
    {
        $user = self::user();
        return $user && $user['role'] === 'super_admin';
    }
    
    public static function hasRole($role)
    {
        $user = self::user();
        return $user && $user['role'] === $role;
    }
    
    public static function register($data)
    {
        error_log("Auth::register called with data: " . json_encode(array_keys($data)));
        $db = Database::getInstance();

        // Check if email already exists
        $existingUser = $db->fetch(
            "SELECT id FROM users WHERE email = :email LIMIT 1",
            ['email' => $data['email']]
        );

        if ($existingUser) {
            error_log("Email already exists: " . $data['email']);
            return false;
        }
        
        // Filter and prepare user data for database
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => 'user',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Add optional fields if they exist
        if (isset($data['phone'])) {
            $userData['phone'] = $data['phone'];
        }
        
        $userId = $db->insert('users', $userData);
        error_log("Database insert result: " . ($userId ? "Success (ID: $userId)" : "Failed"));

        if ($userId) {
            $user = $db->fetch("SELECT * FROM users WHERE id = :id", ['id' => $userId]);
            error_log("User created successfully: " . $user['email']);
            self::login($user);
            return true;
        }

        error_log("Registration failed - could not insert user");
        return false;
    }
    
    public static function resetPassword($email)
    {
        $db = Database::getInstance();
        
        $user = $db->fetch(
            "SELECT * FROM users WHERE email = :email AND status = 'active' LIMIT 1",
            ['email' => $email]
        );
        
        if (!$user) {
            return false;
        }
        
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store reset token
        $db->query(
            "INSERT INTO password_resets (email, token, expires_at, created_at) 
             VALUES (:email, :token, :expires, :created) 
             ON DUPLICATE KEY UPDATE token = :token, expires_at = :expires, created_at = :created",
            [
                'email' => $email,
                'token' => $token,
                'expires' => $expires,
                'created' => date('Y-m-d H:i:s')
            ]
        );
        
        return $token;
    }
    
    public static function verifyResetToken($token)
    {
        $db = Database::getInstance();
        
        $reset = $db->fetch(
            "SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW() LIMIT 1",
            ['token' => $token]
        );
        
        return $reset;
    }
    
    public static function updatePassword($token, $password)
    {
        $db = Database::getInstance();
        
        $reset = self::verifyResetToken($token);
        if (!$reset) {
            return false;
        }
        
        // Update password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updated = $db->update(
            'users',
            ['password' => $hashedPassword, 'updated_at' => date('Y-m-d H:i:s')],
            'email = :email',
            ['email' => $reset['email']]
        );
        
        if ($updated) {
            // Delete reset token
            $db->delete('password_resets', 'token = :token', ['token' => $token]);
            return true;
        }
        
        return false;
    }
}
?>
