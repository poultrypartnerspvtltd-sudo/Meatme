<?php
/**
 * MeatMe - Fresh Chicken eCommerce Platform
 * Entry point for the application
 */

// Start session
session_start();

// Load autoloader and environment variables
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = new Dotenv(__DIR__);
$dotenv->load();

// Load configuration
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

// Load core classes
require_once __DIR__ . '/app/Core/Router.php';
require_once __DIR__ . '/app/Core/Controller.php';
require_once __DIR__ . '/app/Core/Model.php';
require_once __DIR__ . '/app/Core/View.php';
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/Auth.php';
require_once __DIR__ . '/app/Core/Session.php';
require_once __DIR__ . '/app/Core/CSRF.php';
require_once __DIR__ . '/app/Core/Helpers.php';

// Load helper functions
require_once __DIR__ . '/app/helpers.php';

// Initialize router
$router = new App\Core\Router();

// Define routes
require_once __DIR__ . '/routes/web.php';

// Handle the request
$router->dispatch();
?>
