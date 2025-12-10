<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        // Get homepage content from database
        $homepageContent = $this->getHomepageContent();
        
        // Get featured products (with fallback)
        try {
            $featuredProducts = Product::featured(6);
        } catch (Exception $e) {
            $featuredProducts = $this->getSampleProducts();
        }
        
        // Get main categories (with fallback)
        try {
            $categories = Category::mainCategories();
        } catch (Exception $e) {
            $categories = $this->getSampleCategories();
        }
        
        // Get recent products (with fallback)
        try {
            $recentProducts = (new Product())->all('created_at DESC', 8);
        } catch (Exception $e) {
            $recentProducts = $this->getSampleProducts();
        }
        
        // Get latest updates
        // Get testimonials (you can add this to database later)
        $testimonials = [
            [
                'name' => 'Priya Sharma',
                'rating' => 5,
                'comment' => 'Amazing quality chicken! Fresh and delivered on time. Will definitely order again.',
                'location' => 'Kathmandu'
            ],
            [
                'name' => 'Rajesh Thapa',
                'rating' => 5,
                'comment' => 'Best chicken in town. Hygienically processed and farm fresh. Highly recommended!',
                'location' => 'Lalitpur'
            ],
            [
                'name' => 'Sunita Rai',
                'rating' => 5,
                'comment' => 'Love the convenience and quality. Same day delivery is a game changer.',
                'location' => 'Bhaktapur'
            ]
        ];
        
        $data = [
            'title' => $homepageContent['hero']['title'] ?? 'Fresh Chicken, Straight from Our Farm',
            'homepageContent' => $homepageContent,
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'recentProducts' => $recentProducts,
            'testimonials' => $testimonials
        ];
        
        $this->render('home.index', $data);
    }
    
    private function getHomepageContent()
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT * FROM homepage_content WHERE is_active = 1 ORDER BY sort_order ASC");
            $stmt->execute();
            $content = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Organize content by section name for easy access
            $sections = [];
            foreach ($content as $section) {
                $sections[$section['section_name']] = $section;
            }
            
            return $sections;
        } catch (Exception $e) {
            // Return default content if database fails
            return $this->getDefaultHomepageContent();
        }
    }
    
    private function getDefaultHomepageContent()
    {
        return [
            'hero' => [
                'title' => 'Fresh Farm Chicken Delivered to Your Door',
                'content' => 'Experience the finest quality chicken products sourced directly from our farms. We guarantee freshness, quality, and taste that your family deserves.',
                'button_text' => 'Shop Now',
                'button_link' => '/products',
                'image' => null
            ],
            'about' => [
                'title' => 'Why Choose MeatMe?',
                'content' => 'We are committed to providing the highest quality chicken products with unmatched freshness and flavor. Our farm-to-table approach ensures you get the best.',
                'button_text' => null,
                'button_link' => null,
                'image' => null
            ],
            'features' => [
                'title' => 'Our Promise to You',
                'content' => 'Fresh daily delivery, premium quality assurance, and customer satisfaction guaranteed. We make sure every product meets our strict quality standards.',
                'button_text' => null,
                'button_link' => null,
                'image' => null
            ]
        ];
    }
    
    private function getSampleProducts()
    {
        return [
            [
                'id' => 1,
                'name' => 'Fresh Whole Chicken',
                'slug' => 'fresh-whole-chicken',
                'price' => 450.00,
                'compare_price' => 500.00,
                'unit' => 'kg',
                'short_description' => 'Farm-fresh whole chicken, perfect for roasting',
                'freshness_indicator' => 'Fresh Today',
                'is_featured' => 1,
                'stock_quantity' => 50,
                'min_quantity' => 1
            ],
            [
                'id' => 2,
                'name' => 'Chicken Breast Boneless',
                'slug' => 'chicken-breast-boneless',
                'price' => 650.00,
                'compare_price' => 700.00,
                'unit' => 'kg',
                'short_description' => 'Tender boneless chicken breast',
                'freshness_indicator' => 'Fresh Today',
                'is_featured' => 1,
                'stock_quantity' => 30,
                'min_quantity' => 0.5
            ],
            [
                'id' => 3,
                'name' => 'Chicken Drumsticks',
                'slug' => 'chicken-drumsticks',
                'price' => 380.00,
                'compare_price' => 420.00,
                'unit' => 'kg',
                'short_description' => 'Juicy chicken drumsticks',
                'freshness_indicator' => 'Fresh Today',
                'is_featured' => 1,
                'stock_quantity' => 40,
                'min_quantity' => 0.5
            ]
        ];
    }
    
    private function getSampleCategories()
    {
        return [
            [
                'id' => 1,
                'name' => 'Whole Chicken',
                'slug' => 'whole-chicken',
                'product_count' => 5
            ],
            [
                'id' => 2,
                'name' => 'Chicken Breast',
                'slug' => 'chicken-breast',
                'product_count' => 8
            ],
            [
                'id' => 3,
                'name' => 'Chicken Legs',
                'slug' => 'chicken-legs',
                'product_count' => 6
            ],
            [
                'id' => 4,
                'name' => 'Chicken Wings',
                'slug' => 'chicken-wings',
                'product_count' => 4
            ],
            [
                'id' => 5,
                'name' => 'Boneless Cuts',
                'slug' => 'boneless-cuts',
                'product_count' => 10
            ],
            [
                'id' => 6,
                'name' => 'Marinated Chicken',
                'slug' => 'marinated-chicken',
                'product_count' => 7
            ]
        ];
    }
}
?>
