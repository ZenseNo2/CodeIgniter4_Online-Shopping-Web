<?php

namespace App\Controllers\Admin;

/**
 * Query statistik bersama untuk dashboard admin, dashboard operator, dan laporan
 * (Praktikum 11).
 */
class Stats
{
    public static function summary(): array
    {
        $db = db_connect();
        $sales = config('Shop')->salesStatuses;

        return [
            'total_produk'   => $db->table('products')->countAllResults(),
            'total_customer' => $db->table('users')->where('role', 'customer')->countAllResults(),
            'total_pesanan'  => $db->table('orders')->countAllResults(),
            'total_penjualan' => (float) ($db->table('orders')->selectSum('total')->whereIn('order_status', $sales)->get()->getRowArray()['total'] ?? 0),
            'pesanan_pending' => $db->table('orders')->where('order_status', 'pending_payment')->countAllResults(),
            'menunggu_verifikasi' => $db->table('payments')->where('payment_status', 'submitted')->countAllResults(),
            'perlu_dikirim'  => $db->table('orders')->where('order_status', 'processing')->countAllResults(),
            'stok_menipis'   => $db->table('products')->where('status', 'active')->where('stock <=', 5)->countAllResults(),
        ];
    }

    /**
     * Penjualan harian pada rentang tanggal, termasuk hari tanpa penjualan (nilai 0).
     *
     * @return list<array{date:string, orders:int, sales:float}>
     */
    public static function daily(string $from, string $to): array
    {
        $rows = db_connect()->table('orders')
            ->select('DATE(created_at) AS tanggal, COUNT(*) AS jumlah, SUM(total) AS penjualan')
            ->whereIn('order_status', config('Shop')->salesStatuses)
            ->where('created_at >=', $from . ' 00:00:00')
            ->where('created_at <=', $to . ' 23:59:59')
            ->groupBy('DATE(created_at)')
            ->orderBy('tanggal')
            ->get()->getResultArray();
        $byDate = array_column($rows, null, 'tanggal');

        $out = [];
        for ($t = strtotime($from); $t <= strtotime($to); $t += 86400) {
            $d = date('Y-m-d', $t);
            $out[] = ['date' => $d, 'orders' => (int) ($byDate[$d]['jumlah'] ?? 0), 'sales' => (float) ($byDate[$d]['penjualan'] ?? 0)];
        }

        return $out;
    }

    /** Jumlah pesanan per status. */
    public static function statusDistribution(?string $from = null, ?string $to = null): array
    {
        $b = db_connect()->table('orders')->select('order_status, COUNT(*) AS jumlah')->groupBy('order_status');
        if ($from) {
            $b->where('created_at >=', $from . ' 00:00:00')->where('created_at <=', $to . ' 23:59:59');
        }
        $rows = array_column($b->get()->getResultArray(), 'jumlah', 'order_status');
        $out = [];
        foreach (config('Shop')->orderStatusLabels as $key => [$label, $color]) {
            $out[] = ['status' => $key, 'label' => $label, 'color' => $color, 'count' => (int) ($rows[$key] ?? 0)];
        }

        return $out;
    }

    /** Penjualan per kategori pada rentang tanggal. */
    public static function byCategory(string $from, string $to): array
    {
        return db_connect()->table('order_items oi')
            ->select('c.name, SUM(oi.quantity) AS qty, SUM(oi.subtotal) AS revenue')
            ->join('orders o', 'o.id = oi.order_id')
            ->join('products p', 'p.id = oi.product_id')
            ->join('categories c', 'c.id = p.category_id')
            ->whereIn('o.order_status', config('Shop')->salesStatuses)
            ->where('o.created_at >=', $from . ' 00:00:00')
            ->where('o.created_at <=', $to . ' 23:59:59')
            ->groupBy('c.id, c.name')
            ->orderBy('revenue', 'DESC')
            ->get()->getResultArray();
    }
}
