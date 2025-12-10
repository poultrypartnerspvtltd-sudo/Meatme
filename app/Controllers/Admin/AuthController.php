<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\CSRF;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Redirect if already logged in as admin
        if (Auth::check() && Auth::isAdmin()) {
            $this->redirect('/admin/dashboard');
        }
        
        $data = [
            'title' => 'Admin Login'
        ];
        
        $this->render('admin.login', $data);
    }
    
    public function login()
    {
        // Validate CSRF token
        if (!CSRF::verify($this->input('csrf_token'))) {
            Session::flash('error', 'Invalid security token. Please try again.');
            $this->back();
        }
        
        $email = $this->input('email');
        $password = $this->input('password');
        $remember = $this->input('remember');
        
        // Validate input
        if (empty($email) || empty($password)) {
            Session::flash('error', 'Please enter both email and password.');
            $this->back();
        }
        
        // Attempt login
        if (Auth::attempt($email, $password, $remember)) {
            // Check if user is admin
            if (Auth::isAdmin()) {
                Session::flash('success', 'Welcome back, Admin!');
                $this->redirect('/admin/dashboard');
            } else {
                // Not an admin, logout and show error
                Auth::logout();
                Session::flash('error', 'Access denied. Admin privileges required.');
                $this->back();
            }
        } else {
            Session::flash('error', 'Invalid email or password.');
            $this->back();
        }
    }
    
    public function logout()
    {
        Auth::logout();
        Session::flash('success', 'You have been logged out successfully.');
        $this->redirect('/admin/login');
    }
}
?>
