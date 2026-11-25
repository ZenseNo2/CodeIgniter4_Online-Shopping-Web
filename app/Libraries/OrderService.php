<?php

namespace App\Libraries;

use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\ShipmentModel;
use RuntimeException;

/**
 * Proses checkout dan perubahan status pesanan (Praktikum 9 & 10).
 *
 * Prinsip yang dijaga:
 *  - harga dan total selalu dihitung ulang dari database, tidak pernah dari browser;
 *  - order, order_items, pengurangan stok, dan pengosongan keranjang berada dalam
 *    SATU transaksi database: berhasil semua atau batal semua;
 *  - data transaksi tidak dihapus fisik; pembatalan memakai status 'cancelled'.
 */
class OrderService
{
    private OrderModel $orders;

    public function __construct()
    {
        $this->orders = new OrderModel();
    }

    /**
     * Membuat pesanan dari keranjang customer.
     *
     * @param array{recipient_name:string, phone:string, address:string} $shipTo
     *
     * @return array pesanan yang baru dibuat
     *
     * @throws RuntimeException bila keranjang kosong, stok kurang, atau transaksi gagal
     */
    public function checkout(int $userId, array $shipTo, string $courierKey, string $methodKey, ?string $notes = null): array
    {
        $shop = config('Shop');
        if (! isset($shop->couriers[$courierKey]) || ! isset($shop->paymentMethods[$methodKey])) {
            throw new RuntimeException('Kurir atau metode pembayaran tidak valid.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            // 1. Ambil keranjang milik customer dari database.
            $cart = $db->table('carts')->where('user_id', $userId)->get()->getRowArray();
            if (! $cart) {
                throw new RuntimeException('Keranjang belanja kosong.');
            }
            $items = $db->table('cart_items')->where('cart_id', $cart['id'])->get()->getResultArray();
            if (! $items) {
                throw new RuntimeException('Keranjang belanja kosong.');
            }

            // 2–4. Ambil ulang produk, validasi status & stok, hitung harga di backend.
            $lines = [];
            $subtotal = 0.0;
            foreach ($items as $it) {
                $p = $db->table('products')->where('id', $it['product_id'])->get()->getRowArray();
                if (! $p || $p['status'] !== 'active') {
                    throw new RuntimeException('Produk "' . ($p['name'] ?? '#' . $it['product_id']) . '" sedang tidak dijual. Hapus dari keranjang terlebih dahulu.');
                }
                if ((int) $p['stock'] < (int) $it['quantity']) {
                    throw new RuntimeException('Stok "' . $p['name'] . '" tidak mencukupi (tersisa ' . $p['stock'] . ').');
                }
                $line = (float) $p['price'] * (int) $it['quantity'];
                $subtotal += $line;
                $lines[] = [
                    'product_id'   => (int) $p['id'],
                    'product_name' => $p['name'],
                    'price'        => $p['price'],
                    'quantity'     => (int) $it['quantity'],
                    'subtotal'     => $line,
                ];
            }
            $shipping = (float) $shop->couriers[$courierKey]['cost'];
            $total = $subtotal + $shipping;

            // 5. Buat record orders.
            $now = date('Y-m-d H:i:s');
            $number = $this->orders->generateNumber();
            $db->table('orders')->insert([
                'user_id'          => $userId,
                'order_number'     => $number,
                'recipient_name'   => $shipTo['recipient_name'],
                'phone'            => $shipTo['phone'],
                'shipping_address' => $shipTo['address'],
                'courier'          => $shop->couriers[$courierKey]['name'],
                'shipping_cost'    => $shipping,
                'subtotal'         => $subtotal,
                'total'            => $total,
                'order_status'     => 'pending_payment',
                'notes'            => $notes ?: null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
            $orderId = (int) $db->insertID();

            // 6–7. Buat order_items (snapshot nama & harga) dan kurangi stok.
            foreach ($lines as $line) {
                $db->table('order_items')->insert($line + ['order_id' => $orderId]);

                // Pengurangan bersyarat: gagal bila stok berubah di antara pengecekan dan update.
                $db->table('products')
                    ->set('stock', 'stock - ' . (int) $line['quantity'], false)
                    ->set('updated_at', $now)
                    ->where('id', $line['product_id'])
                    ->where('stock >=', $line['quantity'])
                    ->update();
                if ($db->affectedRows() !== 1) {
                    throw new RuntimeException('Stok "' . $line['product_name'] . '" baru saja berubah. Silakan periksa keranjang lagi.');
                }
            }

            // Data pembayaran & pengiriman awal.
            $db->table('payments')->insert([
                'order_id' => $orderId, 'method' => $methodKey, 'amount' => $total, 'payment_status' => 'unpaid',
            ]);
            $db->table('shipments')->insert([
                'order_id' => $orderId, 'courier' => $shop->couriers[$courierKey]['name'], 'shipment_status' => 'pending',
            ]);

            // 8. Kosongkan keranjang.
            $db->table('cart_items')->where('cart_id', $cart['id'])->delete();

            if ($db->transStatus() === false) {
                throw new RuntimeException('Transaksi gagal disimpan.');
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();

            throw $e instanceof RuntimeException ? $e : new RuntimeException('Transaksi gagal: ' . $e->getMessage(), 0, $e);
        }

        return $this->orders->find($orderId);
    }

    /**
     * Membatalkan pesanan dan mengembalikan stok.
     * Customer: hanya saat menunggu pembayaran. Admin/operator: sebelum dikirim.
     */
    public function cancel(array $order, string $byRole, string $reason): void
    {
        $allowed = $byRole === 'customer'
            ? ['pending_payment']
            : ['pending_payment', 'waiting_verification', 'processing'];
        if (! in_array($order['order_status'], $allowed, true)) {
            throw new RuntimeException('Pesanan dengan status ini tidak dapat dibatalkan.');
        }

        $db = db_connect();
        $db->transStart();
        foreach ((new OrderItemModel())->where('order_id', $order['id'])->findAll() as $it) {
            $db->table('products')->set('stock', 'stock + ' . (int) $it['quantity'], false)->where('id', $it['product_id'])->update();
        }
        $db->table('orders')->where('id', $order['id'])->update([
            'order_status' => 'cancelled', 'notes' => $reason, 'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $db->transComplete();

        if (! $db->transStatus()) {
            throw new RuntimeException('Gagal membatalkan pesanan.');
        }
    }

    /** Customer mengunggah bukti → pembayaran menunggu verifikasi. */
    public function submitPayment(array $order, string $proofFile): void
    {
        if (! in_array($order['order_status'], ['pending_payment'], true) || ! in_array($order['payment_status'], ['unpaid', 'rejected'], true)) {
            throw new RuntimeException('Bukti pembayaran tidak dapat diunggah untuk pesanan ini.');
        }
        $db = db_connect();
        $db->transStart();
        (new PaymentModel())->update($order['payment_id'], [
            'proof_file' => $proofFile, 'payment_status' => 'submitted', 'submitted_at' => date('Y-m-d H:i:s'), 'note' => null,
        ]);
        $this->orders->update($order['id'], ['order_status' => 'waiting_verification']);
        $db->transComplete();
    }

    /** Admin/operator memverifikasi pembayaran. */
    public function verifyPayment(array $payment, bool $approve, int $verifierId, ?string $note = null): void
    {
        if ($payment['payment_status'] !== 'submitted') {
            throw new RuntimeException('Pembayaran ini tidak sedang menunggu verifikasi.');
        }
        $db = db_connect();
        $db->transStart();
        (new PaymentModel())->update($payment['id'], [
            'payment_status' => $approve ? 'verified' : 'rejected',
            'verified_by'    => $verifierId,
            'verified_at'    => date('Y-m-d H:i:s'),
            'note'           => $note ?: null,
        ]);
        $this->orders->update($payment['order_id'], ['order_status' => $approve ? 'processing' : 'pending_payment']);
        $db->transComplete();
    }

    /** Mengirim pesanan: simpan kurir & nomor resi. */
    public function ship(array $order, string $courier, string $tracking): void
    {
        if ($order['order_status'] !== 'processing') {
            throw new RuntimeException('Hanya pesanan berstatus Diproses yang dapat dikirim.');
        }
        $db = db_connect();
        $db->transStart();
        (new ShipmentModel())->where('order_id', $order['id'])->set([
            'courier' => $courier, 'tracking_number' => $tracking, 'shipment_status' => 'shipped', 'shipped_at' => date('Y-m-d H:i:s'),
        ])->update();
        $this->orders->update($order['id'], ['order_status' => 'shipped']);
        $db->transComplete();
    }

    /** Pesanan diterima (oleh customer atau ditandai admin) → selesai. */
    public function complete(array $order): void
    {
        if ($order['order_status'] !== 'shipped') {
            throw new RuntimeException('Pesanan belum dalam pengiriman.');
        }
        $db = db_connect();
        $db->transStart();
        (new ShipmentModel())->where('order_id', $order['id'])->set([
            'shipment_status' => 'delivered', 'delivered_at' => date('Y-m-d H:i:s'),
        ])->update();
        $this->orders->update($order['id'], ['order_status' => 'completed']);
        $db->transComplete();
    }
}
