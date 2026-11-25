<?= $this->extend('layouts/panel') ?>

<?= $this->section('actions') ?>
<a class="bar-btn" href="<?= site_url('admin/products/create') ?>"><?= icon('plus') ?><span class="hide-sm">Tambah</span></a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form class="toolbar" method="get" action="<?= site_url('admin/products') ?>">
  <label class="search"><?= icon('search') ?><span class="sr-only">Cari</span>
    <input type="search" name="q" value="<?= esc($q) ?>" placeholder="Cari nama produk">
  </label>
  <select class="select-pill" name="category" data-autosubmit aria-label="Kategori"><?= select_options($categories, $category, 'Semua kategori') ?></select>
  <select class="select-pill" name="status" data-autosubmit aria-label="Status">
    <?= select_options(['active' => 'Aktif', 'inactive' => 'Nonaktif', 'low' => 'Stok ≤ 5'], $status, 'Semua status') ?>
  </select>
</form>

<section class="section">
  <div class="section-head"><h2>Daftar Produk</h2><span class="meta"><?= $pager->getTotal() ?> produk</span></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>Produk</th><th class="hide-sm">Kategori</th><th class="text-r">Harga</th><th class="text-r">Stok</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><div class="cell-product"><img class="thumb-sm" src="<?= product_image($p['image']) ?>" alt="">
            <span><a href="<?= site_url('admin/products/' . $p['id'] . '/edit') ?>"><?= esc($p['name']) ?></a><span class="sub">#<?= $p['id'] ?> · diubah <?= tanggal_id($p['updated_at']) ?></span></span></div></td>
          <td class="hide-sm"><?= esc($p['category_name']) ?></td>
          <td class="text-r nowrap"><?= rupiah($p['price']) ?></td>
          <td class="text-r"><?= $p['stock'] < 1 ? '<span class="badge red">0</span>' : ($p['stock'] <= 5 ? '<span class="badge orange">' . $p['stock'] . '</span>' : (int) $p['stock']) ?></td>
          <td><?= $p['status'] === 'active' ? '<span class="badge green">Aktif</span>' : '<span class="badge gray">Nonaktif</span>' ?></td>
          <td class="actions">
            <a class="link-btn small" href="<?= site_url('admin/products/' . $p['id'] . '/edit') ?>">Ubah</a>
            <form action="<?= site_url('admin/products/' . $p['id'] . '/toggle') ?>" method="post"><?= csrf_field() ?>
              <button class="link-btn small" type="submit"><?= $p['status'] === 'active' ? 'Nonaktifkan' : 'Aktifkan' ?></button></form>
            <form action="<?= site_url('admin/products/' . $p['id'] . '/delete') ?>" method="post"
                  data-confirm="Hapus <?= esc($p['name'], 'attr') ?>?" data-confirm-text="Produk yang sudah pernah dipesan hanya akan dinonaktifkan agar riwayat transaksi tetap utuh." data-confirm-ok="Hapus">
              <?= csrf_field() ?><button class="link-btn small danger" type="submit">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach ?>
      <?php if (! $products): ?><tr><td colspan="6" class="c muted" style="padding:22px">Tidak ada produk yang cocok.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
  <?= $pager->links('default', 'ios') ?>
</section>
<?= $this->endSection() ?>
