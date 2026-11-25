<?= $this->extend('layouts/store') ?>
<?= $this->section('content') ?>
<?php
// Tiga form di halaman ini memakai nama field yang sama (phone, address).
// Field tersembunyi "_form" menandai form mana yang baru dikirim, agar error tampil di form yang benar.
$sent = old('_form');
$row = static function (string $form, string $name, string $label, string $input) use ($sent): string {
    $errs = $sent === $form ? validation_errors_array() : [];
    $err  = $errs[$name] ?? null;

    return '<div class="row stack ' . ($err ? 'has-error' : '') . '"><div class="field"><label for="' . $form . '-' . $name . '">' . esc($label) . '</label>'
        . $input . '</div>' . ($err ? '<p class="field-error">' . esc($err) . '</p>' : '') . '</div>';
};
$val = static fn (string $form, string $name, $default = '') => esc($sent === $form ? (string) old($name, $default) : (string) $default);
?>
<h1 class="page-title">Akun Saya</h1>

<div class="profile-head" style="margin-top:14px">
  <span class="avatar lg"><?= esc(inisial($user['name'])) ?></span>
  <div class="grow" style="flex:1;min-width:0"><b><?= esc($user['name']) ?></b><span><?= esc($user['email']) ?> · Customer sejak <?= tanggal_id($user['created_at']) ?></span></div>
</div>

<div class="stats cols-3" style="margin-top:12px">
  <div class="stat"><div class="k"><?= icon('receipt') ?>Total pesanan</div><div class="v"><?= (int) $stats['total_orders'] ?></div><div class="d">sejak bergabung</div></div>
  <div class="stat"><div class="k"><?= icon('truck') ?>Sedang berjalan</div><div class="v"><?= (int) $stats['active_orders'] ?></div><div class="d">belum selesai</div></div>
  <div class="stat"><div class="k"><?= icon('card') ?>Total belanja</div><div class="v" style="font-size:22px"><?= rupiah($stats['total_spent'] ?? 0) ?></div><div class="d">pesanan terbayar</div></div>
</div>

<div class="two-col even">
  <div>
    <section class="section">
      <div class="section-head"><h2>Pesanan Terakhir</h2><a href="<?= site_url('orders') ?>">Lihat semua</a></div>
      <div class="group">
        <?php foreach ($recent as $o): ?>
          <a class="row" href="<?= site_url('orders/' . $o['order_number']) ?>">
            <span class="grow"><span class="title"><?= esc($o['order_number']) ?></span><span class="sub"><?= tanggal_id($o['created_at']) ?> · <?= rupiah($o['total']) ?></span></span>
            <?= status_badge('order', $o['order_status']) ?><?= icon('chevron-right', 'chev') ?>
          </a>
        <?php endforeach ?>
        <?php if (! $recent): ?><div class="row empty">Belum ada pesanan.</div><?php endif ?>
      </div>
    </section>

    <section class="section" id="alamat">
      <div class="section-head"><h2>Alamat Pengiriman</h2></div>
      <div class="group">
        <?php foreach ($addresses as $a): ?>
          <div class="row" style="align-items:flex-start">
            <span class="tile <?= $a['is_default'] ? 't-blue' : 't-gray' ?>"><?= icon('pin') ?></span>
            <span class="grow"><span class="title"><?= esc($a['label']) ?> <?= $a['is_default'] ? '<span class="badge blue">Utama</span>' : '' ?></span>
              <span class="sub"><?= esc($a['recipient_name']) ?> · <?= esc($a['phone']) ?></span>
              <span class="sub"><?= esc($a['address']) ?>, <?= esc($a['city']) ?> <?= esc($a['postal_code']) ?></span></span>
            <span style="display:flex;gap:10px;flex:none">
              <?php if (! $a['is_default']): ?>
                <form action="<?= site_url('account/addresses/' . $a['id'] . '/default') ?>" method="post"><?= csrf_field() ?><button class="link-btn small" type="submit">Jadikan utama</button></form>
              <?php endif ?>
              <form action="<?= site_url('account/addresses/' . $a['id'] . '/delete') ?>" method="post" data-confirm="Hapus alamat <?= esc($a['label'], 'attr') ?>?" data-confirm-ok="Hapus"><?= csrf_field() ?><button class="link-btn small danger" type="submit">Hapus</button></form>
            </span>
          </div>
        <?php endforeach ?>
        <?php if (! $addresses): ?><div class="row empty">Belum ada alamat tersimpan.</div><?php endif ?>
      </div>

      <details class="add-address" style="margin-top:10px" <?= $sent === 'address' ? 'open' : '' ?>>
        <summary>+ Tambah alamat</summary>
        <form action="<?= site_url('account/addresses') ?>" method="post" style="margin-top:8px">
          <?= csrf_field() ?><input type="hidden" name="_form" value="address">
          <div class="group form-group">
            <?= $row('address', 'label', 'Label', '<input id="address-label" name="label" value="' . $val('address', 'label', 'Rumah') . '" maxlength="50" required>') ?>
            <?= $row('address', 'recipient_name', 'Penerima', '<input id="address-recipient_name" name="recipient_name" value="' . $val('address', 'recipient_name', $user['name']) . '" required>') ?>
            <?= $row('address', 'phone', 'Telepon', '<input id="address-phone" type="tel" name="phone" value="' . $val('address', 'phone', $user['phone'] ?? '') . '" required>') ?>
            <?= $row('address', 'address', 'Alamat', '<input id="address-address" name="address" value="' . $val('address', 'address') . '" placeholder="Jalan, nomor, RT/RW" required>') ?>
            <?= $row('address', 'city', 'Kota', '<input id="address-city" name="city" value="' . $val('address', 'city') . '" required>') ?>
            <?= $row('address', 'postal_code', 'Kode pos', '<input id="address-postal_code" name="postal_code" value="' . $val('address', 'postal_code') . '" inputmode="numeric" placeholder="Opsional">') ?>
          </div>
          <div class="form-actions"><button class="btn" type="submit">Simpan Alamat</button></div>
        </form>
      </details>
    </section>
  </div>

  <div>
    <section class="section">
      <div class="section-head"><h2>Profil</h2></div>
      <form action="<?= site_url('account/profile') ?>" method="post">
        <?= csrf_field() ?><input type="hidden" name="_form" value="profile">
        <div class="group form-group">
          <?= $row('profile', 'name', 'Nama', '<input id="profile-name" name="name" value="' . $val('profile', 'name', $user['name']) . '" required>') ?>
          <div class="row stack"><div class="field"><label>Email</label><input value="<?= esc($user['email'], 'attr') ?>" disabled></div></div>
          <?= $row('profile', 'phone', 'Telepon', '<input id="profile-phone" type="tel" name="phone" value="' . $val('profile', 'phone', $user['phone'] ?? '') . '" placeholder="Opsional">') ?>
          <?= $row('profile', 'address', 'Alamat', '<input id="profile-address" name="address" value="' . $val('profile', 'address', $user['address'] ?? '') . '" placeholder="Opsional">') ?>
        </div>
        <div class="form-actions"><button class="btn" type="submit">Simpan Profil</button></div>
      </form>
    </section>

    <section class="section">
      <div class="section-head"><h2>Ganti Password</h2></div>
      <form action="<?= site_url('account/password') ?>" method="post">
        <?= csrf_field() ?><input type="hidden" name="_form" value="password">
        <div class="group form-group">
          <?= $row('password', 'current_password', 'Password lama', '<input id="password-current_password" type="password" name="current_password" autocomplete="current-password" required>') ?>
          <?= $row('password', 'password', 'Password baru', '<input id="password-password" type="password" name="password" autocomplete="new-password" placeholder="Minimal 8 karakter" required>') ?>
          <?= $row('password', 'password_confirm', 'Ulangi', '<input id="password-password_confirm" type="password" name="password_confirm" autocomplete="new-password" required>') ?>
        </div>
        <div class="form-actions"><button class="btn" type="submit">Ganti Password</button></div>
      </form>
    </section>
  </div>
</div>
<?= $this->endSection() ?>
