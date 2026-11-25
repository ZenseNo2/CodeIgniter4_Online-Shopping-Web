<?= $this->extend('layouts/store') ?>
<?= $this->section('content') ?>
<h1 class="page-title">Tentang ShopEase</h1>
<p class="page-sub">Aplikasi toko online sederhana untuk Hands-On Lab Web Framework.</p>
<div class="prose" style="margin-top:18px">
  <h2>Apa itu ShopEase?</h2>
  <p>ShopEase adalah toko online yang dibangun dengan CodeIgniter 4 dan MySQL. Pengunjung dapat melihat katalog, customer dapat berbelanja dan memantau pesanan, sedangkan admin dan operator mengelola produk, memverifikasi pembayaran, dan mengirim pesanan.</p>
  <h2>Alur belanja</h2>
  <ul>
    <li>Pilih produk di katalog lalu masukkan ke keranjang.</li>
    <li>Checkout: pilih alamat, kurir, dan metode pembayaran.</li>
    <li>Transfer sesuai total, lalu unggah bukti pembayaran di halaman pesanan.</li>
    <li>Admin/operator memverifikasi pembayaran dan mengirim pesanan dengan nomor resi.</li>
    <li>Setelah barang tiba, tekan <b>Pesanan Diterima</b>.</li>
  </ul>
  <h2>Catatan</h2>
  <p>Semua produk, rekening, dan transaksi di aplikasi ini adalah data contoh untuk keperluan praktikum. Tidak ada pembayaran sungguhan yang diproses.</p>
</div>
<?= $this->endSection() ?>
