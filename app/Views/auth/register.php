<?= view('auth/_head', ['pageTitle' => 'Daftar']) ?>
<main class="auth-page">
  <div class="auth-card" style="max-width:440px">
    <a href="<?= site_url('/') ?>" class="auth-mark" aria-label="Kembali ke toko"><?= icon('bag') ?></a>
    <h1>Buat Akun</h1>
    <p class="subtitle">Akun baru otomatis terdaftar sebagai customer.</p>

    <?= view('partials/flash') ?>

    <form action="<?= site_url('register') ?>" method="post" novalidate>
      <?= csrf_field() ?>
      <div class="section-head"><h2>Data Diri</h2></div>
      <div class="group form-group">
        <?= field_row('name', 'Nama', '<input id="name" name="name" value="' . esc(old('name')) . '" placeholder="Nama lengkap" autocomplete="name" required maxlength="100" autofocus>') ?>
        <?= field_row('email', 'Email', '<input id="email" type="email" name="email" value="' . esc(old('email')) . '" placeholder="nama@email.com" autocomplete="email" autocapitalize="off" required>') ?>
        <?= field_row('phone', 'Telepon', '<input id="phone" type="tel" name="phone" value="' . esc(old('phone')) . '" placeholder="Opsional" autocomplete="tel">') ?>
        <?= field_row('address', 'Alamat', '<input id="address" name="address" value="' . esc(old('address')) . '" placeholder="Opsional" autocomplete="street-address">') ?>
      </div>

      <div class="section-head" style="margin-top:22px"><h2>Keamanan</h2></div>
      <div class="group form-group">
        <?= field_row('password', 'Password', '<input id="password" type="password" name="password" placeholder="Minimal 8 karakter" autocomplete="new-password" required minlength="8">') ?>
        <?= field_row('password_confirm', 'Ulangi', '<input id="password_confirm" type="password" name="password_confirm" placeholder="Ketik ulang password" autocomplete="new-password" required>') ?>
      </div>
      <p class="section-foot">Password disimpan dalam bentuk hash (<code>password_hash</code>), tidak pernah sebagai teks asli.</p>

      <button class="btn lg block" type="submit" style="margin-top:18px">Daftar</button>
    </form>
    <p class="section-foot" style="text-align:center;margin-top:14px">Sudah punya akun? <a href="<?= site_url('login') ?>">Masuk</a></p>
  </div>
</main>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
