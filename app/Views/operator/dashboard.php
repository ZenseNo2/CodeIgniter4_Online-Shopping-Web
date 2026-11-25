<?= $this->extend('layouts/panel') ?>

<?= $this->section('subtitle') ?>
<p class="subtitle"><?= hari_ini_id() ?> · Tugas operator: verifikasi pembayaran dan kirim pesanan.</p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="stats cols-4">
  <div class="stat"><div class="k"><span class="tile t-purple"><?= icon('card') ?></span>Perlu verifikasi</div><a class="v" href="<?= site_url('admin/payments') ?>"><?= count($payments) ?></a><div class="d">bukti transfer</div></div>
  <div class="stat"><div class="k"><span class="tile t-teal"><?= icon('truck') ?></span>Siap dikirim</div><a class="v" href="<?= site_url('admin/shipments') ?>"><?= count($toShip) ?></a><div class="d">pesanan diproses</div></div>
  <div class="stat"><div class="k"><span class="tile t-blue"><?= icon('clock') ?></span>Dalam perjalanan</div><a class="v" href="<?= site_url('admin/shipments?status=shipped') ?>"><?= count($shipping) ?></a><div class="d">menunggu diterima</div></div>
  <div class="stat"><div class="k"><span class="tile t-orange"><?= icon('box') ?></span>Stok menipis</div><a class="v" href="<?= site_url('admin/products?status=low') ?>"><?= $stats['stok_menipis'] ?></a><div class="d">produk ≤ 5 unit</div></div>
</div>

<div class="grid-2">
  <section class="section">
    <div class="section-head"><h2>Pembayaran Menunggu</h2><a href="<?= site_url('admin/payments') ?>">Buka verifikasi</a></div>
    <div class="group">
      <?php foreach ($payments as $p): ?>
        <a class="row" href="<?= site_url('admin/payments') ?>"><span class="tile t-purple"><?= icon('card') ?></span>
          <span class="grow"><span class="title"><?= esc($p['order_number']) ?> · <?= esc($p['customer_name']) ?></span><span class="sub">Dikirim <?= tanggal_id($p['submitted_at'], true) ?></span></span>
          <span class="value"><?= rupiah($p['amount']) ?></span><?= icon('chevron-right', 'chev') ?></a>
      <?php endforeach ?>
      <?php if (! $payments): ?><div class="row empty">Tidak ada pembayaran yang menunggu.</div><?php endif ?>
    </div>
  </section>
  <section class="section">
    <div class="section-head"><h2>Siap Dikirim</h2><a href="<?= site_url('admin/shipments') ?>">Input resi</a></div>
    <div class="group">
      <?php foreach ($toShip as $o): ?>
        <a class="row" href="<?= site_url('admin/orders/' . $o['id']) ?>"><span class="tile t-teal"><?= icon('box') ?></span>
          <span class="grow"><span class="title"><?= esc($o['order_number']) ?></span><span class="sub"><?= esc($o['recipient_name']) ?> · <?= esc($o['courier']) ?> · <?= (int) $o['item_count'] ?> barang</span></span><?= icon('chevron-right', 'chev') ?></a>
      <?php endforeach ?>
      <?php if (! $toShip): ?><div class="row empty">Tidak ada pesanan yang perlu dikirim.</div><?php endif ?>
    </div>
  </section>
</div>

<section class="section">
  <div class="section-head"><h2>Dalam Pengiriman</h2></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>Pesanan</th><th>Penerima</th><th>Kurir</th><th>Resi</th><th class="hide-sm">Dikirim</th></tr></thead>
      <tbody>
      <?php foreach ($shipping as $o): ?>
        <tr><td><a href="<?= site_url('admin/orders/' . $o['id']) ?>"><?= esc($o['order_number']) ?></a></td><td><?= esc($o['recipient_name']) ?></td>
          <td><?= esc($o['courier']) ?></td><td><code><?= esc($o['tracking_number']) ?></code></td><td class="hide-sm"><?= tanggal_id($o['shipped_at']) ?></td></tr>
      <?php endforeach ?>
      <?php if (! $shipping): ?><tr><td colspan="5" class="c muted" style="padding:22px">Tidak ada pesanan dalam perjalanan.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
</section>
<?= $this->endSection() ?>
