<?php

namespace App\Models;

use CodeIgniter\Model;

class ShipmentModel extends Model
{
    protected $table         = 'shipments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['order_id', 'courier', 'tracking_number', 'shipment_status', 'shipped_at', 'delivered_at'];

    public function withOrder(): self
    {
        return $this->select('shipments.*, o.order_number, o.recipient_name, o.shipping_address, o.order_status, o.total, o.created_at AS order_date, u.name AS customer_name')
            ->join('orders o', 'o.id = shipments.order_id')
            ->join('users u', 'u.id_user = o.user_id');
    }
}
