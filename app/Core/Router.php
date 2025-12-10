<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $middlewares = [];
    
    public function get($path, $handler, $middleware = [])
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }
    
    public function post($path, $handler, $middleware = [])
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }
    
    public function put($path, $handler, $middleware = [])
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }
    
    public function delete($path, $handler, $middleware = [])
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }
    
    private function addRoute($method, $path, $handler, $middleware = [])
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    public function dispatch()
    {
        // Determine HTTP method, support method override via hidden form field or header
        $originalMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestMethod = $originalMethod;

        // Allow method override using POST form field `_method`
        if ($originalMethod === 'POST') {
            // Prefer explicit header override
            $headerOverride = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? $_SERVER['HTTP_X_HTTPMETHOD_OVERRIDE'] ?? null;
            if ($headerOverride) {
                $requestMethod = strtoupper($headerOverride);
            } elseif (!empty($_POST['_method'])) {
                $requestMethod = strtoupper($_POST['_method']);
            } elseif (!empty($_REQUEST['_method'])) {
                $requestMethod = strtoupper($_REQUEST['_method']);
            }
        }
        $requestPath = $this->getPath();
        
        // Debug logging (can be removed in production)
        // Use environment variable check instead of relying on an APP_DEBUG constant
        if (!empty($_ENV['APP_DEBUG']) && in_array(strtolower((string)$_ENV['APP_DEBUG']), ['1', 'true', 'on'], true)) {
            error_log("Router: Method={$requestMethod}, Path={$requestPath}, REQUEST_URI=" . ($_SERVER['REQUEST_URI'] ?? 'N/A'));
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchPath($route['path'], $requestPath)) {
                // Execute middleware
                foreach ($route['middleware'] as $middleware) {
                    if (!$this->executeMiddleware($middleware)) {
                        return;
                    }
                }
                
                // Validate CSRF for state-changing methods using the effective request method
                if (in_array($requestMethod, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
                    CSRF::validate();
                }

                // Execute handler
                $this->executeHandler($route['handler'], $requestPath, $route['path']);
                return;
            }
        }
        
        // 404 Not Found
        $this->notFound();
    }
    
    private function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($path, PHP_URL_PATH);
        
        // Remove base path if running in subdirectory
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $basePath = dirname($scriptName);
        
        // Normalize base path (remove trailing slash, handle Windows paths)
        $basePath = str_replace('\\', '/', $basePath);
        
        // Handle root directory case
        if ($basePath === '.' || $basePath === '/' || $basePath === '\\') {
            $basePath = '';
        }
        
        // Remove base path from request path if it exists
        if (!empty($basePath) && $basePath !== '/') {
            // Ensure base path starts with /
            if (strpos($basePath, '/') !== 0) {
                $basePath = '/' . $basePath;
            }
            
            // Remove base path from request path
            if (strpos($path, $basePath) === 0) {
                $path = substr($path, strlen($basePath));
            }
        }
        
        // Ensure path starts with / and normalize
        $path = '/' . ltrim($path, '/');
        
        // Handle root path edge cases
        if ($path === '//' || empty(trim($path, '/'))) {
            $path = '/';
        }
        
        return $path;
    }
    
    private function matchPath($routePath, $requestPath)
    {
        // Normalize trailing slashes so '/admin' and '/admin/' both match
        $normalizedRoute = rtrim($routePath, '/');
        $normalizedRequest = rtrim($requestPath, '/');

        if ($normalizedRoute === '') {
            $normalizedRoute = '/';
        }
        if ($normalizedRequest === '') {
            $normalizedRequest = '/';
        }

        // Convert route path to regex pattern (handle parameters like {id})
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $normalizedRoute);
        $pattern = '#^' . $pattern . '$#';

        return (bool) preg_match($pattern, $normalizedRequest);
    }
    
    private function executeHandler($handler, $requestPath, $routePath)
    {
        if (is_string($handler)) {
            // Controller@method format
            if (strpos($handler, '@') !== false) {
                list($controller, $method) = explode('@', $handler);
                
                // Handle namespaced controllers (e.g., Api\ProductController)
                // If controller already has namespace, use as-is, otherwise prepend App\Controllers
                if (strpos($controller, '\\') === 0) {
                    // Absolute namespace (starts with \)
                    $controllerClass = substr($controller, 1);
                } elseif (strpos($controller, '\\') !== false) {
                    // Relative namespace (e.g., Api\ProductController)
                    $controllerClass = "App\\Controllers\\{$controller}";
                } else {
                    // Simple controller name (e.g., ProductController)
                    $controllerClass = "App\\Controllers\\{$controller}";
                }
                
                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    if (method_exists($controllerInstance, $method)) {
                        $params = $this->extractParams($routePath, $requestPath);
                        call_user_func_array([$controllerInstance, $method], $params);
                        return;
                    }
                }
            }
        } elseif (is_callable($handler)) {
            // Closure
            $params = $this->extractParams($routePath, $requestPath);
            call_user_func_array($handler, $params);
            return;
        }
        
        $this->notFound();
    }
    
    private function extractParams($routePath, $requestPath)
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));
        
        $params = [];
        foreach ($routeParts as $index => $part) {
            if (preg_match('/\{([^}]+)\}/', $part, $matches)) {
                $params[] = $requestParts[$index] ?? null;
            }
        }
        
        return $params;
    }
    
    private function executeMiddleware($middleware)
    {
        if (is_string($middleware)) {
            $middlewareClass = "App\\Middleware\\{$middleware}";
            if (class_exists($middlewareClass)) {
                $middlewareInstance = new $middlewareClass();
                return $middlewareInstance->handle();
            }
        } elseif (is_callable($middleware)) {
            return $middleware();
        }
        
        return true;
    }
    
    private function notFound()
    {
        http_response_code(404);
        require_once __DIR__ . '/../../views/errors/404.php';
    }
}
?>
