<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;

class UserController extends Controller
{
    public function index()
    {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;

        $userModel = new \App\Models\User();
        $pagination = $userModel->paginate($page, $perPage, [], 'created_at DESC');

        $data = [
            'title' => 'Users',
            'user' => Auth::user(),
            'users' => $pagination['data'],
            'pagination' => $pagination
        ];

        return $this->render('admin.users', $data);
    }

    public function show($id)
    {
        $userModel = new \App\Models\User();
        $userData = $userModel->find($id);

        if (!$userData) {
            http_response_code(404);
            echo 'User not found';
            return;
        }

        $data = [
            'title' => 'User Detail',
            'user' => Auth::user(),
            'detail' => $userData
        ];

        return $this->render('admin.user_view', $data);
    }

    public function updateStatus($id)
    {
        $userModel = new \App\Models\User();
        $existing = $userModel->find($id);

        if (!$existing) {
            return $this->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Log session info for debugging when debug enabled
        if (!empty($_ENV['APP_DEBUG']) && in_array(strtolower((string)$_ENV['APP_DEBUG']), ['1', 'true', 'on'], true)) {
            error_log('Admin::updateStatus session_id=' . \App\Core\Session::getId());
            error_log('Admin::updateStatus session_has_user=' . (\App\Core\Session::has('user_id') ? 'yes' : 'no'));
            error_log('Admin::updateStatus session_all=' . json_encode(\App\Core\Session::all()));
        }

        // Prevent admin from blocking/unblocking themselves
        $current = Auth::user();
        if ($current && isset($current['id']) && (int)$current['id'] === (int)$id) {
            return $this->json(['success' => false, 'message' => 'You cannot change your own status while logged in.'], 403);
        }

        // Toggle status or accept explicit status param
        $requested = $this->input('status');
        if ($requested) {
            $newStatus = $requested;
        } else {
            $newStatus = ($existing['status'] === 'active') ? 'blocked' : 'active';
        }

        $updated = $userModel->update($id, ['status' => $newStatus]);

        if ($updated) {
            return $this->json(['success' => true, 'message' => 'Status updated', 'status' => $newStatus]);
        }

        return $this->json(['success' => false, 'message' => 'Unable to update status'], 500);
    }
}

