<?= $this->extend('layouts/panel') ?>

<?= $this->section('actions') ?>
<a class="bar-btn" href="<?= site_url('admin/categories/create') ?>"><?= icon('plus') ?><span class="hide-sm">Tambah</span></a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
  <div class="section-head"><h2>Daftar Kategori</h2><span class="meta"><?= count($categories) ?> kategori</span></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>Nama</th><th class="hide-sm">Slug</th><th class="hide-sm">Deskripsi</th><th class="text-r">Produk</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($categories as $c): ?>
        <tr>
          <td><b style="font-weight:500"><?= esc($c['name']) ?></b></td>
          <td class="hide-sm"><code><?= esc($c['slug']) ?></code></td>
          <td class="hide-sm muted"><?= esc($c['description'] ?: '–') ?></td>
          <td class="text-r"><a href="<?= site_url('admin/products?category=' . $c['id']) ?>"><?= (int) $c['total_products'] ?></a><span class="sub"><?= (int) $c['active_products'] ?> aktif</span></td>
          <td class="actions">
            <a class="link-btn small" href="<?= site_url('admin/categories/' . $c['id'] . '/edit') ?>">Ubah</a>
            <form action="<?= site_url('admin/categories/' . $c['id'] . '/delete') ?>" method="post"
                  data-confirm="Hapus kategori <?= esc($c['name'], 'attr') ?>?" data-confirm-text="<?= $c['total_products'] ? 'Kategori ini masih memiliki produk sehingga tidak dapat dihapus.' : 'Tindakan ini tidak dapat dibatalkan.' ?>" data-confirm-ok="Hapus">
              <?= csrf_field() ?><button class="link-btn small danger" type="submit">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach ?>
      <?php if (! $categories): ?><tr><td colspan="5" class="c muted" style="padding:22px">Belum ada kategori.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
  <p class="section-foot">Kategori yang masih memiliki produk tidak dapat dihapus agar data produk tetap konsisten.</p>
</section>
<?= $this->endSection() ?>
