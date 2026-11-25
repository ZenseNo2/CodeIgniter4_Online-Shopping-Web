<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table         = 'payments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'order_id', 'method', 'amount', 'proof_file', 'payment_status',
        'note', 'submitted_at', 'verified_by', 'verified_at',
    ];

    /** Pembayaran + data pesanan + nama customer & verifikator. */
    public function withOrder(): self
    {
        return $this->select('payments.*, o.order_number, o.total, o.order_status, o.created_at AS order_date, u.name AS customer_name, v.name AS verifier_name')
            ->join('orders o', 'o.id = payments.order_id')
            ->join('users u', 'u.id_user = o.user_id')
            ->join('users v', 'v.id_user = payments.verified_by', 'left');
    }
}
