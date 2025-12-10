<?php

namespace App\Middleware;

use App\Core\Auth;

class AdminAuth
{
    public function handle()
    {
        if (Auth::guest() || !Auth::isAdmin()) {
            \App\Core\Helpers::redirect('admin/login');
            exit;
        }
        
        return true;
    }
}
?>
