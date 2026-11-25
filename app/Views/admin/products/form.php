<?= $this->extend('layouts/panel') ?>

<?= $this->section('back') ?>
<a class="back-link" href="<?= site_url('admin/products') ?>"><?= icon('chevron-left') ?>Produk</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $st = field_value('status', $row, 'active'); ?>
<form method="post" enctype="multipart/form-data" action="<?= site_url($row ? 'admin/products/' . $row['id'] . '/update' : 'admin/products/store') ?>" style="max-width:760px">
  <?= csrf_field() ?>

  <section class="section">
    <div class="section-head"><h2>Foto Produk</h2></div>
    <div class="group form-group">
      <div class="row stack <?= has_error('image') ?>">
        <div class="image-field">
          <img id="img-preview" src="<?= product_image($row['image'] ?? null) ?>" alt="Pratinjau foto produk">
          <div>
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" data-preview="#img-preview" aria-label="Foto produk">
            <div class="muted" style="font-size:13px;margin-top:6px">JPG, PNG, atau WEBP, maksimal 2 MB.<?= $row ? ' Kosongkan bila tidak ingin mengganti foto.' : ' Opsional.' ?></div>
          </div>
        </div>
        <?= field_error('image') ?>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="section-head"><h2>Informasi Produk</h2></div>
    <div class="group form-group">
      <?= field_row('name', 'Nama produk', '<input id="name" name="name" value="' . esc(field_value('name', $row)) . '" placeholder="Wajib" required maxlength="150">') ?>
      <?= field_row('category_id', 'Kategori', '<select id="category_id" name="category_id" required>' . select_options($categories, field_value('category_id', $row), 'Pilih…') . '</select>') ?>
      <?= field_row('price', 'Harga (Rp)', '<input id="price" type="number" name="price" value="' . esc(field_value('price', $row) !== '' ? (string) (float) field_value('price', $row) : '') . '" min="0" step="1" inputmode="numeric" placeholder="mis. 250000" required>') ?>
      <?= field_row('stock', 'Stok', '<input id="stock" type="number" name="stock" value="' . esc(field_value('stock', $row, '0')) . '" min="0" step="1" required><span class="unit">unit</span>') ?>
      <div class="row stack <?= has_error('status') ?>">
        <div class="field"><label>Status</label>
          <div class="segmented" role="radiogroup">
            <input type="radio" id="st-a" name="status" value="active" <?= $st === 'active' ? 'checked' : '' ?>><label for="st-a">Aktif</label>
            <input type="radio" id="st-i" name="status" value="inactive" <?= $st === 'inactive' ? 'checked' : '' ?>><label for="st-i">Nonaktif</label>
          </div>
        </div>
        <?= field_error('status') ?>
      </div>
      <div class="row stack <?= has_error('description') ?>">
        <div class="field" style="align-items:flex-start"><label for="description" style="padding-top:13px">Deskripsi</label>
          <textarea id="description" name="description" rows="5" placeholder="Opsional"><?= esc(field_value('description', $row)) ?></textarea></div>
        <?= field_error('description') ?>
      </div>
    </div>
    <p class="section-foot">Produk nonaktif tidak tampil di katalog dan tidak dapat dimasukkan ke keranjang.</p>
  </section>

  <div class="form-actions">
    <a class="btn gray" href="<?= site_url('admin/products') ?>">Batal</a>
    <button class="btn" type="submit">Simpan</button>
  </div>
</form>
<?= $this->endSection() ?>
