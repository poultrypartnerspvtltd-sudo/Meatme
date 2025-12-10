<?php

namespace App\Core;

class Controller
{
    protected $view;
    protected $db;
    
    public function __construct()
    {
        $this->view = new View();
        $this->db = Database::getInstance();
    }
    
    protected function render($template, $data = [])
    {
        return $this->view->render($template, $data);
    }
    
    protected function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function redirect($path = '', $status = 302)
    {
        Helpers::redirect($path, $status);
    }
    
    protected function back($fallback = '')
    {
        Helpers::back($fallback);
    }
    
    protected function input($key = null, $default = null)
    {
        if ($key === null) {
            return array_merge($_GET, $_POST, $this->parseRequestBody());
        }

        if (isset($_POST[$key])) {
            return $_POST[$key];
        }

        if (isset($_GET[$key])) {
            return $_GET[$key];
        }

        $body = $this->parseRequestBody();
        return $body[$key] ?? $default;
    }

    /**
     * Parse request body for PUT/PATCH/DELETE when content-type is form-urlencoded
     * Returns an associative array of parsed values (or empty array)
     */
    protected function parseRequestBody()
    {
        static $cached = null;
        if ($cached !== null) return $cached;

        $cached = [];

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (in_array($method, ['PUT', 'PATCH', 'DELETE', 'POST'], true)) {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
            $raw = file_get_contents('php://input');

            if (stripos($contentType, 'application/x-www-form-urlencoded') !== false || strpos($raw, '=') !== false) {
                parse_str($raw, $parsed);
                if (is_array($parsed)) {
                    $cached = $parsed;
                }
            }
        }

        return $cached;
    }
    
    protected function validate($rules)
    {
        $errors = [];
        $data = $this->input();
        
        foreach ($rules as $field => $rule) {
            $ruleArray = explode('|', $rule);
            $value = $data[$field] ?? null;
            
            foreach ($ruleArray as $singleRule) {
                $ruleParts = explode(':', $singleRule);
                $ruleName = $ruleParts[0];
                $ruleValue = $ruleParts[1] ?? null;
                
                switch ($ruleName) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field][] = ucfirst($field) . ' is required';
                        }
                        break;
                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = ucfirst($field) . ' must be a valid email';
                        }
                        break;
                    case 'min':
                        if (!empty($value) && strlen($value) < $ruleValue) {
                            $errors[$field][] = ucfirst($field) . " must be at least {$ruleValue} characters";
                        }
                        break;
                    case 'max':
                        if (!empty($value) && strlen($value) > $ruleValue) {
                            $errors[$field][] = ucfirst($field) . " must not exceed {$ruleValue} characters";
                        }
                        break;
                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $errors[$field][] = ucfirst($field) . ' must be a number';
                        }
                        break;
                    case 'confirmed':
                        $confirmField = $field . '_confirmation';
                        if ($value !== ($data[$confirmField] ?? null)) {
                            $errors[$field][] = ucfirst($field) . ' confirmation does not match';
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }
    
    protected function sanitize($input)
    {
        if (is_array($input)) {
            return array_map([$this, 'sanitize'], $input);
        }
        
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    protected function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
}
?>
