<?php
/**
 * Application Configuration
 */

return [
    'name' => $_ENV['APP_NAME'] ?? 'MeatMe - Fresh Chicken',
    'url' => $_ENV['APP_URL'] ?? 'http://localhost/Meatme',
    'env' => $_ENV['APP_ENV'] ?? 'development',
    'debug' => $_ENV['APP_DEBUG'] ?? true,
    
    'timezone' => 'Asia/Kathmandu',
    
    'currency' => [
        'code' => 'NPR',
        'symbol' => 'Rs.',
        'position' => 'before' // before or after
    ],
    
    'pagination' => [
        'per_page' => 12,
        'admin_per_page' => 20
    ],
    
    'upload' => [
        'max_size' => $_ENV['MAX_FILE_SIZE'] ?? 5242880, // 5MB
        'allowed_types' => explode(',', $_ENV['ALLOWED_IMAGE_TYPES'] ?? 'jpg,jpeg,png,webp'),
        'path' => 'assets/uploads/'
    ],
    
    'mail' => [
        'host' => $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com',
        'port' => $_ENV['MAIL_PORT'] ?? 587,
        'username' => $_ENV['MAIL_USERNAME'] ?? '',
        'password' => $_ENV['MAIL_PASSWORD'] ?? '',
        'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@meatme.com',
        'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'MeatMe Fresh Chicken'
    ],
    
    'payment' => [
        'stripe' => [
            'public_key' => $_ENV['STRIPE_PUBLIC_KEY'] ?? '',
            'secret_key' => $_ENV['STRIPE_SECRET_KEY'] ?? ''
        ],
        'razorpay' => [
            'key_id' => $_ENV['RAZORPAY_KEY_ID'] ?? '',
            'key_secret' => $_ENV['RAZORPAY_KEY_SECRET'] ?? ''
        ],
        'paypal' => [
            'client_id' => $_ENV['PAYPAL_CLIENT_ID'] ?? '',
            'client_secret' => $_ENV['PAYPAL_CLIENT_SECRET'] ?? '',
            'mode' => $_ENV['PAYPAL_MODE'] ?? 'sandbox'
        ],
        'esewa' => [
            'merchant_id' => $_ENV['ESEWA_MERCHANT_ID'] ?? '',
            'secret_key' => $_ENV['ESEWA_SECRET_KEY'] ?? ''
        ]
    ],
    
    'whatsapp' => [
        'api_url' => $_ENV['WHATSAPP_API_URL'] ?? 'https://api.whatsapp.com/send',
        'business_number' => $_ENV['WHATSAPP_BUSINESS_NUMBER'] ?? '+977-9800000000'
    ],
    
    'security' => [
        'session_lifetime' => $_ENV['SESSION_LIFETIME'] ?? 7200,
        'csrf_token_expire' => $_ENV['CSRF_TOKEN_EXPIRE'] ?? 3600
    ]
];
?>
