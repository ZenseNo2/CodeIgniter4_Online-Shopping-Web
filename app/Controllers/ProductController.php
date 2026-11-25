<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Katalog dan detail produk untuk customer (Praktikum 7).
 */
class ProductController extends BaseController
{
    public function catalog()
    {
        $q        = trim((string) $this->request->getGet('q'));
        $category = (string) $this->request->getGet('category');
        $sort     = (string) ($this->request->getGet('sort') ?: 'newest');
        if (! in_array($sort, ['newest', 'popular', 'price_asc', 'price_desc', 'name'], true)) {
            $sort = 'newest';
        }

        $model = new ProductModel();
        $products = $model->catalog($q ?: null, $category ?: null, $sort)->paginate(config('Shop')->perPage);

        $categories = (new CategoryModel())->withCounts();
        $current = null;
        foreach ($categories as $c) {
            if ($c['slug'] === $category) {
                $current = $c;
            }
        }

        return view('frontend/catalog', [
            'title'      => $current ? $current['name'] : 'Katalog Produk',
            'products'   => $products,
            'pager'      => $model->pager,
            'categories' => $categories,
            'current'    => $current,
            'q'          => $q,
            'category'   => $category,
            'sort'       => $sort,
        ]);
    }

    public function detail(int $id)
    {
        $model = new ProductModel();
        $product = $model->withCategory()->where('products.status', 'active')->find($id)
            ?? throw new PageNotFoundException('Produk tidak ditemukan.');

        $related = (new ProductModel())->catalog(null, $product['category_slug'])
            ->where('products.id !=', $id)->findAll(4);

        $sold = db_connect()->table('order_items oi')->selectSum('oi.quantity', 'sold')
            ->join('orders o', 'o.id = oi.order_id')
            ->where('oi.product_id', $id)->where('o.order_status !=', 'cancelled')
            ->get()->getRowArray()['sold'] ?? 0;

        return view('frontend/product', [
            'title'   => $product['name'],
            'product' => $product,
            'related' => $related,
            'sold'    => (int) $sold,
        ]);
    }
}
