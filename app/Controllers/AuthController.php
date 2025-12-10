<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\CSRF;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }
        
        $this->render('auth.login', ['title' => 'Login']);
    }
    
    public function login()
    {
        $email = $this->input('email');
        $password = $this->input('password');
        $remember = $this->input('remember');
        
        // Validate input
        $errors = $this->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', $this->input());
            $this->back();
        }
        
        // Attempt login
        if (Auth::attempt($email, $password)) {
            // Redirect to intended URL or dashboard
            $intendedUrl = Session::get('intended_url', 'dashboard');
            Session::remove('intended_url');
            
            Session::flash('success', 'Welcome back!');
            $this->redirect($intendedUrl);
        } else {
            Session::flash('error', 'Invalid email or password');
            Session::flash('old', ['email' => $email]);
            $this->back();
        }
    }
    
    public function showRegister()
    {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }
        
        $this->render('auth.register', ['title' => 'Register']);
    }
    
    public function register()
    {
        // Validate CSRF token first
        $csrfToken = $this->input('csrf_token');
        if (!CSRF::verify($csrfToken)) {
            Session::flash('error', 'Invalid security token. Please try again.');
            $this->back();
        }
        
        $data = $this->input();
        
        // Validate input
        $errors = $this->validate([
            'name' => 'required|min:2|max:255',
            'email' => 'required|email',
            'phone' => 'required|min:10',
            'password' => 'required|min:6|confirmed'
        ]);
        
        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', $this->input());
            $this->back();
        }
        
        // Check if email already exists
        $existingUser = User::findByEmail($data['email']);
        if ($existingUser) {
            Session::flash('error', 'Email already exists');
            Session::flash('old', $this->input());
            $this->back();
        }
        
        // Create user
        if (Auth::register($data)) {
            Session::flash('success', 'Registration successful! Welcome to MeatMe!');
            $this->redirect('dashboard');
        } else {
            Session::flash('error', 'Registration failed. Please try again.');
            Session::flash('old', $this->input());
            $this->back();
        }
    }
    
    public function logout()
    {
        Auth::logout();
        Session::flash('success', 'You have been logged out successfully');
        $this->redirect('');
    }
    
    public function showForgotPassword()
    {
        $this->render('auth.forgot-password', ['title' => 'Forgot Password']);
    }
    
    public function forgotPassword()
    {
        $email = $this->input('email');
        
        // Validate input
        $errors = $this->validate([
            'email' => 'required|email'
        ]);
        
        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', $this->input());
            $this->back();
        }
        
        // Generate reset token
        $token = Auth::resetPassword($email);
        
        if ($token) {
            // Send email (implement email sending later)
            // For now, just show success message
            Session::flash('success', 'Password reset link has been sent to your email');
            Session::flash('reset_token', $token); // Remove this in production
        } else {
            Session::flash('error', 'Email not found');
        }
        
        $this->back();
    }
    
    public function showResetPassword($token)
    {
        // Verify token
        $reset = Auth::verifyResetToken($token);
        
        if (!$reset) {
            Session::flash('error', 'Invalid or expired reset token');
            $this->redirect('/forgot-password');
        }
        
        $this->render('auth.reset-password', [
            'title' => 'Reset Password',
            'token' => $token
        ]);
    }
    
    public function resetPassword()
    {
        $token = $this->input('token');
        $password = $this->input('password');
        
        // Validate input
        $errors = $this->validate([
            'token' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);
        
        if (!empty($errors)) {
            Session::flash('errors', $errors);
            $this->back();
        }
        
        // Update password
        if (Auth::updatePassword($token, $password)) {
            Session::flash('success', 'Password updated successfully! Please login with your new password.');
            $this->redirect('/login');
        } else {
            Session::flash('error', 'Invalid or expired reset token');
            $this->redirect('/forgot-password');
        }
    }
}
?>
