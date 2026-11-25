<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;

/**
 * Laporan penjualan per periode (Praktikum 11).
 */
class ReportController extends BaseController
{
    public function index()
    {
        [$from, $to] = $this->period();
        $daily = Stats::daily($from, $to);
        $sales = array_sum(array_column($daily, 'sales'));
        $orders = array_sum(array_column($daily, 'orders'));

        $items = db_connect()->table('order_items oi')->selectSum('oi.quantity', 'qty')
            ->join('orders o', 'o.id = oi.order_id')
            ->whereIn('o.order_status', config('Shop')->salesStatuses)
            ->where('o.created_at >=', $from . ' 00:00:00')->where('o.created_at <=', $to . ' 23:59:59')
            ->get()->getRowArray()['qty'] ?? 0;

        return view('admin/reports/index', [
            'title'      => 'Laporan Penjualan',
            'from'       => $from,
            'to'         => $to,
            'daily'      => $daily,
            'summary'    => ['sales' => $sales, 'orders' => $orders, 'avg' => $orders ? $sales / $orders : 0, 'items' => (int) $items],
            'best'       => (new ProductModel())->bestSellers(10, $from, $to),
            'categories' => Stats::byCategory($from, $to),
            'status'     => Stats::statusDistribution($from, $to),
        ]);
    }

    /** Unduh laporan harian sebagai CSV (bisa dibuka di Excel). */
    public function export()
    {
        [$from, $to] = $this->period();
        $fh = fopen('php://temp', 'w+');
        fwrite($fh, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
        fputcsv($fh, ['Tanggal', 'Jumlah Pesanan', 'Penjualan (Rp)'], ';');
        foreach (Stats::daily($from, $to) as $d) {
            fputcsv($fh, [$d['date'], $d['orders'], number_format($d['sales'], 0, '', '')], ';');
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        return $this->response->download("laporan-penjualan_{$from}_{$to}.csv", $csv)->setContentType('text/csv; charset=UTF-8');
    }

    /** Periode dari query string; default 30 hari terakhir, maksimal 366 hari. */
    private function period(): array
    {
        $to   = (string) $this->request->getGet('to');
        $from = (string) $this->request->getGet('from');
        $valid = static fn ($d) => preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) && strtotime($d) !== false;
        $to   = $valid($to) ? $to : date('Y-m-d');
        $from = $valid($from) ? $from : date('Y-m-d', strtotime($to . ' -29 days'));
        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }
        if ((strtotime($to) - strtotime($from)) / 86400 > 366) {
            $from = date('Y-m-d', strtotime($to . ' -366 days'));
        }

        return [$from, $to];
    }
}
