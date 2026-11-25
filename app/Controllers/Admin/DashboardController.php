<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\ProductModel;

/**
 * Dashboard administrator (Praktikum 11). Route: /admin (filter auth:admin).
 */
class DashboardController extends BaseController
{
    public function index()
    {
        $to   = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-13 days'));

        return view('admin/dashboard', [
            'title'    => 'Dashboard',
            'stats'    => Stats::summary(),
            'daily'    => Stats::daily($from, $to),
            'status'   => Stats::statusDistribution(),
            'best'     => (new ProductModel())->bestSellers(5),
            'recent'   => (new OrderModel())->withStatus()->orderBy('orders.created_at', 'DESC')->findAll(6),
            'lowStock' => (new ProductModel())->where('status', 'active')->where('stock <=', 5)->orderBy('stock')->findAll(5),
        ]);
    }
}
