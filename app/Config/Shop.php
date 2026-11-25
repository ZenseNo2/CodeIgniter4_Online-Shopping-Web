<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Pengaturan toko ShopEase. Diakses dengan config('Shop').
 */
class Shop extends BaseConfig
{
    public string $storeName = 'ShopEase';
    public string $tagline   = 'Belanja online mudah dan aman.';

    /** Jumlah produk per halaman katalog. */
    public int $perPage = 8;

    /** Pilihan kurir beserta ongkos kirim tetap (Rupiah). */
    public array $couriers = [
        'jne'     => ['name' => 'JNE Reguler', 'cost' => 15000, 'eta' => '2–3 hari'],
        'jnt'     => ['name' => 'J&T Express', 'cost' => 14000, 'eta' => '2–4 hari'],
        'sicepat' => ['name' => 'SiCepat REG', 'cost' => 13000, 'eta' => '2–3 hari'],
    ];

    /**
     * Rekening tujuan transfer. DATA CONTOH untuk keperluan praktikum,
     * bukan rekening sungguhan.
     */
    public array $paymentMethods = [
        'transfer_demo_a' => ['name' => 'Transfer Bank A (contoh)', 'account' => '000-111-2222', 'holder' => 'ShopEase Demo'],
        'transfer_demo_b' => ['name' => 'Transfer Bank B (contoh)', 'account' => '333-444-5555', 'holder' => 'ShopEase Demo'],
    ];

    /** Batas upload (KB) dan tipe file gambar yang diterima. */
    public int $maxImageKb = 2048;

    /** Status pesanan yang dihitung sebagai penjualan pada dashboard & laporan. */
    public array $salesStatuses = ['processing', 'shipped', 'completed'];

    public array $orderStatusLabels = [
        'pending_payment'      => ['Menunggu Pembayaran', 'orange'],
        'waiting_verification' => ['Menunggu Verifikasi', 'purple'],
        'processing'           => ['Diproses', 'blue'],
        'shipped'              => ['Dikirim', 'teal'],
        'completed'            => ['Selesai', 'green'],
        'cancelled'            => ['Dibatalkan', 'gray'],
    ];

    public array $paymentStatusLabels = [
        'unpaid'    => ['Belum Dibayar', 'orange'],
        'submitted' => ['Menunggu Verifikasi', 'purple'],
        'verified'  => ['Terverifikasi', 'green'],
        'rejected'  => ['Ditolak', 'red'],
    ];

    public array $shipmentStatusLabels = [
        'pending'   => ['Belum Dikirim', 'gray'],
        'shipped'   => ['Dalam Pengiriman', 'teal'],
        'delivered' => ['Diterima', 'green'],
    ];
}
