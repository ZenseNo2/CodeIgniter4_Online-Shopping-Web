<?= $this->extend('layouts/store') ?>
<?= $this->section('content') ?>
<?php
$sorts = ['newest' => 'Terbaru', 'popular' => 'Terlaris', 'price_asc' => 'Harga terendah', 'price_desc' => 'Harga tertinggi', 'name' => 'Nama A–Z'];
$total = $pager->getTotal();
?>
<nav class="crumbs"><a href="<?= site_url('/') ?>">Beranda</a> › <?= $current ? '<a href="' . site_url('catalog') . '">Katalog</a> › ' . esc($current['name']) : 'Katalog' ?></nav>
<h1 class="page-title"><?= esc($current['name'] ?? 'Katalog Produk') ?></h1>
<p class="page-sub"><?= esc($current['description'] ?? 'Semua produk yang sedang dijual di ShopEase.') ?></p>

<div class="catalog" style="margin-top:18px">
  <aside>
    <div class="section-head"><h2>Kategori</h2></div>
    <nav class="group">
      <a class="row <?= $category === '' ? 'is-active' : '' ?>" href="<?= query_with(['category' => null]) ?>"><span class="grow">Semua kategori</span><?php if ($category === ''): ?><?= icon('tick') ?><?php endif ?></a>
      <?php foreach ($categories as $c): ?>
        <a class="row" href="<?= query_with(['category' => $c['slug']]) ?>">
          <span class="grow"><?= esc($c['name']) ?></span>
          <span class="value"><?= (int) $c['active_products'] ?></span>
          <?php if ($category === $c['slug']): ?><?= icon('tick') ?><?php endif ?>
        </a>
      <?php endforeach ?>
    </nav>
    <div class="chips mobile-only">
      <a class="chip <?= $category === '' ? 'on' : '' ?>" href="<?= query_with(['category' => null]) ?>">Semua</a>
      <?php foreach ($categories as $c): ?>
        <a class="chip <?= $category === $c['slug'] ? 'on' : '' ?>" href="<?= query_with(['category' => $c['slug']]) ?>"><?= esc($c['name']) ?><span class="n"><?= (int) $c['active_products'] ?></span></a>
      <?php endforeach ?>
    </div>
  </aside>

  <div>
    <form class="toolbar" method="get" action="<?= current_url() ?>">
      <?php if ($category !== ''): ?><input type="hidden" name="category" value="<?= esc($category, 'attr') ?>"><?php endif ?>
      <label class="search"><?= icon('search') ?><span class="sr-only">Cari</span>
        <input type="search" name="q" value="<?= esc($q) ?>" placeholder="Cari nama atau deskripsi produk">
      </label>
      <select class="select-pill" name="sort" data-autosubmit aria-label="Urutkan">
        <?= select_options($sorts, $sort) ?>
      </select>
    </form>

    <p class="result-info">
      <?= $total ?> produk<?= $q !== '' ? ' untuk "<b>' . esc($q) . '</b>"' : '' ?>
      <?php if ($q !== '' || $category !== ''): ?> · <a href="<?= site_url('catalog') ?>">Hapus filter</a><?php endif ?>
    </p>

    <?php if ($products): ?>
      <div class="product-grid fill">
        <?php foreach ($products as $p): ?><?= view('partials/product_card', ['p' => $p]) ?><?php endforeach ?>
      </div>
      <?= $pager->links('default', 'ios') ?>
    <?php else: ?>
      <div class="empty-state"><?= icon('search') ?><b>Produk tidak ditemukan</b>Coba kata kunci lain atau pilih kategori berbeda.<br>
        <a class="btn gray" href="<?= site_url('catalog') ?>">Lihat semua produk</a></div>
    <?php endif ?>
  </div>
</div>
<?= $this->endSection() ?>
