<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>
<?php
$labels = config('Shop')->orderStatusLabels;
$all = array_sum($counts);
?>
<div class="chips" style="margin-bottom:12px">
  <a class="chip <?= $status === '' ? 'on' : '' ?>" href="<?= query_with(['status' => null]) ?>">Semua<span class="n"><?= $all ?></span></a>
  <?php foreach ($labels as $k => [$label]): ?>
    <a class="chip <?= $status === $k ? 'on' : '' ?>" href="<?= query_with(['status' => $k]) ?>"><?= esc($label) ?><span class="n"><?= (int) ($counts[$k] ?? 0) ?></span></a>
  <?php endforeach ?>
</div>

<form class="toolbar" method="get" action="<?= site_url('admin/orders') ?>">
  <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?= esc($status, 'attr') ?>"><?php endif ?>
  <label class="search"><?= icon('search') ?><span class="sr-only">Cari</span>
    <input type="search" name="q" value="<?= esc($q) ?>" placeholder="Cari nomor pesanan atau nama customer">
  </label>
</form>

<section class="section">
  <div class="section-head"><h2>Daftar Pesanan</h2><span class="meta"><?= $pager->getTotal() ?> pesanan</span></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>Pesanan</th><th>Customer</th><th class="hide-sm">Kurir</th><th class="text-r">Total</th><th class="hide-sm">Pembayaran</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td class="nowrap"><a href="<?= site_url('admin/orders/' . $o['id']) ?>"><?= esc($o['order_number']) ?></a><span class="sub"><?= tanggal_id($o['created_at'], true) ?></span></td>
          <td><?= esc($o['customer_name']) ?><span class="sub"><?= (int) $o['item_count'] ?> barang</span></td>
          <td class="hide-sm"><?= esc($o['courier']) ?><?= $o['tracking_number'] ? '<span class="sub">' . esc($o['tracking_number']) . '</span>' : '' ?></td>
          <td class="text-r nowrap"><?= rupiah($o['total']) ?></td>
          <td class="hide-sm"><?= status_badge('payment', $o['payment_status']) ?></td>
          <td><?= status_badge('order', $o['order_status']) ?></td>
          <td class="actions"><a class="link-btn small" href="<?= site_url('admin/orders/' . $o['id']) ?>">Detail</a></td>
        </tr>
      <?php endforeach ?>
      <?php if (! $orders): ?><tr><td colspan="7" class="c muted" style="padding:22px">Tidak ada pesanan.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
  <?= $pager->links('default', 'ios') ?>
</section>
<?= $this->endSection() ?>
