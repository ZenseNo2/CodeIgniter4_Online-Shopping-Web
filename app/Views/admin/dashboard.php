<?= $this->extend('layouts/panel') ?>

<?= $this->section('subtitle') ?>
<p class="subtitle"><?= hari_ini_id() ?> · Ringkasan toko hari ini</p>
<?= $this->endSection() ?>

<?= $this->section('actions') ?>
<a class="bar-btn" href="<?= site_url('admin/reports') ?>"><?= icon('chart') ?><span class="hide-sm">Laporan</span></a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$sum14 = array_sum(array_column($daily, 'sales'));
$points = array_map(static fn ($d) => [date('d', strtotime($d['date'])), $d['sales'], tanggal_id($d['date']) . ': ' . rupiah($d['sales']) . ' (' . $d['orders'] . ' pesanan)'], $daily);
?>
<div class="stats cols-4">
  <div class="stat"><div class="k"><span class="tile t-green"><?= icon('card') ?></span>Total penjualan</div><div class="v" style="font-size:24px"><?= rupiah($stats['total_penjualan']) ?></div><div class="d">pesanan dibayar</div></div>
  <div class="stat"><div class="k"><span class="tile t-blue"><?= icon('receipt') ?></span>Total pesanan</div><a class="v" href="<?= site_url('admin/orders') ?>"><?= $stats['total_pesanan'] ?></a><div class="d"><?= $stats['pesanan_pending'] ?> menunggu pembayaran</div></div>
  <div class="stat"><div class="k"><span class="tile t-orange"><?= icon('box') ?></span>Produk</div><a class="v" href="<?= site_url('admin/products') ?>"><?= $stats['total_produk'] ?></a><div class="d"><?= $stats['stok_menipis'] ?> stok menipis</div></div>
  <div class="stat"><div class="k"><span class="tile t-indigo"><?= icon('users') ?></span>Customer</div><a class="v" href="<?= site_url('admin/users?role=customer') ?>"><?= $stats['total_customer'] ?></a><div class="d">akun terdaftar</div></div>
</div>

<?php if ($stats['menunggu_verifikasi'] || $stats['perlu_dikirim']): ?>
  <div class="alert info"><?= icon('info') ?><div class="grow">
    <?php if ($stats['menunggu_verifikasi']): ?><a href="<?= site_url('admin/payments') ?>"><b><?= $stats['menunggu_verifikasi'] ?> pembayaran</b></a> menunggu verifikasi. <?php endif ?>
    <?php if ($stats['perlu_dikirim']): ?><a href="<?= site_url('admin/shipments') ?>"><b><?= $stats['perlu_dikirim'] ?> pesanan</b></a> siap dikirim.<?php endif ?>
  </div></div>
<?php endif ?>

<div class="grid-2">
  <section class="section">
    <div class="section-head"><h2>Penjualan 14 Hari Terakhir</h2><span class="meta"><?= rupiah($sum14) ?></span></div>
    <div class="group chart"><?= view('partials/bar_chart', ['points' => $points, 'label' => 'Penjualan harian 14 hari terakhir']) ?></div>
  </section>
  <section class="section">
    <div class="section-head"><h2>Status Pesanan</h2></div>
    <?= view('admin/_status_bars', ['status' => $status]) ?>
  </section>
</div>

<div class="grid-2">
  <section class="section">
    <div class="section-head"><h2>Pesanan Terbaru</h2><a href="<?= site_url('admin/orders') ?>">Lihat semua</a></div>
    <div class="table-wrap">
      <table class="list">
        <thead><tr><th>Pesanan</th><th class="hide-sm">Customer</th><th class="text-r">Total</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($recent as $o): ?>
          <tr>
            <td class="nowrap"><a href="<?= site_url('admin/orders/' . $o['id']) ?>"><?= esc($o['order_number']) ?></a><span class="sub"><?= tanggal_id($o['created_at'], true) ?></span></td>
            <td class="hide-sm"><?= esc($o['customer_name']) ?></td>
            <td class="text-r nowrap"><?= rupiah($o['total']) ?></td>
            <td><?= status_badge('order', $o['order_status']) ?></td>
          </tr>
        <?php endforeach ?>
        <?php if (! $recent): ?><tr><td colspan="4" class="c muted" style="padding:22px">Belum ada pesanan.</td></tr><?php endif ?>
        </tbody>
      </table>
    </div>
  </section>
  <div>
    <section class="section">
      <div class="section-head"><h2>Produk Terlaris</h2></div>
      <div class="group">
        <?php foreach ($best as $i => $b): ?>
          <div class="row"><span class="muted" style="width:16px"><?= $i + 1 ?></span><img class="thumb-sm" src="<?= product_image($b['image']) ?>" alt="">
            <span class="grow"><span class="title"><?= esc($b['name']) ?></span><span class="sub"><?= (int) $b['sold'] ?> terjual · <?= rupiah($b['revenue']) ?></span></span></div>
        <?php endforeach ?>
        <?php if (! $best): ?><div class="row empty">Belum ada penjualan.</div><?php endif ?>
      </div>
    </section>
    <section class="section">
      <div class="section-head"><h2>Stok Menipis</h2><a href="<?= site_url('admin/products?status=low') ?>">Kelola</a></div>
      <div class="group">
        <?php foreach ($lowStock as $p): ?>
          <a class="row" href="<?= site_url('admin/products/' . $p['id'] . '/edit') ?>"><img class="thumb-sm" src="<?= product_image($p['image']) ?>" alt="">
            <span class="grow"><?= esc($p['name']) ?></span><span class="badge <?= $p['stock'] < 1 ? 'red' : 'orange' ?>"><?= $p['stock'] < 1 ? 'Habis' : 'Sisa ' . $p['stock'] ?></span><?= icon('chevron-right', 'chev') ?></a>
        <?php endforeach ?>
        <?php if (! $lowStock): ?><div class="row empty">Semua stok aman.</div><?php endif ?>
      </div>
    </section>
  </div>
</div>
<?= $this->endSection() ?>
