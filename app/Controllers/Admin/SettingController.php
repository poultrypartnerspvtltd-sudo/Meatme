<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\CSRF;

class SettingController extends Controller
{
    public function index()
    {
        // Ensure admin
        if (!Auth::check() || !Auth::isAdmin()) {
            $this->redirect('/admin/login');
        }

        // Load current config (read-only for display)
        $config = require __DIR__ . '/../../../config/app.php';

        $data = [
            'title' => 'Settings',
            'user' => Auth::user(),
            'config' => $config
        ];

        return $this->render('admin.settings', $data);
    }

    public function update()
    {
        // Basic CSRF verification
        if (!CSRF::verify($this->input('csrf_token'))) {
            Session::flash('error', 'Invalid security token.');
            $this->redirect('/admin/settings');
        }

        // Collect submitted values (not persisted to file in this minimal implementation)
        $siteName = $this->input('name');
        $siteUrl = $this->input('url');
        $timezone = $this->input('timezone');

        // TODO: Persist settings to database or config file as needed.

        Session::flash('success', 'Settings saved (note: not persisted in this build).');
        $this->redirect('/admin/settings');
    }
}

