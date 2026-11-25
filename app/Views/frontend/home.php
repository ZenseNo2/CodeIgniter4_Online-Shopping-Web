<?= $this->extend('layouts/store') ?>
<?= $this->section('content') ?>
<?php
$tiles = ['elektronik' => ['cube-check', 't-blue'], 'fashion' => ['tag', 't-pink'], 'aksesoris' => ['star', 't-orange'], 'rumah-tangga' => ['home', 't-green'], 'olahraga' => ['clock', 't-teal']];
$art = array_slice($bestSellers ?: $newest, 0, 3);
?>
<section class="hero">
  <div>
    <span class="eyebrow">TOKO ONLINE · PRAKTIKUM CODEIGNITER 4</span>
    <h1>Belanja kebutuhan harian tanpa repot.</h1>
    <p>Cari produk, masukkan ke keranjang, bayar dengan transfer, lalu pantau status pengiriman pesanan dari satu tempat.</p>
    <div class="cta">
      <a class="btn lg" href="<?= site_url('catalog') ?>">Lihat Katalog</a>
      <?php if (! is_logged_in()): ?><a class="btn lg gray" href="<?= site_url('register') ?>">Buat Akun</a><?php endif ?>
    </div>
  </div>
  <div class="hero-art" aria-hidden="true">
    <?php foreach ($art as $p): ?><img src="<?= product_image($p['image']) ?>" alt=""><?php endforeach ?>
  </div>
</section>

<div class="perks">
  <div class="perk"><span class="tile t-green"><?= icon('shield') ?></span><div><b>Pembayaran diverifikasi</b><span>Bukti transfer dicek oleh admin</span></div></div>
  <div class="perk"><span class="tile t-blue"><?= icon('truck') ?></span><div><b>Lacak pengiriman</b><span>Nomor resi tampil di pesanan</span></div></div>
  <div class="perk"><span class="tile t-orange"><?= icon('box') ?></span><div><b>Stok selalu akurat</b><span>Dihitung ulang saat checkout</span></div></div>
</div>

<section class="shelf">
  <div class="shelf-head"><h2>Kategori</h2><a href="<?= site_url('catalog') ?>">Semua produk</a></div>
  <div class="cat-grid">
    <?php foreach ($categories as $c): [$ic, $tc] = $tiles[$c['slug']] ?? ['layers', 't-indigo']; ?>
      <a class="cat" href="<?= site_url('catalog?category=' . rawurlencode($c['slug'])) ?>">
        <span class="tile <?= $tc ?>"><?= icon($ic) ?></span>
        <div><b><?= esc($c['name']) ?></b><span style="display:block"><?= (int) $c['active_products'] ?> produk</span></div>
      </a>
    <?php endforeach ?>
  </div>
</section>

<?php if ($bestSellers): ?>
<section class="shelf">
  <div class="shelf-head"><h2>Paling Laris</h2><a href="<?= site_url('catalog?sort=popular') ?>">Lihat semua</a></div>
  <div class="product-grid">
    <?php foreach ($bestSellers as $p): ?><?= view('partials/product_card', ['p' => $p]) ?><?php endforeach ?>
  </div>
</section>
<?php endif ?>

<section class="shelf">
  <div class="shelf-head"><h2>Produk Terbaru</h2><a href="<?= site_url('catalog') ?>">Lihat semua</a></div>
  <?php if ($newest): ?>
    <div class="product-grid">
      <?php foreach ($newest as $p): ?><?= view('partials/product_card', ['p' => $p]) ?><?php endforeach ?>
    </div>
  <?php else: ?>
    <div class="empty-state"><?= icon('box') ?><b>Belum ada produk</b>Admin belum menambahkan produk aktif.</div>
  <?php endif ?>
</section>
<?= $this->endSection() ?>
