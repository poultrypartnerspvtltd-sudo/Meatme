<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // Ensure admin is logged in
        if (!Auth::check() || !Auth::isAdmin()) {
            $this->redirect('/admin/login');
        }
    }

    public function index()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 20;

        $paginator = (new Product())->paginate($page, $perPage, [], 'created_at DESC');

        $data = [
            'title' => 'Products',
            'products' => $paginator['data'],
            'paginator' => $paginator,
            'user' => Auth::user()
        ];

        return $this->render('admin.products', $data);
    }

    // Minimal stubs for other actions to avoid fatal errors when routes exist
    public function create()
    {
        // Load categories for selection
        $categories = \App\Models\Category::active();

        $data = ['title' => 'Create Product', 'user' => Auth::user(), 'categories' => $categories];
        return $this->render('admin.product_create', $data);
    }

    public function store()
    {
        $input = $this->input();

        // Validate input
        $errors = $this->validate([
            'name' => 'required',
            'price' => 'required|numeric'
        ]);

        if (!empty($errors)) {
            \App\Core\Session::flash('errors', $errors);
            \App\Core\Session::flash('old', $input);
            $this->back();
        }

        // Prepare data for insertion
        $name = $this->sanitize($input['name'] ?? '');
        $price = isset($input['price']) ? (float) $input['price'] : 0.0;
        $stock = isset($input['stock_quantity']) ? (int) $input['stock_quantity'] : 0;
        $short = $this->sanitize($input['short_description'] ?? '');
        $sku = $this->sanitize($input['sku'] ?? '');

        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', trim($name)));

        $product = new Product();
        $created = $product->create([
            'name' => $name,
            'slug' => $slug,
            'price' => $price,
            'category_id' => isset($input['category_id']) && $input['category_id'] !== '' ? (int)$input['category_id'] : null,
            'stock_quantity' => $stock,
            'short_description' => $short,
            'sku' => $sku,
            'is_active' => 1
        ]);

        if ($created) {
            \App\Core\Session::flash('success', 'Product created successfully');
        } else {
            \App\Core\Session::flash('error', 'Failed to create product');
        }

        $this->redirect('admin/products');
    }

    public function show($id)
    {
        $product = (new Product())->find($id);
        $data = ['title' => 'View Product', 'product' => $product, 'user' => Auth::user()];
        return $this->render('admin.product_view', $data);
    }

    public function edit($id)
    {
        $product = (new Product())->find($id);
        $categories = \App\Models\Category::active();
        $data = ['title' => 'Edit Product', 'product' => $product, 'user' => Auth::user(), 'categories' => $categories];
        return $this->render('admin.product_edit', $data);
    }

    public function update($id)
    {
        $input = $this->input();

        // Validate
        $errors = $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock_quantity' => 'numeric'
        ]);

        if (!empty($errors)) {
            \App\Core\Session::flash('errors', $errors);
            \App\Core\Session::flash('old', $input);
            $this->back();
        }

        $data = [
            'name' => $this->sanitize($input['name'] ?? ''),
            'category_id' => isset($input['category_id']) && $input['category_id'] !== '' ? (int)$input['category_id'] : null,
            'price' => isset($input['price']) ? (float)$input['price'] : 0,
            'stock_quantity' => isset($input['stock_quantity']) ? (int)$input['stock_quantity'] : 0,
            'short_description' => $this->sanitize($input['short_description'] ?? ''),
            'sku' => $this->sanitize($input['sku'] ?? '')
        ];

        $product = new Product();
        $updated = $product->update($id, $data);

        if ($updated) {
            \App\Core\Session::flash('success', 'Product updated successfully');
        } else {
            \App\Core\Session::flash('error', 'Failed to update product');
        }

        $this->redirect('admin/products');
    }

    public function delete($id)
    {
        $model = new Product();
        $model->delete($id);
        $this->redirect('/admin/products');
    }
}

?>
