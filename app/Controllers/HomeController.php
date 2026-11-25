<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

/**
 * Halaman publik: beranda, tentang, kontak (Praktikum 3).
 */
class HomeController extends BaseController
{
    public function index()
    {
        $products = new ProductModel();

        return view('frontend/home', [
            'title'       => null,
            'categories'  => (new CategoryModel())->withCounts(),
            'newest'      => $products->catalog(null, null, 'newest')->findAll(8),
            'bestSellers' => (new ProductModel())->catalog(null, null, 'popular')->findAll(4),
        ]);
    }

    public function about()
    {
        return view('frontend/about', ['title' => 'Tentang ShopEase']);
    }

    public function contact()
    {
        return view('frontend/contact', ['title' => 'Kontak']);
    }
}
