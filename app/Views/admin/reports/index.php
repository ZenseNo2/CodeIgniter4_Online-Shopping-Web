<?= $this->extend('layouts/panel') ?>

<?= $this->section('subtitle') ?>
<p class="subtitle">Periode <?= tanggal_id($from) ?> – <?= tanggal_id($to) ?> · hanya pesanan yang sudah dibayar (Diproses, Dikirim, Selesai).</p>
<?= $this->endSection() ?>

<?= $this->section('actions') ?>
<a class="bar-btn" href="<?= site_url('admin/reports/export?' . http_build_query(['from' => $from, 'to' => $to])) ?>"><?= icon('download') ?><span class="hide-sm">Unduh CSV</span></a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$quick = ['7 hari' => 6, '30 hari' => 29, '90 hari' => 89];
$points = array_map(static fn ($d) => [date('d/m', strtotime($d['date'])), $d['sales'], tanggal_id($d['date']) . ': ' . rupiah($d['sales']) . ' (' . $d['orders'] . ' pesanan)'], $daily);
$maxCat = max(1, ...array_map(static fn ($c) => (float) $c['revenue'], $categories ?: [['revenue' => 0]]));
?>
<form class="toolbar date-range" method="get" action="<?= site_url('admin/reports') ?>">
  <label class="sr-only" for="from">Dari</label><input id="from" type="date" name="from" value="<?= esc($from, 'attr') ?>" max="<?= date('Y-m-d') ?>">
  <span class="muted">sampai</span>
  <label class="sr-only" for="to">Sampai</label><input id="to" type="date" name="to" value="<?= esc($to, 'attr') ?>" max="<?= date('Y-m-d') ?>">
  <button class="btn" type="submit">Tampilkan</button>
  <span class="segmented" style="margin-left:auto">
    <?php foreach ($quick as $label => $days): $f = date('Y-m-d', strtotime("-{$days} days")); ?>
      <a class="<?= $from === $f && $to === date('Y-m-d') ? 'on' : '' ?>" href="<?= site_url('admin/reports?' . http_build_query(['from' => $f, 'to' => date('Y-m-d')])) ?>"><?= $label ?></a>
    <?php endforeach ?>
  </span>
</form>

<div class="stats cols-4">
  <div class="stat"><div class="k"><span class="tile t-green"><?= icon('card') ?></span>Penjualan</div><div class="v" style="font-size:24px"><?= rupiah($summary['sales']) ?></div><div class="d">total omzet periode ini</div></div>
  <div class="stat"><div class="k"><span class="tile t-blue"><?= icon('receipt') ?></span>Pesanan dibayar</div><div class="v"><?= $summary['orders'] ?></div><div class="d">transaksi</div></div>
  <div class="stat"><div class="k"><span class="tile t-orange"><?= icon('box') ?></span>Barang terjual</div><div class="v"><?= $summary['items'] ?></div><div class="d">unit</div></div>
  <div class="stat"><div class="k"><span class="tile t-purple"><?= icon('chart') ?></span>Rata-rata</div><div class="v" style="font-size:24px"><?= rupiah($summary['avg']) ?></div><div class="d">per pesanan</div></div>
</div>

<section class="section">
  <div class="section-head"><h2>Penjualan Harian</h2></div>
  <div class="group chart"><?= view('partials/bar_chart', ['points' => $points, 'height' => 200, 'label' => 'Penjualan harian']) ?></div>
</section>

<div class="grid-2">
  <section class="section">
    <div class="section-head"><h2>Produk Terlaris</h2></div>
    <div class="table-wrap">
      <table class="list">
        <thead><tr><th>#</th><th>Produk</th><th class="text-r">Terjual</th><th class="text-r">Pendapatan</th></tr></thead>
        <tbody>
        <?php foreach ($best as $i => $b): ?>
          <tr><td class="muted"><?= $i + 1 ?></td>
            <td><div class="cell-product"><img class="thumb-sm" src="<?= product_image($b['image']) ?>" alt=""><?= esc($b['name']) ?></div></td>
            <td class="text-r"><?= (int) $b['sold'] ?></td><td class="text-r nowrap"><?= rupiah($b['revenue']) ?></td></tr>
        <?php endforeach ?>
        <?php if (! $best): ?><tr><td colspan="4" class="c muted" style="padding:22px">Belum ada penjualan pada periode ini.</td></tr><?php endif ?>
        </tbody>
      </table>
    </div>
  </section>
  <div>
    <section class="section">
      <div class="section-head"><h2>Per Kategori</h2></div>
      <div class="group" style="padding:6px 0">
        <?php foreach ($categories as $c): ?>
          <div class="hbar"><span><?= esc($c['name']) ?><span class="sub muted" style="display:block;font-size:12px"><?= (int) $c['qty'] ?> unit</span></span>
            <span class="meter c-blue"><span style="width:<?= round($c['revenue'] / $maxCat * 100) ?>%"></span></span>
            <span class="n" style="width:auto;white-space:nowrap"><?= round($c['revenue'] / max(1, $summary['sales']) * 100) ?>%</span></div>
        <?php endforeach ?>
        <?php if (! $categories): ?><div class="row empty">Belum ada data.</div><?php endif ?>
      </div>
    </section>
    <section class="section">
      <div class="section-head"><h2>Status Pesanan (periode ini)</h2></div>
      <?= view('admin/_status_bars', ['status' => $status]) ?>
    </section>
  </div>
</div>
<?= $this->endSection() ?>
