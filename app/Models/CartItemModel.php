<?php

namespace App\Models;

use CodeIgniter\Model;

class CartItemModel extends Model
{
    protected $table         = 'cart_items';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['cart_id', 'product_id', 'quantity'];

    /** Isi keranjang beserta data produk terkini (harga, stok, status). */
    public function detailed(int $cartId): array
    {
        return $this->select('cart_items.*, p.name, p.price, p.stock, p.image, p.status, c.name AS category_name')
            ->select('(cart_items.quantity * p.price) AS line_total', false)
            ->join('products p', 'p.id = cart_items.product_id')
            ->join('categories c', 'c.id = p.category_id')
            ->where('cart_items.cart_id', $cartId)
            ->orderBy('cart_items.created_at', 'ASC')
            ->findAll();
    }
}
