<?= $this->extend('layouts/panel') ?>

<?= $this->section('back') ?>
<a class="back-link" href="<?= site_url('admin/categories') ?>"><?= icon('chevron-left') ?>Kategori</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form method="post" action="<?= site_url($row ? 'admin/categories/' . $row['id'] . '/update' : 'admin/categories/store') ?>" style="max-width:720px">
  <?= csrf_field() ?>
  <section class="section">
    <div class="section-head"><h2>Informasi Kategori</h2></div>
    <div class="group form-group">
      <?= field_row('name', 'Nama kategori', '<input id="name" name="name" value="' . esc(field_value('name', $row)) . '" placeholder="Wajib, mis. Elektronik" required maxlength="100" autofocus>') ?>
      <div class="row stack <?= has_error('description') ?>">
        <div class="field" style="align-items:flex-start"><label for="description" style="padding-top:13px">Deskripsi</label>
          <textarea id="description" name="description" placeholder="Opsional"><?= esc(field_value('description', $row)) ?></textarea></div>
        <?= field_error('description') ?>
      </div>
    </div>
    <p class="section-foot">Slug URL dibuat otomatis dari nama<?= $row ? ' (saat ini <code>' . esc($row['slug']) . '</code>)' : '' ?>.</p>
  </section>
  <div class="form-actions">
    <a class="btn gray" href="<?= site_url('admin/categories') ?>">Batal</a>
    <button class="btn" type="submit">Simpan</button>
  </div>
</form>
<?= $this->endSection() ?>
