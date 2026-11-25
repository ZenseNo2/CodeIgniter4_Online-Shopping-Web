<?php
/**
 * Layout panel admin/operator (sidebar seperti Settings di iPadOS).
 * Section: 'content', 'actions', 'back', 'subtitle'.
 */
$user = current_user();
$db = db_connect();
$waitingPay = $db->table('payments')->where('payment_status', 'submitted')->countAllResults();
$toShip = $db->table('orders')->where('order_status', 'processing')->countAllResults();

$menu = [
    'Umum' => [
        [role_home($user['role']), $user['role'] === 'admin' ? 'Dashboard' : 'Antrean Kerja', 'chart', 't-blue', 0, true],
        ['/', 'Lihat Toko', 'bag', 't-gray', 0, false],
    ],
    'Katalog' => [
        ['admin/categories', 'Kategori', 'layers', 't-indigo', 0, false],
        ['admin/products', 'Produk', 'box', 't-orange', 0, false],
    ],
    'Transaksi' => [
        ['admin/orders', 'Pesanan', 'receipt', 't-green', 0, false],
        ['admin/payments', 'Pembayaran', 'card', 't-purple', $waitingPay, false],
        ['admin/shipments', 'Pengiriman', 'truck', 't-teal', $toShip, false],
    ],
    'Analitik' => [
        ['admin/reports', 'Laporan Penjualan', 'chart', 't-pink', 0, false],
    ],
];
if ($user['role'] === 'admin') {
    $menu['Administrasi'] = [['admin/users', 'Pengguna & Role', 'users', 't-red', 0, false]];
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#f2f2f7" media="(prefers-color-scheme: light)">
  <meta name="theme-color" content="#000000" media="(prefers-color-scheme: dark)">
  <title><?= esc($title ?? 'Panel') ?> · ShopEase Admin</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<?= view('partials/sprite') ?>

<div class="app">
  <aside class="sidebar" aria-label="Navigasi panel">
    <div class="sidebar-brand"><b>ShopEase</b><span>Panel <?= role_label($user['role']) ?></span></div>

    <div class="profile-cell">
      <span class="avatar"><?= esc(inisial($user['name'])) ?></span>
      <span class="who"><b><?= esc($user['name']) ?></b><span><?= role_label($user['role']) ?> · <?= esc($user['email']) ?></span></span>
    </div>

    <?php foreach ($menu as $grup => $items): ?>
      <div class="nav-group-title"><?= esc($grup) ?></div>
      <nav class="nav-group">
        <?php foreach ($items as [$url, $label, $ic, $warna, $count, $exact]): ?>
          <a class="nav-item <?= $url === '/' ? '' : menu_active($url, $exact) ?>" href="<?= site_url($url) ?>">
            <span class="tile <?= $warna ?>"><?= icon($ic) ?></span>
            <span class="label"><?= esc($label) ?></span>
            <?php if ($count > 0): ?><span class="badge red"><?= $count ?></span><?php endif ?>
          </a>
        <?php endforeach ?>
      </nav>
    <?php endforeach ?>

    <form action="<?= site_url('logout') ?>" method="post">
      <?= csrf_field() ?>
      <button class="logout-btn" type="submit">Keluar</button>
    </form>
  </aside>

  <div class="main">
    <header class="navbar">
      <div class="nav-left">
        <button class="menu-btn" type="button" data-open-nav aria-label="Buka menu"><?= icon('sidebar') ?></button>
        <?= $this->renderSection('back') ?>
      </div>
      <div class="nav-title"><?= esc($title ?? '') ?></div>
      <div class="nav-right"><?= $this->renderSection('actions') ?></div>
    </header>

    <main class="content">
      <h1 class="large-title"><?= esc($title ?? '') ?></h1>
      <?= $this->renderSection('subtitle') ?>
      <?= view('partials/flash') ?>
      <?= $this->renderSection('content') ?>
    </main>
  </div>
</div>

<nav class="tabbar" aria-label="Menu utama">
  <a class="<?= menu_active(role_home($user['role']), true) ?>" href="<?= site_url(role_home($user['role'])) ?>"><?= icon('chart') ?><span><?= $user['role'] === 'admin' ? 'Dashboard' : 'Antrean' ?></span></a>
  <a class="<?= menu_active('admin/orders') ?>" href="<?= site_url('admin/orders') ?>"><?= icon('receipt') ?><span>Pesanan</span></a>
  <a class="<?= menu_active('admin/payments') ?>" href="<?= site_url('admin/payments') ?>"><?= icon('card') ?><span>Bayar<?= $waitingPay ? " ({$waitingPay})" : '' ?></span></a>
  <a class="<?= menu_active('admin/products') ?>" href="<?= site_url('admin/products') ?>"><?= icon('box') ?><span>Produk</span></a>
  <button type="button" data-open-nav><?= icon('sidebar') ?><span>Menu</span></button>
</nav>

<?= view('partials/dialog') ?>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
