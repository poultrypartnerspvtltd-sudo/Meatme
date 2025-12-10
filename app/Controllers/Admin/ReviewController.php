<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Product Reviews',
            'user' => Auth::user()
        ];

        return $this->render('admin.reviews', $data);
    }

    public function approve($id)
    {
        $this->json(['success' => true, 'message' => 'Review approved']);
    }

    public function delete($id)
    {
        $this->json(['success' => true, 'message' => 'Review deleted']);
    }
}

