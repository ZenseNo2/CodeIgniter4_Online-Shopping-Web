<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table         = 'products';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'category_id', 'name', 'description',
        'price', 'stock', 'image', 'status',
    ];

    /** Produk + nama & slug kategori. */
    public function withCategory(): self
    {
        return $this->select('products.*, categories.name AS category_name, categories.slug AS category_slug')
            ->join('categories', 'categories.id = products.category_id');
    }

    /**
     * Builder katalog customer: hanya produk aktif, bisa dicari,
     * difilter kategori, dan diurutkan.
     */
    public function catalog(?string $q = null, ?string $categorySlug = null, string $sort = 'newest'): self
    {
        $this->withCategory()->where('products.status', 'active');

        if ($q) {
            $this->groupStart()
                ->like('products.name', $q)
                ->orLike('products.description', $q)
                ->groupEnd();
        }
        if ($categorySlug) {
            $this->where('categories.slug', $categorySlug);
        }

        match ($sort) {
            'price_asc'  => $this->orderBy('products.price', 'ASC'),
            'price_desc' => $this->orderBy('products.price', 'DESC'),
            'name'       => $this->orderBy('products.name', 'ASC'),
            'popular'    => $this->orderBy('(SELECT COALESCE(SUM(oi.quantity),0) FROM order_items oi WHERE oi.product_id = products.id)', 'DESC', false),
            default      => $this->orderBy('products.created_at', 'DESC')->orderBy('products.id', 'DESC'),
        };

        return $this;
    }

    /** Produk terlaris berdasarkan jumlah terjual pada pesanan yang tidak dibatalkan. */
    public function bestSellers(int $limit = 5, ?string $from = null, ?string $to = null): array
    {
        $b = $this->db->table('order_items oi')
            ->select('oi.product_id, p.name, p.image, SUM(oi.quantity) AS sold, SUM(oi.subtotal) AS revenue')
            ->join('orders o', 'o.id = oi.order_id')
            ->join('products p', 'p.id = oi.product_id')
            ->whereIn('o.order_status', config('Shop')->salesStatuses);
        if ($from) {
            $b->where('o.created_at >=', $from . ' 00:00:00');
        }
        if ($to) {
            $b->where('o.created_at <=', $to . ' 23:59:59');
        }

        return $b->groupBy('oi.product_id, p.name, p.image')
            ->orderBy('sold', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }
}
