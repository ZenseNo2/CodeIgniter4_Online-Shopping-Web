<?= $this->extend('layouts/panel') ?>

<?= $this->section('subtitle') ?>
<p class="subtitle"><?= $waiting ?> bukti pembayaran menunggu pengecekan.</p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $tabs = ['submitted' => 'Menunggu', 'verified' => 'Diterima', 'rejected' => 'Ditolak', 'unpaid' => 'Belum bayar', 'all' => 'Semua']; ?>
<div class="segmented" style="margin-bottom:14px;max-width:560px">
  <?php foreach ($tabs as $k => $label): ?>
    <a class="<?= $status === $k ? 'on' : '' ?>" href="<?= query_with(['status' => $k]) ?>"><?= $label ?><?= $k === 'submitted' && $waiting ? " ({$waiting})" : '' ?></a>
  <?php endforeach ?>
</div>

<?php if (! $payments): ?>
  <div class="empty-state"><?= icon('check') ?><b>Tidak ada data</b><?= $status === 'submitted' ? 'Semua bukti pembayaran sudah diperiksa.' : 'Belum ada pembayaran dengan status ini.' ?></div>
<?php else: ?>
<div class="pay-grid">
  <?php foreach ($payments as $p): ?>
    <div class="pay-card">
      <?php if ($p['proof_file']): ?>
        <a class="proof" href="<?= site_url('admin/payments/' . $p['id'] . '/proof') ?>" target="_blank" rel="noopener" title="Buka bukti ukuran penuh">
          <img src="<?= site_url('admin/payments/' . $p['id'] . '/proof') ?>" alt="Bukti transfer <?= esc($p['order_number'], 'attr') ?>" loading="lazy"></a>
      <?php else: ?>
        <div class="proof none">Belum ada bukti</div>
      <?php endif ?>
      <div class="info">
        <div class="top"><a href="<?= site_url('admin/orders/' . $p['order_id']) ?>"><b><?= esc($p['order_number']) ?></b></a><?= status_badge('payment', $p['payment_status']) ?></div>
        <div class="muted"><?= esc($p['customer_name']) ?> · <?= esc(config('Shop')->paymentMethods[$p['method']]['name'] ?? $p['method']) ?></div>
        <div class="amount"><?= rupiah($p['amount']) ?></div>
        <div class="muted" style="font-size:13px">
          <?= $p['submitted_at'] ? 'Dikirim ' . tanggal_id($p['submitted_at'], true) : 'Pesanan ' . tanggal_id($p['order_date'], true) ?>
          <?= $p['verified_at'] ? '<br>Diperiksa ' . tanggal_id($p['verified_at'], true) . ' oleh ' . esc($p['verifier_name'] ?? '–') : '' ?>
          <?= $p['note'] ? '<br>Catatan: ' . esc($p['note']) : '' ?>
        </div>
        <?php if ($p['payment_status'] === 'submitted'): ?>
          <div class="actions">
            <form action="<?= site_url('admin/payments/' . $p['id'] . '/approve') ?>" method="post" data-confirm="Terima pembayaran <?= esc($p['order_number'], 'attr') ?>?" data-confirm-text="Pastikan nominal <?= rupiah($p['amount']) ?> sudah sesuai. Pesanan akan masuk tahap Diproses." data-confirm-ok="Terima">
              <?= csrf_field() ?><button class="btn small" type="submit"><?= icon('check') ?>Terima</button>
            </form>
            <form class="reject" action="<?= site_url('admin/payments/' . $p['id'] . '/reject') ?>" method="post">
              <?= csrf_field() ?>
              <input class="input-sm" name="note" placeholder="Alasan penolakan" required minlength="5" maxlength="255">
              <button class="btn small gray danger" type="submit">Tolak</button>
            </form>
          </div>
        <?php endif ?>
      </div>
    </div>
  <?php endforeach ?>
</div>
<?= $pager->links('default', 'ios') ?>
<?php endif ?>
<?= $this->endSection() ?>
