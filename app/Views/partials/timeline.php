<?php
/** Timeline status pesanan. @var array $order */
$st = $order['order_status'];
$steps = [
    ['pending_payment', 'Dipesan', tanggal_id($order['created_at'], true)],
    ['waiting_verification', 'Dibayar', $order['submitted_at'] ? tanggal_id($order['submitted_at'], true) : ''],
    ['processing', 'Diproses', $order['verified_at'] && $order['payment_status'] === 'verified' ? tanggal_id($order['verified_at'], true) : ''],
    ['shipped', 'Dikirim', $order['shipped_at'] ? tanggal_id($order['shipped_at'], true) : ''],
    ['completed', 'Selesai', $order['delivered_at'] ? tanggal_id($order['delivered_at'], true) : ''],
];
$keys = array_column($steps, 0);
$pos = array_search($st, $keys, true);
$cancelled = $st === 'cancelled';
if ($cancelled) {
    $pos = $order['submitted_at'] ? 1 : 0;
}
?>
<div class="timeline <?= $cancelled ? 'cancelled' : '' ?>">
  <?php foreach ($steps as $i => [$key, $label, $when]):
      $cls = $i < $pos || ($i === $pos && ($st === 'completed' || $cancelled)) ? 'done' : ($i === $pos ? 'current' : ''); ?>
    <div class="step <?= $cls ?>">
      <div class="dotx"><?= $cls === 'done' ? icon('check') : '<span>' . ($i + 1) . '</span>' ?></div>
      <b><?= $label ?></b><?= $when && $cls ? '<span>' . esc($when) . '</span>' : '' ?>
    </div>
  <?php endforeach ?>
</div>
<?php if ($cancelled): ?>
  <div class="alert error" style="margin-top:10px"><?= icon('x') ?><div class="grow"><b>Pesanan dibatalkan.</b> <?= esc($order['notes'] ?? '') ?> Stok produk telah dikembalikan.</div></div>
<?php endif ?>
