<?= $this->extend('layouts/store') ?>
<?= $this->section('content') ?>
<h1 class="page-title">Keranjang</h1>
<p class="page-sub"><?= $count ?> barang di keranjang Anda.</p>

<?php if (! $items): ?>
  <div class="empty-state" style="margin-top:18px"><?= icon('cart') ?><b>Keranjang masih kosong</b>Temukan produk yang Anda butuhkan di katalog.<br>
    <a class="btn" href="<?= site_url('catalog') ?>">Mulai Belanja</a></div>
<?php else: ?>
<div class="two-col" style="margin-top:18px">
  <section class="section" style="margin-top:0">
    <?php if ($problems): ?>
      <div class="alert warning"><?= icon('alert') ?><div class="grow"><b>Ada item yang perlu diperbaiki sebelum checkout:</b><br><?= implode('<br>', array_map('esc', $problems)) ?></div></div>
    <?php endif ?>
    <div class="group">
      <?php foreach ($items as $it): ?>
        <div class="line">
          <a href="<?= site_url('product/' . $it['product_id']) ?>"><img src="<?= product_image($it['image']) ?>" alt=""></a>
          <div style="min-width:0">
            <a class="name" href="<?= site_url('product/' . $it['product_id']) ?>" style="color:inherit"><?= esc($it['name']) ?></a>
            <div class="sub"><?= esc($it['category_name']) ?> · <?= rupiah($it['price']) ?> / unit · stok <?= (int) $it['stock'] ?></div>
            <?php if ($it['problem']): ?><div class="warn"><?= esc($it['problem']) ?></div><?php endif ?>
            <div class="controls">
              <form action="<?= site_url('cart/update/' . $it['id']) ?>" method="post" data-autosave>
                <?= csrf_field() ?>
                <div class="qty sm">
                  <button type="button" data-step="-1" aria-label="Kurangi">−</button>
                  <input type="number" name="quantity" value="<?= (int) $it['quantity'] ?>" min="0" max="<?= max(1, (int) $it['stock']) ?>" aria-label="Jumlah <?= esc($it['name'], 'attr') ?>">
                  <button type="button" data-step="1" aria-label="Tambah">+</button>
                </div>
                <noscript><button class="link-btn small" type="submit">Simpan</button></noscript>
              </form>
              <form action="<?= site_url('cart/remove/' . $it['id']) ?>" method="post" data-confirm="Hapus <?= esc($it['name'], 'attr') ?> dari keranjang?" data-confirm-ok="Hapus">
                <?= csrf_field() ?><button class="link-btn small danger" type="submit">Hapus</button>
              </form>
            </div>
          </div>
          <div class="right"><b><?= rupiah($it['line_total']) ?></b></div>
        </div>
      <?php endforeach ?>
    </div>
    <p class="section-foot"><a href="<?= site_url('catalog') ?>">‹ Lanjut belanja</a></p>
  </section>

  <aside class="sticky">
    <div class="section-head"><h2>Ringkasan</h2></div>
    <div class="group summary">
      <div class="row"><span class="grow">Subtotal (<?= $count ?> barang)</span><span class="value"><?= rupiah($subtotal) ?></span></div>
      <div class="row"><span class="grow">Ongkos kirim</span><span class="value muted">Dipilih saat checkout</span></div>
      <div class="row total"><span class="grow">Total sementara</span><span class="value"><?= rupiah($subtotal) ?></span></div>
      <div class="summary-actions">
        <?php if ($problems): ?>
          <button class="btn lg block" disabled>Checkout</button>
        <?php else: ?>
          <a class="btn lg block" href="<?= site_url('checkout') ?>">Checkout</a>
        <?php endif ?>
      </div>
      <p class="note">Harga dan stok dihitung ulang oleh server ketika pesanan dibuat.</p>
    </div>
  </aside>
</div>
<?php endif ?>
<?= $this->endSection() ?>
