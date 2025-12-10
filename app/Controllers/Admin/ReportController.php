<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;

class ReportController extends Controller
{
    public function sales()
    {
        $data = [
            'title' => 'Sales Report',
            'user' => Auth::user()
        ];

        return $this->render('admin.reports_sales', $data);
    }

    public function products()
    {
        $data = [
            'title' => 'Product Report',
            'user' => Auth::user()
        ];

        return $this->render('admin.reports_products', $data);
    }

    public function customers()
    {
        $data = [
            'title' => 'Customers Report',
            'user' => Auth::user()
        ];

        return $this->render('admin.reports_customers', $data);
    }

    public function export($type)
    {
        // Minimal export: return a CSV placeholder
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="report_' . $type . '.csv"');
        echo "id,name,value\n1,Example,100";
        exit;
    }
}

