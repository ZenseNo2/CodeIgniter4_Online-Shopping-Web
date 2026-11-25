<?= $this->extend('layouts/store') ?>
<?= $this->section('content') ?>
<?php
$selAddr = old('address_id');
if ($selAddr === null) {
    $def = array_values(array_filter($addresses, static fn ($a) => (int) $a['is_default'] === 1));
    $selAddr = $def ? (string) $def[0]['id'] : ($addresses ? (string) $addresses[0]['id'] : 'new');
}
$selCourier = old('courier', array_key_first($couriers));
$selMethod  = old('payment_method', array_key_first($methods));
?>
<nav class="crumbs"><a href="<?= site_url('cart') ?>">Keranjang</a> › Checkout</nav>
<h1 class="page-title">Checkout</h1>
<p class="page-sub">Periksa alamat, pilih kurir dan metode pembayaran.</p>

<form action="<?= site_url('checkout') ?>" method="post" data-checkout data-subtotal="<?= (float) $subtotal ?>" novalidate>
  <?= csrf_field() ?>
  <div class="two-col" style="margin-top:18px">
    <div>
      <section class="section" style="margin-top:0">
        <div class="section-head"><h2>Alamat Pengiriman</h2><a href="<?= site_url('account#alamat') ?>">Kelola</a></div>
        <div class="group">
          <?php foreach ($addresses as $a): ?>
            <label class="row choice">
              <input type="radio" name="address_id" value="<?= (int) $a['id'] ?>" <?= $selAddr === (string) $a['id'] ? 'checked' : '' ?>>
              <span class="grow"><b><?= esc($a['recipient_name']) ?></b> · <?= esc($a['phone']) ?> <?= $a['is_default'] ? '<span class="badge blue">Utama</span>' : '' ?>
                <span class="sub"><?= esc($a['label']) ?> — <?= esc($a['address']) ?>, <?= esc($a['city']) ?> <?= esc($a['postal_code']) ?></span></span>
            </label>
          <?php endforeach ?>
          <label class="row choice">
            <input type="radio" name="address_id" value="new" <?= $selAddr === 'new' ? 'checked' : '' ?>>
            <span class="grow"><b>Gunakan alamat baru</b></span>
          </label>
        </div>

        <div class="new-address" <?= $selAddr === 'new' ? '' : 'hidden' ?>>
          <div class="group form-group" style="margin-top:10px">
            <?= field_row('recipient_name', 'Nama penerima', '<input id="recipient_name" name="recipient_name" data-req value="' . esc(old('recipient_name', $user['name'])) . '" maxlength="100">') ?>
            <?= field_row('phone', 'Telepon', '<input id="phone" type="tel" name="phone" data-req value="' . esc(old('phone', $user['phone'] ?? '')) . '" placeholder="08xxxxxxxxxx">') ?>
            <?= field_row('address', 'Alamat', '<input id="address" name="address" data-req value="' . esc(old('address', $user['address'] ?? '')) . '" placeholder="Jalan, nomor, RT/RW, kelurahan">') ?>
            <?= field_row('city', 'Kota', '<input id="city" name="city" data-req value="' . esc(old('city')) . '" maxlength="100">') ?>
            <?= field_row('postal_code', 'Kode pos', '<input id="postal_code" name="postal_code" inputmode="numeric" value="' . esc(old('postal_code')) . '" placeholder="Opsional" maxlength="10">') ?>
            <label class="check-row"><input type="checkbox" name="save_address" value="1" <?= old('save_address') ? 'checked' : '' ?>>Simpan ke daftar alamat saya</label>
          </div>
        </div>
      </section>

      <section class="section">
        <div class="section-head"><h2>Kurir</h2></div>
        <div class="group <?= has_error('courier') ?>">
          <?php foreach ($couriers as $key => $c): ?>
            <label class="row choice">
              <input type="radio" name="courier" value="<?= esc($key, 'attr') ?>" data-cost="<?= (float) $c['cost'] ?>" <?= $selCourier === $key ? 'checked' : '' ?>>
              <span class="grow"><b><?= esc($c['name']) ?></b><span class="sub">Estimasi <?= esc($c['eta']) ?></span></span>
              <span class="value"><?= rupiah($c['cost']) ?></span>
            </label>
          <?php endforeach ?>
        </div>
        <?= field_error('courier') ?>
      </section>

      <section class="section">
        <div class="section-head"><h2>Metode Pembayaran</h2></div>
        <div class="group">
          <?php foreach ($methods as $key => $m): ?>
            <label class="row choice">
              <input type="radio" name="payment_method" value="<?= esc($key, 'attr') ?>" <?= $selMethod === $key ? 'checked' : '' ?>>
              <span class="grow"><b><?= esc($m['name']) ?></b><span class="sub">Rekening <?= esc($m['account']) ?> a.n. <?= esc($m['holder']) ?></span></span>
            </label>
          <?php endforeach ?>
        </div>
        <p class="section-foot">Bukti transfer diunggah setelah pesanan dibuat. Rekening di atas hanya contoh untuk praktikum.</p>
      </section>

      <section class="section">
        <div class="section-head"><h2>Catatan untuk Penjual</h2></div>
        <div class="group form-group" style="padding:0 16px">
          <textarea class="plain" name="notes" maxlength="255" placeholder="Opsional, mis. warna atau patokan alamat"><?= esc(old('notes')) ?></textarea>
        </div>
        <?= field_error('notes') ?>
      </section>
    </div>

    <aside class="sticky">
      <div class="section-head"><h2>Pesanan (<?= $count ?> barang)</h2></div>
      <div class="group summary">
        <?php foreach ($items as $it): ?>
          <div class="line compact">
            <img src="<?= product_image($it['image']) ?>" alt="">
            <div style="min-width:0"><div class="name" style="font-size:14px"><?= esc($it['name']) ?></div><div class="sub"><?= (int) $it['quantity'] ?> × <?= rupiah($it['price']) ?></div></div>
            <div class="right" style="font-size:14px"><?= rupiah($it['line_total']) ?></div>
          </div>
        <?php endforeach ?>
        <div class="row"><span class="grow">Subtotal</span><span class="value"><?= rupiah($subtotal) ?></span></div>
        <div class="row"><span class="grow">Ongkos kirim</span><span class="value" data-ship>–</span></div>
        <div class="row total"><span class="grow">Total bayar</span><span class="value" data-total><?= rupiah($subtotal) ?></span></div>
        <div class="summary-actions"><button class="btn lg block" type="submit">Buat Pesanan</button></div>
        <p class="note">Dengan menekan tombol di atas, stok produk langsung dipesan untuk Anda.</p>
      </div>
    </aside>
  </div>
</form>
<?= $this->endSection() ?>
