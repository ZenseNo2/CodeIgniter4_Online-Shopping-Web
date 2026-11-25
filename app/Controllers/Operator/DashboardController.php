<?php

namespace App\Controllers\Operator;

use App\Controllers\Admin\Stats;
use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\PaymentModel;

/**
 * Dashboard operator: antrean pekerjaan harian. Route: /operator (filter auth:operator).
 */
class DashboardController extends BaseController
{
    public function index()
    {
        return view('operator/dashboard', [
            'title'    => 'Antrean Kerja',
            'stats'    => Stats::summary(),
            'payments' => (new PaymentModel())->withOrder()->where('payments.payment_status', 'submitted')->orderBy('payments.submitted_at')->findAll(),
            'toShip'   => (new OrderModel())->withStatus()->where('orders.order_status', 'processing')->orderBy('orders.created_at')->findAll(),
            'shipping' => (new OrderModel())->withStatus()->where('orders.order_status', 'shipped')->orderBy('sh.shipped_at')->findAll(8),
        ]);
    }
}
