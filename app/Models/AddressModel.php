<?php

namespace App\Models;

use CodeIgniter\Model;

class AddressModel extends Model
{
    protected $table         = 'addresses';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'label', 'recipient_name', 'phone', 'address', 'city', 'postal_code', 'is_default'];

    public function forUser(int $userId): array
    {
        return $this->where('user_id', $userId)->orderBy('is_default', 'DESC')->orderBy('id', 'DESC')->findAll();
    }

    /** Menjadikan satu alamat sebagai alamat utama milik user tersebut. */
    public function makeDefault(int $userId, int $addressId): void
    {
        $this->db->transStart();
        $this->builder()->where('user_id', $userId)->update(['is_default' => 0]);
        $this->builder()->where('user_id', $userId)->where('id', $addressId)->update(['is_default' => 1]);
        $this->db->transComplete();
    }

    /** Alamat lengkap dalam satu teks, disimpan sebagai snapshot di pesanan. */
    public static function format(array $a): string
    {
        return trim($a['address']) . "\n" . trim($a['city']) . (! empty($a['postal_code']) ? ' ' . trim($a['postal_code']) : '');
    }
}
