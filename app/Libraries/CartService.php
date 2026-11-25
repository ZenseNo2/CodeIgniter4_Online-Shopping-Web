<?php

namespace App\Libraries;

use App\Models\CartItemModel;
use App\Models\CartModel;
use App\Models\ProductModel;

/**
 * Aturan keranjang belanja (Praktikum 8).
 * Semua pengecekan stok dan status produk dilakukan di server.
 */
class CartService
{
    private CartModel $carts;
    private CartItemModel $items;
    private ProductModel $products;

    public function __construct()
    {
        $this->carts    = new CartModel();
        $this->items    = new CartItemModel();
        $this->products = new ProductModel();
    }

    /**
     * Isi keranjang lengkap dengan subtotal, total, dan peringatan stok.
     *
     * @return array{cart: array, items: list<array>, subtotal: float, count: int, problems: list<string>}
     */
    public function summary(int $userId): array
    {
        $cart  = $this->carts->forUser($userId);
        $items = $this->items->detailed((int) $cart['id']);
        $problems = [];
        $subtotal = 0.0;
        $count = 0;

        foreach ($items as &$it) {
            $it['problem'] = null;
            if ($it['status'] !== 'active') {
                $it['problem'] = 'Produk sedang tidak dijual.';
            } elseif ((int) $it['stock'] < 1) {
                $it['problem'] = 'Stok habis.';
            } elseif ((int) $it['quantity'] > (int) $it['stock']) {
                $it['problem'] = 'Stok tersisa ' . $it['stock'] . '.';
            }
            if ($it['problem']) {
                $problems[] = $it['name'] . ': ' . $it['problem'];
            }
            $subtotal += (float) $it['line_total'];
            $count += (int) $it['quantity'];
        }
        unset($it);

        return ['cart' => $cart, 'items' => $items, 'subtotal' => $subtotal, 'count' => $count, 'problems' => $problems];
    }

    /**
     * Menambah produk. Bila produk sudah ada, jumlahnya ditambahkan.
     *
     * @return string|null pesan kesalahan, atau null bila berhasil
     */
    public function add(int $userId, int $productId, int $quantity): ?string
    {
        if ($quantity < 1) {
            return 'Jumlah tidak valid.';
        }
        $product = $this->products->where('status', 'active')->find($productId);
        if (! $product) {
            return 'Produk tidak ditemukan atau sedang tidak dijual.';
        }

        $cart     = $this->carts->forUser($userId);
        $existing = $this->items->where('cart_id', $cart['id'])->where('product_id', $productId)->first();
        $newQty   = $quantity + (int) ($existing['quantity'] ?? 0);

        if ((int) $product['stock'] < $newQty) {
            return 'Stok tidak mencukupi. Tersedia ' . $product['stock'] . ($existing ? ', di keranjang sudah ada ' . $existing['quantity'] : '') . '.';
        }

        if ($existing) {
            $this->items->update($existing['id'], ['quantity' => $newQty]);
        } else {
            $this->items->insert(['cart_id' => $cart['id'], 'product_id' => $productId, 'quantity' => $quantity]);
        }
        $this->carts->update($cart['id'], ['user_id' => $userId]); // menyegarkan updated_at

        return null;
    }

    /** Mengubah jumlah satu item milik user. Jumlah 0 berarti hapus. */
    public function update(int $userId, int $itemId, int $quantity): ?string
    {
        $item = $this->ownedItem($userId, $itemId);
        if (! $item) {
            return 'Item keranjang tidak ditemukan.';
        }
        if ($quantity < 1) {
            $this->items->delete($itemId);

            return null;
        }
        $product = $this->products->find($item['product_id']);
        if ((int) $product['stock'] < $quantity) {
            return 'Stok ' . $product['name'] . ' hanya tersisa ' . $product['stock'] . '.';
        }
        $this->items->update($itemId, ['quantity' => $quantity]);

        return null;
    }

    public function remove(int $userId, int $itemId): bool
    {
        if (! $this->ownedItem($userId, $itemId)) {
            return false;
        }

        return (bool) $this->items->delete($itemId);
    }

    public function clear(int $cartId): void
    {
        $this->items->where('cart_id', $cartId)->delete();
    }

    /** Item hanya boleh diubah oleh pemilik keranjangnya. */
    private function ownedItem(int $userId, int $itemId): ?array
    {
        return $this->items->select('cart_items.*')
            ->join('carts', 'carts.id = cart_items.cart_id')
            ->where('carts.user_id', $userId)
            ->where('cart_items.id', $itemId)
            ->first();
    }
}
