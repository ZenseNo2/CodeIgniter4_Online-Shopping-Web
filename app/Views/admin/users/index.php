<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>
<form class="toolbar" method="get" action="<?= site_url('admin/users') ?>">
  <label class="search"><?= icon('search') ?><span class="sr-only">Cari</span>
    <input type="search" name="q" value="<?= esc($q) ?>" placeholder="Cari nama atau email">
  </label>
  <select class="select-pill" name="role" data-autosubmit aria-label="Role">
    <?= select_options(['customer' => 'Customer', 'operator' => 'Operator', 'admin' => 'Admin'], $role, 'Semua role') ?>
  </select>
</form>

<section class="section">
  <div class="section-head"><h2>Daftar Pengguna</h2><span class="meta"><?= $pager->getTotal() ?> akun</span></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>Nama</th><th class="hide-sm">Telepon</th><th class="text-r hide-sm">Pesanan</th><th class="hide-sm">Terdaftar</th><th>Role</th></tr></thead>
      <tbody>
      <?php foreach ($users as $u): $self = (int) $u['id_user'] === (int) session('user_id'); ?>
        <tr>
          <td><div class="cell-product"><span class="avatar sm"><?= esc(inisial($u['name'])) ?></span><span><?= esc($u['name']) ?><?= $self ? ' <span class="badge gray">Anda</span>' : '' ?><span class="sub"><?= esc($u['email']) ?></span></span></div></td>
          <td class="hide-sm"><?= esc($u['phone'] ?: '–') ?></td>
          <td class="text-r hide-sm"><?= (int) $u['order_count'] ?></td>
          <td class="hide-sm"><?= tanggal_id($u['created_at']) ?></td>
          <td>
            <?php if ($self): ?>
              <span class="badge blue"><?= role_label($u['role']) ?></span>
            <?php else: ?>
              <form action="<?= site_url('admin/users/' . $u['id_user'] . '/role') ?>" method="post" class="inline-form">
                <?= csrf_field() ?>
                <select class="select-sm" name="role" data-autosubmit aria-label="Role <?= esc($u['name'], 'attr') ?>">
                  <?= select_options(['customer' => 'Customer', 'operator' => 'Operator', 'admin' => 'Admin'], $u['role']) ?>
                </select>
              </form>
            <?php endif ?>
          </td>
        </tr>
      <?php endforeach ?>
      <?php if (! $users): ?><tr><td colspan="5" class="c muted" style="padding:22px">Tidak ada pengguna yang cocok.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
  <?= $pager->links('default', 'ios') ?>
  <p class="section-foot">Perubahan role langsung berlaku pada permintaan berikutnya, karena filter auth membaca role dari database.</p>
</section>
<?= $this->endSection() ?>
