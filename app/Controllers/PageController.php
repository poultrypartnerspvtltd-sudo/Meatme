<?php

namespace App\Controllers;

use App\Core\Controller;

class PageController extends Controller
{
    public function about()
    {
        $data = [
            'title' => 'About Us - MeatMe',
            'meta_description' => 'Learn about MeatMe - your trusted source for fresh, high-quality chicken products delivered to your doorstep.',
            'page' => 'about'
        ];
        
        $this->render('pages.about', $data);
    }
    
    public function privacy()
    {
        $data = [
            'title' => 'Privacy Policy - MeatMe',
            'meta_description' => 'Read our privacy policy to understand how MeatMe protects and handles your personal information.',
            'page' => 'privacy'
        ];
        
        $this->render('pages.privacy', $data);
    }
    
    public function terms()
    {
        $data = [
            'title' => 'Terms of Service - MeatMe',
            'meta_description' => 'Read our terms of service to understand the rules and regulations for using MeatMe services.',
            'page' => 'terms'
        ];
        
        $this->render('pages.terms', $data);
    }
    
    public function delivery()
    {
        $data = [
            'title' => 'Delivery Policy - MeatMe',
            'meta_description' => 'Learn about our delivery policy, areas we serve, and delivery timings for fresh chicken products.',
            'page' => 'delivery'
        ];
        
        $this->render('pages.delivery', $data);
    }
}
