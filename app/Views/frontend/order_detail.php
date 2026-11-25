<?= $this->extend('layouts/store') ?>
<?= $this->section('content') ?>
<?php
$o = $order;
$canPay = $o['order_status'] === 'pending_payment' && in_array($o['payment_status'], ['unpaid', 'rejected'], true);
?>
<nav class="crumbs"><a href="<?= site_url('orders') ?>">Pesanan Saya</a> › <?= esc($o['order_number']) ?></nav>
<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between">
  <div>
    <h1 class="page-title"><?= esc($o['order_number']) ?></h1>
    <p class="page-sub">Dibuat <?= tanggal_id($o['created_at'], true) ?> · <?= status_badge('order', $o['order_status']) ?></p>
  </div>
</div>

<div style="margin-top:16px"><?= view('partials/timeline', ['order' => $o]) ?></div>

<div class="two-col" style="margin-top:4px">
  <div>
    <?php if ($canPay): ?>
      <section class="section">
        <div class="section-head"><h2>Pembayaran</h2><?= status_badge('payment', $o['payment_status']) ?></div>
        <div class="group">
          <?php if ($o['payment_status'] === 'rejected'): ?>
            <div class="alert error" style="margin:12px 16px 0"><?= icon('alert') ?><div class="grow"><b>Bukti pembayaran ditolak.</b> <?= esc($o['payment_note'] ?? '') ?> Silakan unggah ulang.</div></div>
          <?php endif ?>
          <div class="pay-box">
            <div class="muted" style="font-size:13px">Transfer tepat sejumlah</div>
            <div class="price-lg"><?= rupiah($o['total']) ?></div>
            <?php if ($method): ?>
              <div class="muted" style="font-size:13px;margin-top:10px"><?= esc($method['name']) ?></div>
              <div class="acc"><?= esc($method['account']) ?></div>
              <div class="muted" style="font-size:13px">a.n. <?= esc($method['holder']) ?> · rekening contoh untuk praktikum</div>
            <?php endif ?>
          </div>
          <form class="upload" action="<?= site_url('orders/' . $o['order_number'] . '/payment') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="file" name="proof" accept="image/jpeg,image/png,image/webp" required data-preview="#proof-preview" aria-label="Bukti pembayaran">
            <button class="btn" type="submit"><?= icon('upload') ?>Kirim Bukti</button>
            <img id="proof-preview" class="proof-thumb" alt="" style="max-width:120px" src="data:image/gif;base64,R0lGODlhAQABAAAAACw=">
          </form>
          <p class="note" style="padding-top:8px">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
        </div>
      </section>
    <?php elseif ($o['proof_file']): ?>
      <section class="section">
        <div class="section-head"><h2>Pembayaran</h2><?= status_badge('payment', $o['payment_status']) ?></div>
        <div class="group">
          <div class="row" style="align-items:flex-start">
            <a href="<?= site_url('orders/' . $o['order_number'] . '/proof') ?>" target="_blank" rel="noopener"><img class="proof-thumb" style="width:110px" src="<?= site_url('orders/' . $o['order_number'] . '/proof') ?>" alt="Bukti pembayaran"></a>
            <span class="grow">
              <span class="title"><?= esc($method['name'] ?? $o['payment_method']) ?></span>
              <span class="sub">Dikirim <?= tanggal_id($o['submitted_at'], true) ?></span>
              <?php if ($o['payment_status'] === 'verified'): ?><span class="sub">Diverifikasi <?= tanggal_id($o['verified_at'], true) ?></span>
              <?php elseif ($o['payment_status'] === 'submitted'): ?><span class="sub">Menunggu pengecekan oleh admin.</span><?php endif ?>
            </span>
          </div>
        </div>
      </section>
    <?php endif ?>

    <section class="section">
      <div class="section-head"><h2>Produk</h2></div>
      <div class="group">
        <?php foreach ($items as $it): ?>
          <div class="line compact">
            <img src="<?= product_image($it['image']) ?>" alt="">
            <div style="min-width:0"><div class="name"><?= esc($it['product_name']) ?></div><div class="sub"><?= (int) $it['quantity'] ?> × <?= rupiah($it['price']) ?></div></div>
            <div class="right"><?= rupiah($it['subtotal']) ?></div>
          </div>
        <?php endforeach ?>
      </div>
      <p class="section-foot">Nama dan harga tersimpan saat pesanan dibuat, sehingga tidak berubah walau produk diedit.</p>
    </section>

    <section class="section">
      <div class="section-head"><h2>Pengiriman</h2><?= status_badge('shipment', $o['shipment_status']) ?></div>
      <div class="group">
        <div class="row"><span class="grow">Penerima</span><span class="value"><?= esc($o['recipient_name']) ?> · <?= esc($o['phone']) ?></span></div>
        <div class="row" style="align-items:flex-start"><span class="grow" style="flex:none;width:110px">Alamat</span><span class="value" style="flex:1"><?= nl2br(esc($o['shipping_address'])) ?></span></div>
        <div class="row"><span class="grow">Kurir</span><span class="value"><?= esc($o['courier']) ?></span></div>
        <div class="row"><span class="grow">Nomor resi</span><span class="value"><?= $o['tracking_number'] ? '<b style="color:var(--label)">' . esc($o['tracking_number']) . '</b>' : 'Belum tersedia' ?></span></div>
        <?php if ($o['notes'] && $o['order_status'] !== 'cancelled'): ?><div class="row"><span class="grow">Catatan</span><span class="value"><?= esc($o['notes']) ?></span></div><?php endif ?>
      </div>
    </section>
  </div>

  <aside class="sticky" style="margin-top:28px">
    <div class="section-head"><h2>Rincian Biaya</h2></div>
    <div class="group summary">
      <div class="row"><span class="grow">Subtotal (<?= (int) $o['item_count'] ?> barang)</span><span class="value"><?= rupiah($o['subtotal']) ?></span></div>
      <div class="row"><span class="grow">Ongkos kirim</span><span class="value"><?= rupiah($o['shipping_cost']) ?></span></div>
      <div class="row total"><span class="grow">Total</span><span class="value"><?= rupiah($o['total']) ?></span></div>
      <?php if ($o['order_status'] === 'shipped' || $o['order_status'] === 'pending_payment'): ?>
        <div class="summary-actions" style="display:grid;gap:8px">
          <?php if ($o['order_status'] === 'shipped'): ?>
            <form action="<?= site_url('orders/' . $o['order_number'] . '/received') ?>" method="post" data-confirm="Pesanan sudah diterima?" data-confirm-text="Status pesanan akan menjadi Selesai." data-confirm-ok="Ya, Diterima">
              <?= csrf_field() ?><button class="btn lg block" type="submit"><?= icon('check') ?>Pesanan Diterima</button>
            </form>
          <?php endif ?>
          <?php if ($o['order_status'] === 'pending_payment'): ?>
            <form action="<?= site_url('orders/' . $o['order_number'] . '/cancel') ?>" method="post" data-confirm="Batalkan pesanan ini?" data-confirm-text="Stok produk akan dikembalikan dan pesanan tidak bisa dilanjutkan." data-confirm-ok="Batalkan">
              <?= csrf_field() ?><button class="btn block gray danger" type="submit">Batalkan Pesanan</button>
            </form>
          <?php endif ?>
        </div>
      <?php endif ?>
    </div>
    <p class="section-foot"><a href="<?= site_url('contact') ?>">Butuh bantuan?</a></p>
  </aside>
</div>
<?= $this->endSection() ?>
