<?= $this->extend('layouts/store') ?>
<?= $this->section('content') ?>
<?php
$filters = ['' => 'Semua', 'pending_payment' => 'Belum Bayar', 'waiting_verification' => 'Verifikasi', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
?>
<h1 class="page-title">Pesanan Saya</h1>
<p class="page-sub">Riwayat dan status semua pesanan Anda.</p>

<div class="chips" style="margin:16px 0 14px">
  <?php foreach ($filters as $k => $label): ?>
    <a class="chip <?= $status === $k ? 'on' : '' ?>" href="<?= query_with(['status' => $k ?: null]) ?>"><?= $label ?></a>
  <?php endforeach ?>
</div>

<?php if (! $orders): ?>
  <div class="empty-state"><?= icon('receipt') ?><b>Belum ada pesanan<?= $status ? ' dengan status ini' : '' ?></b>Pesanan yang Anda buat akan muncul di sini.<br>
    <a class="btn" href="<?= site_url('catalog') ?>">Mulai Belanja</a></div>
<?php else: ?>
  <?php foreach ($orders as $o): $its = $items[$o['id']] ?? []; ?>
    <a class="order-card" href="<?= site_url('orders/' . $o['order_number']) ?>" style="display:block;color:inherit;text-decoration:none">
      <div class="head">
        <span class="no"><?= esc($o['order_number']) ?></span>
        <span class="date"><?= tanggal_id($o['created_at'], true) ?></span>
        <?= status_badge('order', $o['order_status']) ?>
      </div>
      <div class="body">
        <div class="thumbs"><?php foreach (array_slice($its, 0, 3) as $it): ?><img src="<?= product_image($it['image']) ?>" alt=""><?php endforeach ?></div>
        <div class="info">
          <b><?= esc($its[0]['product_name'] ?? '') ?></b><?= count($its) > 1 ? ' <span class="sub">+' . (count($its) - 1) . ' produk lain</span>' : '' ?>
          <div class="sub"><?= (int) $o['item_count'] ?> barang · <?= esc($o['courier']) ?><?= $o['tracking_number'] ? ' · Resi ' . esc($o['tracking_number']) : '' ?></div>
          <?php if ($o['order_status'] === 'pending_payment'): ?>
            <div class="sub" style="color:var(--orange)"><?= $o['payment_status'] === 'rejected' ? 'Bukti ditolak, unggah ulang bukti pembayaran' : 'Segera lakukan pembayaran dan unggah bukti' ?></div>
          <?php endif ?>
        </div>
        <div class="total"><span>Total</span><b><?= rupiah($o['total']) ?></b></div>
      </div>
    </a>
  <?php endforeach ?>
  <?= $pager->links('default', 'ios') ?>
<?php endif ?>
<?= $this->endSection() ?>
