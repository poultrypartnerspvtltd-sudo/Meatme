<?php

namespace App\Core;

class View
{
    private $viewPath;
    private $layoutPath;
    private $data = [];
    private $sections = [];
    private $currentSection = null;
    
    public function __construct()
    {
        $this->viewPath = __DIR__ . '/../../views/';
        $this->layoutPath = __DIR__ . '/../../views/layouts/';
    }
    
    public function render($template, $data = [], $layout = 'app')
    {
        $this->data = array_merge($this->data, $data);
        
        // Start output buffering
        ob_start();
        
        // Extract data to variables
        extract($this->data);
        
        // Include the template
        $templateFile = $this->viewPath . str_replace('.', '/', $template) . '.php';
        
        if (file_exists($templateFile)) {
            include $templateFile;
        } else {
            throw new \Exception("Template not found: {$template}");
        }
        
        // Get the content
        $content = ob_get_clean();
        
        // If layout is specified, wrap content in layout
        if ($layout) {
            $layoutFile = $this->layoutPath . $layout . '.php';
            
            if (file_exists($layoutFile)) {
                // Make content available to layout and extract all data
                extract($this->data);
                ob_start();
                include $layoutFile;
                $content = ob_get_clean();
            }
        }
        
        echo $content;
    }
    
    public function share($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
    }
    
    public function exists($template)
    {
        $templateFile = $this->viewPath . str_replace('.', '/', $template) . '.php';
        return file_exists($templateFile);
    }
    
    public function layout($layoutName, $variables = [])
    {
        $this->data = array_merge($this->data, $variables);
        $layoutPath = $this->viewPath . str_replace('.', '/', $layoutName) . ".php";
        
        if (file_exists($layoutPath)) {
            // Extract data to variables
            extract($this->data);
            include $layoutPath;
        } else {
            throw new \Exception("Layout not found: " . $layoutPath);
        }
    }
    
    public function start($section)
    {
        $this->currentSection = $section;
        ob_start();
    }
    
    public function stop()
    {
        if ($this->currentSection) {
            $this->sections[$this->currentSection] = ob_get_clean();
            $this->currentSection = null;
        }
    }
    
    public function section($name, $default = '')
    {
        return $this->sections[$name] ?? $default;
    }
    
    public static function escape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    public static function asset($path)
    {
        return Helpers::asset($path);
    }
    
    public static function url($path = '')
    {
        return Helpers::url($path);
    }
    
    public static function formatPrice($amount, $currency = null)
    {
        $config = require __DIR__ . '/../../config/app.php';
        $currencyConfig = $config['currency'];
        
        $symbol = $currency ?? $currencyConfig['symbol'];
        $formatted = number_format($amount, 2);
        
        if ($currencyConfig['position'] === 'before') {
            return $symbol . ' ' . $formatted;
        } else {
            return $formatted . ' ' . $symbol;
        }
    }
    
    public static function formatDate($date, $format = 'M d, Y')
    {
        return date($format, strtotime($date));
    }
    
    public static function timeAgo($datetime)
    {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time/60) . ' minutes ago';
        if ($time < 86400) return floor($time/3600) . ' hours ago';
        if ($time < 2592000) return floor($time/86400) . ' days ago';
        if ($time < 31536000) return floor($time/2592000) . ' months ago';
        
        return floor($time/31536000) . ' years ago';
    }
    
    public static function truncate($text, $length = 100, $suffix = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . $suffix;
    }
    
    /**
     * Highlight search terms in text
     * 
     * @param string $text The text to search in
     * @param string|array $searchTerms The search term(s) to highlight
     * @param string $highlightClass CSS class for highlighting
     * @return string Text with highlighted search terms
     */
    public static function highlightSearchTerms($text, $searchTerms, $highlightClass = 'bg-warning text-dark')
    {
        if (empty($searchTerms) || empty($text)) {
            return self::escape($text);
        }
        
        // If search terms is a string, split into array of terms
        $terms = is_array($searchTerms) ? $searchTerms : preg_split('/\s+/', trim($searchTerms));
        
        // Escape each term for regex
        $escapedTerms = array_map('preg_quote', $terms, array_fill(0, count($terms), '/'));
        
        // Create regex pattern to match whole words (case insensitive)
        $pattern = '/\b(' . implode('|', $escapedTerms) . ')\b/iu';
        
        // Replace matches with highlighted span
        $highlighted = preg_replace_callback($pattern, function($matches) use ($highlightClass) {
            return '<span class="' . $highlightClass . '">' . $matches[0] . '</span>';
        }, self::escape($text));
        
        // If no matches found with word boundaries, try without word boundaries
        if ($highlighted === $text) {
            $pattern = '/(' . implode('|', $escapedTerms) . ')/iu';
            $highlighted = preg_replace_callback($pattern, function($matches) use ($highlightClass) {
                return '<span class="' . $highlightClass . '">' . $matches[0] . '</span>';
            }, self::escape($text));
        }
        
        return $highlighted;
    }
}
?>
