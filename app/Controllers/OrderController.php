<?php

namespace App\Controllers;

use App\Libraries\ImageUpload;
use App\Libraries\OrderService;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

/**
 * Riwayat pesanan, upload bukti pembayaran, dan pelacakan (Praktikum 9 & 10).
 * Setiap aksi memeriksa bahwa pesanan benar-benar milik customer yang login.
 */
class OrderController extends BaseController
{
    public function index()
    {
        $status = (string) $this->request->getGet('status');
        $model = (new OrderModel())->withStatus()->where('orders.user_id', $this->userId());
        if ($status !== '' && isset(config('Shop')->orderStatusLabels[$status])) {
            $model->where('orders.order_status', $status);
        }

        $orders = $model->orderBy('orders.created_at', 'DESC')->paginate(10);
        $items = [];
        if ($orders) {
            $rows = (new OrderItemModel())->select('order_items.*, p.image')
                ->join('products p', 'p.id = order_items.product_id', 'left')
                ->whereIn('order_items.order_id', array_column($orders, 'id'))->findAll();
            foreach ($rows as $it) {
                $items[$it['order_id']][] = $it;
            }
        }

        return view('frontend/orders', [
            'title'  => 'Pesanan Saya',
            'orders' => $orders,
            'items'  => $items,
            'pager'  => $model->pager,
            'status' => $status,
        ]);
    }

    public function show(string $number)
    {
        $order = $this->ownedOrder($number);

        return view('frontend/order_detail', [
            'title'   => 'Pesanan ' . $order['order_number'],
            'order'   => $order,
            'items'   => (new OrderItemModel())->forOrder((int) $order['id']),
            'method'  => config('Shop')->paymentMethods[$order['payment_method']] ?? null,
        ]);
    }

    public function uploadPayment(string $number)
    {
        $order = $this->ownedOrder($number);

        if (! $this->validate(ImageUpload::rules('proof', 'Bukti pembayaran', true))) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        try {
            $name = ImageUpload::store($this->request->getFile('proof'), WRITEPATH . 'uploads/payments');
            (new OrderService())->submitPayment($order, $name);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/orders/' . $number)->with('success', 'Bukti pembayaran terkirim. Pesanan menunggu verifikasi admin.');
    }

    /** Menampilkan bukti pembayaran milik sendiri (file disimpan di luar folder public). */
    public function proof(string $number)
    {
        $order = $this->ownedOrder($number);

        return self::sendProof($this->response, $order['proof_file']);
    }

    public function cancel(string $number)
    {
        $order = $this->ownedOrder($number);
        try {
            (new OrderService())->cancel($order, 'customer', 'Dibatalkan oleh customer.');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/orders/' . $number)->with('success', 'Pesanan dibatalkan dan stok dikembalikan.');
    }

    public function received(string $number)
    {
        $order = $this->ownedOrder($number);
        try {
            (new OrderService())->complete($order);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/orders/' . $number)->with('success', 'Terima kasih! Pesanan ditandai selesai.');
    }

    /** Pesanan hanya bisa dibuka pemiliknya; selain itu 404 (tidak membocorkan keberadaan pesanan). */
    private function ownedOrder(string $number): array
    {
        $order = (new OrderModel())->findByNumber($number);
        if (! $order || (int) $order['user_id'] !== $this->userId()) {
            throw new PageNotFoundException('Pesanan tidak ditemukan.');
        }

        return $order;
    }

    public static function sendProof($response, ?string $file)
    {
        $path = WRITEPATH . 'uploads/payments/' . basename((string) $file);
        if (! $file || ! is_file($path)) {
            throw new PageNotFoundException('Bukti pembayaran tidak ditemukan.');
        }
        $mime = mime_content_type($path) ?: 'application/octet-stream';
        if (! in_array($mime, ImageUpload::ALLOWED_MIME, true)) {
            throw new PageNotFoundException();
        }

        return $response->setHeader('Content-Type', $mime)
            ->setHeader('Cache-Control', 'private, no-store')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody(file_get_contents($path));
    }
}
