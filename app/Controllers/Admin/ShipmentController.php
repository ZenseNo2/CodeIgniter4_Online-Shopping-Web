<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\OrderService;
use App\Models\OrderModel;
use App\Models\ShipmentModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

/**
 * Pengelolaan pengiriman dan nomor resi (Praktikum 10).
 */
class ShipmentController extends BaseController
{
    public function index()
    {
        $status = (string) ($this->request->getGet('status') ?? 'ready');
        $model = (new ShipmentModel())->withOrder();
        match ($status) {
            'ready'     => $model->where('o.order_status', 'processing'),
            'shipped'   => $model->where('shipments.shipment_status', 'shipped'),
            'delivered' => $model->where('shipments.shipment_status', 'delivered'),
            default     => $model->whereNotIn('o.order_status', ['cancelled']),
        };

        return view('admin/shipments/index', [
            'title'     => 'Pengiriman',
            'shipments' => $model->orderBy('o.created_at', 'DESC')->paginate(12),
            'pager'     => $model->pager,
            'status'    => $status,
            'couriers'  => array_column(config('Shop')->couriers, 'name', 'name'),
        ]);
    }

    public function ship(int $orderId)
    {
        $rules = [
            'courier'         => ['label' => 'Kurir', 'rules' => 'required|max_length[100]'],
            'tracking_number' => ['label' => 'Nomor resi', 'rules' => 'required|alpha_numeric_punct|min_length[6]|max_length[100]'],
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }
        $order = (new OrderModel())->find($orderId) ?? throw new PageNotFoundException();
        try {
            (new OrderService())->ship($order, trim($this->request->getPost('courier')), strtoupper(trim($this->request->getPost('tracking_number'))));
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Pesanan ' . $order['order_number'] . ' dikirim. Nomor resi tersimpan.');
    }
}
