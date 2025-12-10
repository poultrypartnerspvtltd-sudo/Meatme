                 <?php
/**
 * Email Configuration
 */

return [
    // SMTP Configuration
    'smtp' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls', // tls or ssl
        'username' => 'meatme9898@gmail.com',
        'password' => 'udkwchmnavdlpcjz',
        'auth' => true,
    ],

    // Email addresses
    'from_email' => 'meatme9898@gmail.com',
    'from_name' => 'MeatMe Contact Form',
    'admin_email' => 'meatme9898@gmail.com',
    'admin_name' => 'MeatMe Admin',

    // Email settings
    'contact_subject_prefix' => 'New Contact Message from',
    'debug_mode' => false, // Set to true for debugging

    // Additional recipients (optional)
    'bcc_emails' => [
        // 'backup@example.com' => 'Backup Admin'
    ],
];
