<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table         = 'order_items';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['order_id', 'product_id', 'product_name', 'price', 'quantity', 'subtotal'];

    public function forOrder(int $orderId): array
    {
        return $this->select('order_items.*, p.image')
            ->join('products p', 'p.id = order_items.product_id', 'left')
            ->where('order_items.order_id', $orderId)
            ->findAll();
    }
}
