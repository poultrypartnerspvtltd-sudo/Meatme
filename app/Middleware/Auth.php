<?php

namespace App\Middleware;

use App\Core\Auth as AuthCore;

class Auth
{
    public function handle()
    {
        if (AuthCore::guest()) {
            // Store intended URL for redirect after login
            if (!empty($_SERVER['REQUEST_URI'])) {
                $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            }
            
            \App\Core\Helpers::redirect('login');
            exit;
        }
        
        return true;
    }
}
?>
