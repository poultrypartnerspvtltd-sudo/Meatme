<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\CSRF;
use App\Models\Category as CategoryModel;

class CategoryController extends Controller
{
    public function index()
    {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 30;

        $category = new CategoryModel();
        $pagination = $category->paginate($page, $perPage, [], 'name ASC');

        $data = [
            'title' => 'Categories',
            'user' => Auth::user(),
            'categories' => $pagination['data'],
            'pagination' => $pagination
        ];

        return $this->render('admin.categories', $data);
    }

    public function store()
    {
        // CSRF
        if (!CSRF::verify($this->input('csrf_token'))) {
            Session::flash('error', 'Invalid token');
            $this->back('/admin/categories');
        }

        $name = $this->sanitize($this->input('name'));
        if (empty($name)) {
            Session::flash('error', 'Name is required');
            $this->back('/admin/categories');
        }

        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', trim($name)));

        $category = new CategoryModel();
        $created = $category->create([
            'name' => $name,
            'slug' => $slug,
            'is_active' => 1
        ]);

        if ($created) {
            Session::flash('success', 'Category created');
        } else {
            Session::flash('error', 'Failed to create category');
        }

        $this->redirect('/admin/categories');
    }

    public function update($id)
    {
        // Not implemented yet
        $this->redirect('/admin/categories');
    }

    public function delete($id)
    {
        $category = new CategoryModel();
        $existing = $category->find($id);

        if (!$existing) {
            if ($this->isAjaxRequest()) {
                return $this->json(['success' => false, 'message' => 'Category not found'], 404);
            }
            Session::flash('error', 'Category not found');
            $this->redirect('/admin/categories');
        }

        $deleted = $category->delete($id);

        if ($this->isAjaxRequest()) {
            return $this->json(['success' => (bool)$deleted]);
        }

        if ($deleted) {
            Session::flash('success', 'Category deleted');
        } else {
            Session::flash('error', 'Unable to delete category');
        }

        $this->redirect('/admin/categories');
    }
}

