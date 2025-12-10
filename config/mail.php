<?php
/**
 * Mail Configuration for MeatMe
 */

return [
    // Default mail settings
    'from_email' => 'noreply@meatme.com',
    'from_name' => 'MeatMe Contact Form',
    'admin_email' => 'meatme9898@gmail.com',
    
    // SMTP settings (if using SMTP instead of PHP mail())
    'smtp_enabled' => false,
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'meatme9898@gmail.com',
    'smtp_password' => '', // Add your app password here if using SMTP
    'smtp_encryption' => 'tls',
    
    // Email templates
    'contact_subject_prefix' => 'New Contact Form Message: ',
    'admin_notification' => true,
    'auto_reply' => true,
    
    // Email validation
    'max_message_length' => 5000,
    'allowed_domains' => [], // Empty means all domains allowed
    'blocked_domains' => ['tempmail.com', '10minutemail.com'], // Block temporary email services
];
?>
