<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table         = 'carts';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id'];

    /** Keranjang milik user; dibuat otomatis bila belum ada. */
    public function forUser(int $userId): array
    {
        $cart = $this->where('user_id', $userId)->first();
        if ($cart) {
            return $cart;
        }
        $id = $this->insert(['user_id' => $userId]);

        return $this->find($id);
    }
}
