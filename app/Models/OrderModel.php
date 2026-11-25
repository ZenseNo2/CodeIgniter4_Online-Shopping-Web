<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table         = 'orders';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id', 'order_number', 'recipient_name', 'phone', 'shipping_address',
        'courier', 'shipping_cost', 'subtotal', 'total', 'order_status', 'notes',
    ];

    /** Pesanan + status pembayaran & pengiriman + nama customer. */
    public function withStatus(): self
    {
        return $this->select('orders.*, u.name AS customer_name, u.email AS customer_email')
            ->select('pay.payment_status, pay.method AS payment_method, pay.proof_file, pay.submitted_at, pay.verified_at, pay.note AS payment_note, pay.id AS payment_id')
            ->select('sh.shipment_status, sh.tracking_number, sh.shipped_at, sh.delivered_at, sh.id AS shipment_id')
            ->select('(SELECT SUM(oi.quantity) FROM order_items oi WHERE oi.order_id = orders.id) AS item_count', false)
            ->join('users u', 'u.id_user = orders.user_id')
            ->join('payments pay', 'pay.order_id = orders.id', 'left')
            ->join('shipments sh', 'sh.order_id = orders.id', 'left');
    }

    public function findByNumber(string $number): ?array
    {
        return $this->withStatus()->where('orders.order_number', $number)->first();
    }

    /** Nomor pesanan unik, contoh: SE-20261125-7F3A. */
    public function generateNumber(): string
    {
        do {
            $number = 'SE-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
        } while ($this->where('order_number', $number)->countAllResults() > 0);

        return $number;
    }
}
