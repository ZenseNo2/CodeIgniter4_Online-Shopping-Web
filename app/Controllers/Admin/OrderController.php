<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\OrderService;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

/**
 * Manajemen pesanan oleh admin/operator (Praktikum 9 & tahap 6).
 */
class OrderController extends BaseController
{
    public function index()
    {
        $status = (string) $this->request->getGet('status');
        $q      = trim((string) $this->request->getGet('q'));

        $model = (new OrderModel())->withStatus();
        if ($status !== '' && isset(config('Shop')->orderStatusLabels[$status])) {
            $model->where('orders.order_status', $status);
        }
        if ($q !== '') {
            $model->groupStart()->like('orders.order_number', $q)->orLike('u.name', $q)->orLike('orders.recipient_name', $q)->groupEnd();
        }

        $counts = array_column(
            db_connect()->table('orders')->select('order_status, COUNT(*) AS n')->groupBy('order_status')->get()->getResultArray(),
            'n',
            'order_status'
        );

        return view('admin/orders/index', [
            'title'  => 'Pesanan',
            'orders' => $model->orderBy('orders.created_at', 'DESC')->paginate(12),
            'pager'  => $model->pager,
            'status' => $status,
            'q'      => $q,
            'counts' => $counts,
        ]);
    }

    public function show(int $id)
    {
        $order = $this->find($id);

        return view('admin/orders/show', [
            'title'    => 'Pesanan ' . $order['order_number'],
            'order'    => $order,
            'items'    => (new OrderItemModel())->forOrder($id),
            'verifier' => $order['payment_id'] ? db_connect()->table('payments p')->select('u.name')->join('users u', 'u.id_user = p.verified_by')->where('p.id', $order['payment_id'])->get()->getRowArray()['name'] ?? null : null,
        ]);
    }

    public function cancel(int $id)
    {
        $order = $this->find($id);
        $reason = trim((string) $this->request->getPost('reason')) ?: 'Dibatalkan oleh ' . role_label(session('role')) . '.';
        try {
            (new OrderService())->cancel($order, (string) session('role'), mb_substr($reason, 0, 255));
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/admin/orders/' . $id)->with('success', 'Pesanan dibatalkan dan stok dikembalikan.');
    }

    public function complete(int $id)
    {
        try {
            (new OrderService())->complete($this->find($id));
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Pesanan ditandai selesai (barang diterima).');
    }

    private function find(int $id): array
    {
        return (new OrderModel())->withStatus()->where('orders.id', $id)->first() ?? throw new PageNotFoundException();
    }
}
