<?php
/**
 * Layout storefront (pengunjung & customer).
 * Section: 'content'. Variabel opsional: $title.
 */
$user  = current_user();
$isCus = ($user['role'] ?? null) === 'customer';
$cart  = cart_count();
$shop  = config('Shop');
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#f2f2f7" media="(prefers-color-scheme: light)">
  <meta name="theme-color" content="#000000" media="(prefers-color-scheme: dark)">
  <title><?= esc(isset($title) ? $title . ' · ' . $shop->storeName : $shop->storeName . ' — ' . $shop->tagline) ?></title>
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<?= view('partials/sprite') ?>
<div class="store">
  <header class="store-header">
    <div class="wrap">
      <a class="brand" href="<?= site_url('/') ?>"><span class="brand-mark"><?= icon('bag') ?></span><?= esc($shop->storeName) ?></a>

      <nav class="store-nav" aria-label="Navigasi utama">
        <a class="<?= menu_active('', true) ?>" href="<?= site_url('/') ?>">Beranda</a>
        <a class="<?= menu_active('catalog') ?> <?= menu_active('product') ?>" href="<?= site_url('catalog') ?>">Katalog</a>
        <a class="<?= menu_active('about') ?>" href="<?= site_url('about') ?>">Tentang</a>
        <a class="<?= menu_active('contact') ?>" href="<?= site_url('contact') ?>">Kontak</a>
      </nav>

      <form class="search header-search" action="<?= site_url('catalog') ?>" method="get" role="search">
        <?= icon('search') ?><label class="sr-only" for="hq">Cari produk</label>
        <input id="hq" type="search" name="q" value="<?= esc((string) service('request')->getGet('q')) ?>" placeholder="Cari produk">
      </form>

      <div class="header-actions">
        <?php if (! $user || $isCus): ?>
          <a class="icon-btn" href="<?= site_url('cart') ?>" aria-label="Keranjang (<?= $cart ?> barang)">
            <?= icon('cart') ?><?php if ($cart > 0): ?><span class="count-badge"><?= $cart > 99 ? '99+' : $cart ?></span><?php endif ?>
          </a>
        <?php endif ?>

        <?php if ($user): ?>
          <details class="menu">
            <summary><span class="avatar sm"><?= esc(inisial($user['name'])) ?></span><span class="who"><?= esc($user['name']) ?></span></summary>
            <div class="menu-panel">
              <div class="head"><b><?= esc($user['name']) ?></b><span><?= esc($user['email']) ?> · <?= role_label($user['role']) ?></span></div>
              <?php if ($isCus): ?>
                <a href="<?= site_url('account') ?>"><?= icon('person') ?>Akun Saya</a>
                <a href="<?= site_url('orders') ?>"><?= icon('receipt') ?>Pesanan Saya</a>
                <a href="<?= site_url('cart') ?>"><?= icon('cart') ?>Keranjang</a>
              <?php else: ?>
                <a href="<?= site_url(role_home($user['role'])) ?>"><?= icon('chart') ?>Buka Panel <?= role_label($user['role']) ?></a>
              <?php endif ?>
              <form action="<?= site_url('logout') ?>" method="post"><?= csrf_field() ?>
                <button type="submit" class="danger"><?= icon('logout') ?>Keluar</button>
              </form>
            </div>
          </details>
        <?php else: ?>
          <a class="btn small gray hide-sm" href="<?= site_url('login') ?>">Masuk</a>
          <a class="btn small" href="<?= site_url('register') ?>">Daftar</a>
        <?php endif ?>
      </div>
    </div>
  </header>

  <main class="store-main">
    <div class="wrap">
      <?= view('partials/flash') ?>
      <?= $this->renderSection('content') ?>
    </div>
  </main>

  <footer class="store-footer">
    <div class="wrap">
      <span>© <?= date('Y') ?> <?= esc($shop->storeName) ?> · Proyek praktikum Web Framework (CodeIgniter 4). Data produk dan transaksi bersifat fiktif.</span>
      <span><a href="<?= site_url('about') ?>">Tentang</a><a href="<?= site_url('contact') ?>">Kontak</a></span>
    </div>
  </footer>
</div>

<nav class="tabbar" aria-label="Menu">
  <a class="<?= menu_active('', true) ?>" href="<?= site_url('/') ?>"><?= icon('home') ?><span>Beranda</span></a>
  <a class="<?= menu_active('catalog') ?>" href="<?= site_url('catalog') ?>"><?= icon('grid') ?><span>Katalog</span></a>
  <?php if (! $user || $isCus): ?>
    <a class="<?= menu_active('cart') ?>" href="<?= site_url('cart') ?>"><?= icon('cart') ?><span>Keranjang<?= $cart ? " ({$cart})" : '' ?></span></a>
    <a class="<?= menu_active('orders') ?>" href="<?= site_url('orders') ?>"><?= icon('receipt') ?><span>Pesanan</span></a>
  <?php endif ?>
  <?php if ($user && $isCus): ?>
    <a class="<?= menu_active('account') ?>" href="<?= site_url('account') ?>"><?= icon('person') ?><span>Akun</span></a>
  <?php elseif ($user): ?>
    <a href="<?= site_url(role_home($user['role'])) ?>"><?= icon('chart') ?><span>Panel</span></a>
  <?php else: ?>
    <a class="<?= menu_active('login') ?>" href="<?= site_url('login') ?>"><?= icon('person') ?><span>Masuk</span></a>
  <?php endif ?>
</nav>

<?= view('partials/dialog') ?>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
