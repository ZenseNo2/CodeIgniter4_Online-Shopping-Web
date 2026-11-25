<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>
<?php $tabs = ['ready' => 'Siap dikirim', 'shipped' => 'Dalam perjalanan', 'delivered' => 'Diterima', 'all' => 'Semua']; ?>
<div class="segmented" style="margin-bottom:14px;max-width:520px">
  <?php foreach ($tabs as $k => $label): ?>
    <a class="<?= $status === $k ? 'on' : '' ?>" href="<?= query_with(['status' => $k]) ?>"><?= $label ?></a>
  <?php endforeach ?>
</div>

<section class="section" style="margin-top:0">
  <div class="section-head"><h2><?= $tabs[$status] ?? 'Pengiriman' ?></h2><span class="meta"><?= $pager->getTotal() ?> pesanan</span></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>Pesanan</th><th>Penerima</th><th>Kurir &amp; Resi</th><th>Status</th></tr></thead>
      <tbody>
      <?php foreach ($shipments as $s): ?>
        <tr>
          <td class="nowrap"><a href="<?= site_url('admin/orders/' . $s['order_id']) ?>"><?= esc($s['order_number']) ?></a><span class="sub"><?= tanggal_id($s['order_date']) ?> · <?= rupiah($s['total']) ?></span></td>
          <td><?= esc($s['recipient_name']) ?><span class="sub"><?= esc(strtok($s['shipping_address'], "\n")) ?></span></td>
          <td>
            <?php if ($s['order_status'] === 'processing'): ?>
              <form class="inline-form" action="<?= site_url('admin/shipments/' . $s['order_id'] . '/ship') ?>" method="post">
                <?= csrf_field() ?>
                <select class="select-sm" name="courier" aria-label="Kurir"><?= select_options($couriers, $s['courier']) ?></select>
                <input class="input-sm" name="tracking_number" placeholder="Nomor resi" required minlength="6" maxlength="100" style="width:150px" aria-label="Nomor resi">
                <button class="btn small" type="submit"><?= icon('truck') ?>Kirim</button>
              </form>
            <?php else: ?>
              <?= esc($s['courier']) ?><span class="sub"><?= esc($s['tracking_number'] ?: '–') ?><?= $s['shipped_at'] ? ' · dikirim ' . tanggal_id($s['shipped_at']) : '' ?></span>
            <?php endif ?>
          </td>
          <td><?= $s['order_status'] === 'processing' ? '<span class="badge blue">Siap kirim</span>' : status_badge('shipment', $s['shipment_status']) ?></td>
        </tr>
      <?php endforeach ?>
      <?php if (! $shipments): ?><tr><td colspan="4" class="c muted" style="padding:22px">Tidak ada pesanan pada tab ini.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
  <?= $pager->links('default', 'ios') ?>
  <p class="section-foot">Hanya pesanan yang pembayarannya sudah diverifikasi (status Diproses) yang dapat dikirim.</p>
</section>
<?= $this->endSection() ?>
