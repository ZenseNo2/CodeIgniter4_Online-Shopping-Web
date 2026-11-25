<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table         = 'categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'slug', 'description'];

    /** Kategori beserta jumlah produk (aktif dan seluruhnya). */
    public function withCounts(): array
    {
        return $this->select('categories.*')
            ->select('(SELECT COUNT(*) FROM products p WHERE p.category_id = categories.id) AS total_products', false)
            ->select("(SELECT COUNT(*) FROM products p WHERE p.category_id = categories.id AND p.status = 'active') AS active_products", false)
            ->orderBy('name')
            ->findAll();
    }

    /** [id => nama] untuk dropdown. */
    public function options(): array
    {
        return array_column($this->orderBy('name')->findAll(), 'name', 'id');
    }

    /** Membuat slug unik dari nama, mis. "Rumah Tangga" → "rumah-tangga". */
    public function uniqueSlug(string $name, ?int $exceptId = null): string
    {
        $base = url_title($name, '-', true) ?: 'kategori';
        $slug = $base;
        $i    = 2;
        while (true) {
            $q = $this->where('slug', $slug);
            if ($exceptId) {
                $q->where('id !=', $exceptId);
            }
            if ($q->countAllResults() === 0) {
                return $slug;
            }
            $slug = $base . '-' . $i++;
        }
    }
}
