<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - MeatMe</title>
    
    <!-- MDBootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .error-container {
            text-align: center;
            color: white;
            max-width: 600px;
            padding: 2rem;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            text-shadow: 0 4px 20px rgba(0,0,0,0.3);
            margin-bottom: 1rem;
        }
        
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .error-description {
            font-size: 1.1rem;
            margin-bottom: 3rem;
            opacity: 0.8;
        }
        
        .btn-home {
            background: rgba(255,255,255,0.2);
            border: 2px solid white;
            color: white;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .btn-home:hover {
            background: white;
            color: #2e7d32;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        
        .chicken-icon {
            font-size: 4rem;
            margin-bottom: 2rem;
            opacity: 0.7;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        
        .floating-elements::before,
        .floating-elements::after {
            content: '';
            position: absolute;
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-elements::before {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .floating-elements::after {
            top: 60%;
            right: 10%;
            animation-delay: 3s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        
        @media (max-width: 768px) {
            .error-code {
                font-size: 6rem;
            }
            
            .error-message {
                font-size: 1.3rem;
            }
            
            .error-description {
                font-size: 1rem;
            }
            
            .btn-home {
                padding: 0.8rem 1.5rem;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="floating-elements"></div>
    
    <div class="error-container">
        <div class="chicken-icon">
            <i class="fas fa-drumstick-bite"></i>
        </div>
        
        <div class="error-code">404</div>
        
        <h1 class="error-message">Oops! Page Not Found</h1>
        
        <p class="error-description">
            Looks like this page flew the coop! The page you're looking for doesn't exist or has been moved to a different location.
        </p>
        
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="<?= e($_ENV['APP_URL'] ?? 'http://localhost/Meatme') ?>" class="btn-home">
                <i class="fas fa-home"></i>
                Back to Home
            </a>
            
            <a href="<?= e($_ENV['APP_URL'] ?? 'http://localhost/Meatme') ?>/products" class="btn-home">
                <i class="fas fa-shopping-bag"></i>
                Browse Products
            </a>
        </div>
        
        <div class="mt-4">
            <p class="mb-2">Need help? Contact us:</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="tel:+977-9800000000" class="text-white text-decoration-none">
                    <i class="fas fa-phone me-1"></i>
                    +977-9800000000
                </a>
                <a href="mailto:info@meatme.com" class="text-white text-decoration-none">
                    <i class="fas fa-envelope me-1"></i>
                    info@meatme.com
                </a>
            </div>
        </div>
    </div>
    
    <!-- MDBootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
</body>
</html>
