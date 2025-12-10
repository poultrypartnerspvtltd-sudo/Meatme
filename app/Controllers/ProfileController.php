<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\CSRF;
use App\Core\Database;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Session::flash('error', 'Please login to access your profile.');
            $this->redirect('login');
        }
        
        $user = Auth::user();
        
        $data = [
            'title' => 'My Profile',
            'user' => $user
        ];
        
        $this->render('profile.index', $data);
    }
    
    public function update()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Session::flash('error', 'Please login to update your profile.');
            $this->redirect('login');
        }
        
        // Validate CSRF token
        if (!CSRF::verify($this->input('csrf_token'))) {
            Session::flash('error', 'Invalid security token. Please try again.');
            $this->back();
        }
        
        $user = Auth::user();
        $data = $this->input();
        
        // Validate input
        $errors = $this->validate([
            'name' => 'required|min:2|max:255',
            'email' => 'required|email',
            'phone' => 'required|min:10'
        ]);
        
        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', $this->input());
            $this->back();
        }
        
        // Check if email is already taken by another user
        if ($data['email'] !== $user['email']) {
            $existingUser = User::findByEmail($data['email']);
            if ($existingUser && $existingUser['id'] !== $user['id']) {
                Session::flash('error', 'Email is already taken by another user.');
                Session::flash('old', $this->input());
                $this->back();
            }
        }
        
        // Sanitize input data
        $sanitizedData = [
            'name' => filter_var(trim($data['name']), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'email' => filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL),
            'phone' => filter_var(trim($data['phone']), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)
        ];

        // Update user profile
        $db = Database::getInstance();

        try {
            $stmt = $db->prepare("
                UPDATE users
                SET name = ?, email = ?, phone = ?, updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                $sanitizedData['name'],
                $sanitizedData['email'],
                $sanitizedData['phone'],
                $user['id']
            ]);
            
            // Update session data
            $_SESSION['user']['name'] = $sanitizedData['name'];
            $_SESSION['user']['email'] = $sanitizedData['email'];
            $_SESSION['user']['phone'] = $sanitizedData['phone'];
            
            Session::flash('success', 'Profile updated successfully!');
            $this->redirect('profile');
            
        } catch (\PDOException $e) {
            error_log("Profile update error: " . $e->getMessage());
            Session::flash('error', 'Failed to update profile. Please try again.');
            $this->back();
        }
    }
    
    public function password()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Session::flash('error', 'Please login to change your password.');
            $this->redirect('login');
        }
        
        $user = Auth::user();
        
        $data = [
            'title' => 'Change Password',
            'user' => $user
        ];
        
        $this->render('profile.password', $data);
    }
    
    public function updatePassword()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Session::flash('error', 'Please login to change your password.');
            $this->redirect('login');
        }
        
        // Validate CSRF token
        if (!CSRF::verify($this->input('csrf_token'))) {
            Session::flash('error', 'Invalid security token. Please try again.');
            $this->back();
        }
        
        $user = Auth::user();
        $data = $this->input();
        
        // Validate input
        $errors = $this->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);
        
        if (!empty($errors)) {
            Session::flash('errors', $errors);
            $this->back();
        }
        
        // Verify current password: fetch hashed password from DB (Auth::user() strips password)
        $db = Database::getInstance();
        $dbUser = $db->fetch("SELECT password FROM users WHERE id = :id LIMIT 1", ['id' => $user['id']]);
        $currentHash = $dbUser['password'] ?? null;

        if (!$currentHash || !password_verify($data['current_password'], $currentHash)) {
            Session::flash('error', 'Current password is incorrect.');
            $this->back();
        }
        
        // Update password
        $db = Database::getInstance();
        
        try {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $stmt = $db->prepare("
                UPDATE users 
                SET password = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            
            $stmt->execute([$hashedPassword, $user['id']]);
            
            Session::flash('success', 'Password changed successfully!');
            $this->redirect('profile');
            
        } catch (\PDOException $e) {
            error_log("Password update error: " . $e->getMessage());
            Session::flash('error', 'Failed to change password. Please try again.');
            $this->back();
        }
    }
}
?>
