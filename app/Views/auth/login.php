<?= view('auth/_head', ['pageTitle' => 'Masuk']) ?>
<main class="auth-page">
  <div class="auth-card">
    <a href="<?= site_url('/') ?>" class="auth-mark" aria-label="Kembali ke toko"><?= icon('bag') ?></a>
    <h1>Masuk ke ShopEase</h1>
    <p class="subtitle">Gunakan email dan password akun Anda.</p>

    <?= view('partials/flash') ?>

    <form action="<?= site_url('login') ?>" method="post" style="margin-top:16px" novalidate>
      <?= csrf_field() ?>
      <div class="group form-group">
        <div class="row stack <?= has_error('email') ?>">
          <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="<?= esc(old('email')) ?>" placeholder="nama@email.com" autocomplete="username" autocapitalize="off" autofocus required>
          </div>
          <?= field_error('email') ?>
        </div>
        <div class="row stack <?= has_error('password') ?>">
          <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Wajib" autocomplete="current-password" required>
          </div>
          <?= field_error('password') ?>
        </div>
      </div>
      <button class="btn lg block" type="submit" style="margin-top:18px">Masuk</button>
    </form>
    <p class="section-foot" style="text-align:center;margin-top:14px">Belum punya akun? <a href="<?= site_url('register') ?>">Daftar sebagai customer</a></p>

    <div class="demo">
      <div class="section-head"><h2>Akun Demo</h2></div>
      <div class="group">
        <div class="row"><span class="grow">Admin</span><code>admin@shopease.com</code></div>
        <div class="row"><span class="grow">Operator</span><code>operator@shopease.com</code></div>
        <div class="row"><span class="grow">Customer</span><code>budi@example.com</code></div>
      </div>
      <p class="section-foot">Semua akun demo memakai password <code>shopease123</code>.</p>
    </div>
  </div>
</main>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
