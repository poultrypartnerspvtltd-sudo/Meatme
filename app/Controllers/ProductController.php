<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Display a listing of products with search, filter, and pagination
     */
    public function index()
    {
        $page = max(1, (int)$this->input('page', 1));
        $perPage = 12;
        $categoryId = $this->input('category');
        $sortBy = $this->input('sort', 'name');
        $search = trim($this->input('search', ''));
        
        // Sanitize and validate category ID
        if ($categoryId) {
            $categoryId = (int)$categoryId;
            $category = (new Category())->find($categoryId);
            if (!$category || !$category['is_active']) {
                Session::flash('error', 'Invalid category selected');
                $this->redirect('products');
                return;
            }
        }
        
        // Get products with pagination
        $productModel = new Product();
        
        if (!empty($search)) {
            // Use the enhanced search method
            $products = Product::search($search, $categoryId, $perPage, ($page - 1) * $perPage);
            
            // For search results, we'll use a simpler count since the search might be complex
            $totalProducts = count(Product::search($search, $categoryId, 1000, 0)); // Limit to 1000 for performance
            
            // Store search query in session for highlighting in the view
            Session::set('last_search', $search);
        } else {
            // Regular product listing
            $conditions = ['is_active' => 1];
            if ($categoryId) {
                $conditions['category_id'] = $categoryId;
            }
            
            $result = $productModel->paginate($page, $perPage, $conditions, $this->getSortOrder($sortBy));
            $products = $result['data'];
            $totalProducts = $result['total'];
            
            // Clear search session
            Session::remove('last_search');
        }
        
        // Get categories for filter
        $categories = Category::active();
        
        // Get selected category
        $selectedCategory = null;
        if ($categoryId) {
            $selectedCategory = (new Category())->find($categoryId);
        }
        
        $data = [
            'title' => 'Fresh Chicken Products',
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'currentPage' => $page,
            'totalPages' => ceil($totalProducts / $perPage),
            'totalProducts' => $totalProducts,
            'search' => $search,
            'sortBy' => $sortBy
        ];
        
        $this->render('products.index', $data);
    }
    
    public function show($slug)
    {
        $product = Product::findBySlug($slug);
        
        if (!$product || !$product['is_active']) {
            $this->redirect('/products');
        }
        
        $productModel = new Product();
        $productModel->id = $product['id'];
        
        // Get product images
        $images = $productModel->images();
        
        // Get product category
        $category = $productModel->category();
        
        // Get product reviews
        $reviews = $productModel->reviews();
        $rating = $productModel->averageRating();
        
        // Get related products
        $relatedProducts = Product::related($product['id'], $product['category_id'], 4);
        
        $data = [
            'title' => $product['name'],
            'product' => $product,
            'images' => $images,
            'category' => $category,
            'reviews' => $reviews,
            'rating' => $rating,
            'relatedProducts' => $relatedProducts
        ];
        
        $this->render('products.show', $data);
    }
    
    public function category($slug)
    {
        $category = Category::findBySlug($slug);
        
        if (!$category || !$category['is_active']) {
            $this->redirect('/products');
        }
        
        $page = (int)$this->input('page', 1);
        $perPage = 12;
        $sortBy = $this->input('sort', 'name');
        
        // Get products in category
        $products = Product::byCategory($category['id'], $perPage, ($page - 1) * $perPage);
        
        // Get total count
        $categoryModel = new Category();
        $categoryModel->id = $category['id'];
        $totalProducts = $categoryModel->productCount();
        
        // Get subcategories
        $subcategories = $categoryModel->children();
        
        $data = [
            'title' => $category['name'],
            'category' => $category,
            'products' => $products,
            'subcategories' => $subcategories,
            'currentPage' => $page,
            'totalPages' => ceil($totalProducts / $perPage),
            'totalProducts' => $totalProducts,
            'sortBy' => $sortBy
        ];
        
        $this->render('products.category', $data);
    }
    
    public function search()
    {
        $query = $this->input('q');
        $categoryId = $this->input('category');
        $page = (int)$this->input('page', 1);
        $perPage = 12;
        
        if (empty($query)) {
            $this->redirect('/products');
        }
        
        // Search products
        $products = Product::search($query, $categoryId, $perPage, ($page - 1) * $perPage);
        
        // Get categories for filter
        $categories = Category::active();
        
        // Get selected category
        $selectedCategory = null;
        if ($categoryId) {
            $selectedCategory = (new Category())->find($categoryId);
        }
        
        $data = [
            'title' => "Search Results for '{$query}'",
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'query' => $query,
            'currentPage' => $page,
            'totalProducts' => count($products)
        ];
        
        $this->render('products.search', $data);
    }
    
    private function getSortOrder($sortBy)
    {
        switch ($sortBy) {
            case 'price_low':
                return 'price ASC';
            case 'price_high':
                return 'price DESC';
            case 'newest':
                return 'created_at DESC';
            case 'popular':
                return 'is_featured DESC, created_at DESC';
            default:
                return 'name ASC';
        }
    }
}
?>
