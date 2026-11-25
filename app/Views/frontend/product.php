<?= $this->extend('layouts/store') ?>
<?= $this->section('content') ?>
<?php
$p = $product;
$stock = (int) $p['stock'];
$user = current_user();
$isStaff = $user && $user['role'] !== 'customer';
?>
<nav class="crumbs"><a href="<?= site_url('/') ?>">Beranda</a> › <a href="<?= site_url('catalog') ?>">Katalog</a> › <a href="<?= site_url('catalog?category=' . rawurlencode($p['category_slug'])) ?>"><?= esc($p['category_name']) ?></a></nav>

<div class="detail">
  <div class="gallery"><img src="<?= product_image($p['image']) ?>" alt="<?= esc($p['name'], 'attr') ?>"></div>
  <div>
    <a class="badge blue" href="<?= site_url('catalog?category=' . rawurlencode($p['category_slug'])) ?>"><?= esc($p['category_name']) ?></a>
    <h1><?= esc($p['name']) ?></h1>
    <div class="price-lg"><?= rupiah($p['price']) ?></div>

    <section class="section" style="margin-top:18px">
      <div class="group">
        <div class="row"><span class="grow">Ketersediaan</span>
          <?php if ($stock < 1): ?><span class="badge red">Stok habis</span>
          <?php elseif ($stock <= 5): ?><span class="badge orange">Sisa <?= $stock ?></span>
          <?php else: ?><span class="badge green">Tersedia · <?= $stock ?></span><?php endif ?>
        </div>
        <div class="row"><span class="grow">Terjual</span><span class="value"><?= $sold ?> unit</span></div>
        <div class="row"><span class="grow">Pengiriman</span><span class="value">JNE, J&amp;T, SiCepat</span></div>
      </div>
    </section>

    <?php if ($isStaff): ?>
      <div class="alert info" style="margin-top:16px"><?= icon('info') ?><div class="grow">Anda login sebagai <?= role_label($user['role']) ?>. Pembelian hanya untuk akun customer.
        <a href="<?= site_url('admin/products/' . $p['id'] . '/edit') ?>">Ubah produk ini</a></div></div>
    <?php elseif ($stock > 0): ?>
      <form class="buy-box" action="<?= site_url('cart/add') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
        <div class="qty" aria-label="Jumlah">
          <button type="button" data-step="-1" aria-label="Kurangi">−</button>
          <input type="number" name="quantity" value="1" min="1" max="<?= $stock ?>" inputmode="numeric" aria-label="Jumlah">
          <button type="button" data-step="1" aria-label="Tambah">+</button>
        </div>
        <button class="btn lg" type="submit"><?= icon('cart') ?>Tambah ke Keranjang</button>
        <button class="btn lg tinted" type="submit" name="buy_now" value="1">Beli Sekarang</button>
      </form>
      <?php if (! $user): ?><p class="section-foot" style="padding-left:0">Anda akan diminta login sebelum produk masuk keranjang.</p><?php endif ?>
    <?php else: ?>
      <button class="btn lg" disabled style="margin-top:20px">Stok habis</button>
    <?php endif ?>

    <div class="section-head" style="margin-top:26px"><h2>Deskripsi</h2></div>
    <div class="prose"><?= nl2br(esc($p['description'] ?: 'Belum ada deskripsi.')) ?></div>
  </div>
</div>

<?php if ($related): ?>
<section class="shelf">
  <div class="shelf-head"><h2>Produk Serupa</h2><a href="<?= site_url('catalog?category=' . rawurlencode($p['category_slug'])) ?>">Lihat kategori</a></div>
  <div class="product-grid">
    <?php foreach ($related as $r): ?><?= view('partials/product_card', ['p' => $r]) ?><?php endforeach ?>
  </div>
</section>
<?php endif ?>
<?= $this->endSection() ?>
