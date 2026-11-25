<?= $this->extend('layouts/panel') ?>

<?= $this->section('back') ?>
<a class="back-link" href="<?= site_url('admin/orders') ?>"><?= icon('chevron-left') ?>Pesanan</a>
<?= $this->endSection() ?>

<?= $this->section('subtitle') ?>
<p class="subtitle"><?= esc($order['customer_name']) ?> · <?= tanggal_id($order['created_at'], true) ?> · <?= status_badge('order', $order['order_status']) ?></p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $o = $order; $canCancel = in_array($o['order_status'], ['pending_payment', 'waiting_verification', 'processing'], true); ?>
<?= view('partials/timeline', ['order' => $o]) ?>

<div class="grid-2">
  <div>
    <section class="section">
      <div class="section-head"><h2>Produk</h2><span class="meta"><?= (int) $o['item_count'] ?> barang</span></div>
      <div class="group">
        <?php foreach ($items as $it): ?>
          <div class="line compact">
            <img src="<?= product_image($it['image']) ?>" alt="">
            <div style="min-width:0"><div class="name"><?= esc($it['product_name']) ?></div><div class="sub"><?= (int) $it['quantity'] ?> × <?= rupiah($it['price']) ?></div></div>
            <div class="right"><?= rupiah($it['subtotal']) ?></div>
          </div>
        <?php endforeach ?>
      </div>
    </section>
    <section class="section">
      <div class="section-head"><h2>Rincian Biaya</h2></div>
      <div class="group summary">
        <div class="row"><span class="grow">Subtotal</span><span class="value"><?= rupiah($o['subtotal']) ?></span></div>
        <div class="row"><span class="grow">Ongkos kirim (<?= esc($o['courier']) ?>)</span><span class="value"><?= rupiah($o['shipping_cost']) ?></span></div>
        <div class="row total"><span class="grow">Total</span><span class="value"><?= rupiah($o['total']) ?></span></div>
      </div>
    </section>
  </div>

  <div>
    <section class="section">
      <div class="section-head"><h2>Pembayaran</h2><?= status_badge('payment', $o['payment_status']) ?></div>
      <div class="group">
        <div class="row"><span class="grow">Metode</span><span class="value"><?= esc(config('Shop')->paymentMethods[$o['payment_method']]['name'] ?? $o['payment_method']) ?></span></div>
        <?php if ($o['submitted_at']): ?><div class="row"><span class="grow">Bukti dikirim</span><span class="value"><?= tanggal_id($o['submitted_at'], true) ?></span></div><?php endif ?>
        <?php if ($o['verified_at']): ?><div class="row"><span class="grow">Diverifikasi</span><span class="value"><?= tanggal_id($o['verified_at'], true) ?><?= $verifier ? ' · ' . esc($verifier) : '' ?></span></div><?php endif ?>
        <?php if ($o['payment_note']): ?><div class="row"><span class="grow">Catatan</span><span class="value"><?= esc($o['payment_note']) ?></span></div><?php endif ?>
        <?php if ($o['proof_file']): ?>
          <div class="row"><a href="<?= site_url('admin/payments/' . $o['payment_id'] . '/proof') ?>" target="_blank" rel="noopener"><img class="proof-thumb" style="max-width:160px" src="<?= site_url('admin/payments/' . $o['payment_id'] . '/proof') ?>" alt="Bukti pembayaran"></a>
            <?php if ($o['payment_status'] === 'submitted'): ?><span class="grow" style="text-align:right"><a class="btn small" href="<?= site_url('admin/payments') ?>">Verifikasi</a></span><?php endif ?></div>
        <?php endif ?>
      </div>
    </section>

    <section class="section">
      <div class="section-head"><h2>Pengiriman</h2><?= status_badge('shipment', $o['shipment_status']) ?></div>
      <div class="group">
        <div class="row"><span class="grow">Penerima</span><span class="value"><?= esc($o['recipient_name']) ?> · <?= esc($o['phone']) ?></span></div>
        <div class="row" style="align-items:flex-start"><span style="width:90px;flex:none">Alamat</span><span class="value" style="flex:1"><?= nl2br(esc($o['shipping_address'])) ?></span></div>
        <div class="row"><span class="grow">Nomor resi</span><span class="value"><?= esc($o['tracking_number'] ?: 'Belum ada') ?></span></div>
        <?php if ($o['notes']): ?><div class="row"><span class="grow">Catatan</span><span class="value"><?= esc($o['notes']) ?></span></div><?php endif ?>
      </div>
      <?php if ($o['order_status'] === 'processing'): ?><p class="section-foot"><a href="<?= site_url('admin/shipments') ?>">Input nomor resi di menu Pengiriman ›</a></p><?php endif ?>
    </section>

    <?php if ($canCancel || $o['order_status'] === 'shipped'): ?>
    <section class="section">
      <div class="section-head"><h2>Tindakan</h2></div>
      <div class="group card-pad" style="display:grid;gap:10px">
        <?php if ($o['order_status'] === 'shipped'): ?>
          <form action="<?= site_url('admin/orders/' . $o['id'] . '/complete') ?>" method="post" data-confirm="Tandai pesanan selesai?" data-confirm-text="Gunakan bila customer sudah menerima barang tetapi belum menekan tombol Pesanan Diterima." data-confirm-ok="Tandai Selesai">
            <?= csrf_field() ?><button class="btn block" type="submit">Tandai Selesai</button>
          </form>
        <?php endif ?>
        <?php if ($canCancel): ?>
          <form class="inline-form" action="<?= site_url('admin/orders/' . $o['id'] . '/cancel') ?>" method="post" data-confirm="Batalkan pesanan <?= esc($o['order_number'], 'attr') ?>?" data-confirm-text="Stok semua produk di pesanan ini akan dikembalikan." data-confirm-ok="Batalkan">
            <?= csrf_field() ?>
            <input class="input-sm" style="flex:1" name="reason" maxlength="255" placeholder="Alasan pembatalan (opsional)">
            <button class="btn small gray danger" type="submit">Batalkan</button>
          </form>
        <?php endif ?>
      </div>
    </section>
    <?php endif ?>
  </div>
</div>
<?= $this->endSection() ?>
