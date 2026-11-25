<?php

namespace App\Controllers;

use App\Libraries\CartService;

/**
 * Keranjang belanja customer (Praktikum 8). Dilindungi filter auth:customer.
 */
class CartController extends BaseController
{
    private CartService $cart;

    public function __construct()
    {
        $this->cart = new CartService();
    }

    public function index()
    {
        return view('frontend/cart', ['title' => 'Keranjang'] + $this->cart->summary($this->userId()));
    }

    public function add()
    {
        $productId = (int) $this->request->getPost('product_id');
        $quantity  = (int) $this->request->getPost('quantity');

        if ($error = $this->cart->add($this->userId(), $productId, $quantity)) {
            return redirect()->back()->with('error', $error);
        }

        if ($this->request->getPost('buy_now')) {
            return redirect()->to('/checkout');
        }

        return redirect()->to('/cart')->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(int $itemId)
    {
        $qty = (int) $this->request->getPost('quantity');
        if ($error = $this->cart->update($this->userId(), $itemId, $qty)) {
            return redirect()->to('/cart')->with('error', $error);
        }

        return redirect()->to('/cart')->with('success', $qty < 1 ? 'Item dihapus dari keranjang.' : 'Jumlah diperbarui.');
    }

    public function remove(int $itemId)
    {
        if (! $this->cart->remove($this->userId(), $itemId)) {
            return redirect()->to('/cart')->with('error', 'Item keranjang tidak ditemukan.');
        }

        return redirect()->to('/cart')->with('success', 'Item dihapus dari keranjang.');
    }
}
