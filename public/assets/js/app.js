/* ShopEase — interaksi kecil di sisi browser (tanpa library).
   Semua aturan penting (stok, harga, total) tetap dihitung ulang di server. */
(function () {
  'use strict';

  // 1. Navbar: munculkan judul kecil & garis bawah saat halaman di-scroll (pola iOS large title)
  var navbar = document.querySelector('.navbar');
  if (navbar) {
    var onScroll = function () { navbar.classList.toggle('scrolled', window.scrollY > 40); };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // 2. Sidebar sebagai drawer di layar kecil
  document.addEventListener('click', function (e) {
    if (e.target.closest('[data-open-nav]')) {
      document.body.classList.add('nav-open');
    } else if (document.body.classList.contains('nav-open') && !e.target.closest('.sidebar')) {
      document.body.classList.remove('nav-open');
    }
  });

  // 3. Tutup pesan alert
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.alert .close');
    if (btn) btn.closest('.alert').remove();
  });

  // 4. Konfirmasi ala UIAlertController untuk form berisiko (hapus, reset, dll.)
  //    <form data-confirm="Judul" data-confirm-text="Penjelasan" data-confirm-ok="Hapus">
  var dialog = document.getElementById('confirm-dialog');
  var pendingForm = null;
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form.dataset.confirm || form.dataset.confirmed === '1' || !dialog || !dialog.showModal) return;
    e.preventDefault();
    pendingForm = form;
    dialog.querySelector('h3').textContent = form.dataset.confirm;
    dialog.querySelector('p').textContent = form.dataset.confirmText || '';
    dialog.querySelector('[data-ok]').textContent = form.dataset.confirmOk || 'Hapus';
    dialog.showModal();
  });
  if (dialog) {
    dialog.querySelector('[data-cancel]').addEventListener('click', function () { dialog.close(); pendingForm = null; });
    dialog.querySelector('[data-ok]').addEventListener('click', function () {
      dialog.close();
      if (pendingForm) { pendingForm.dataset.confirmed = '1'; pendingForm.requestSubmit ? pendingForm.requestSubmit() : pendingForm.submit(); }
    });
  }

  // 5. Filter otomatis: <select data-autosubmit> langsung mengirim form-nya
  document.querySelectorAll('select[data-autosubmit]').forEach(function (s) {
    s.addEventListener('change', function () { s.form.submit(); });
  });

  // 6. Stepper jumlah: <div class="qty"><button data-step="-1">…<input>…<button data-step="1">
  document.addEventListener('click', function (e) {
    var b = e.target.closest('.qty [data-step]');
    if (!b) return;
    e.preventDefault();
    var input = b.closest('.qty').querySelector('input');
    var min = parseInt(input.min || '0', 10), max = parseInt(input.max || '9999', 10);
    var v = Math.min(max, Math.max(min, (parseInt(input.value, 10) || 0) + parseInt(b.dataset.step, 10)));
    if (String(v) === input.value) return;
    input.value = v;
    input.dispatchEvent(new Event('change', { bubbles: true }));
  });
  // Jumlah di keranjang langsung disimpan saat diubah
  document.addEventListener('change', function (e) {
    var input = e.target.closest('form[data-autosave] input[name=quantity]');
    if (input) input.form.requestSubmit ? input.form.requestSubmit() : input.form.submit();
  });

  // 7. Checkout: tampilkan form alamat baru & hitung ulang ringkasan saat kurir dipilih
  var checkout = document.querySelector('[data-checkout]');
  if (checkout) {
    var newAddr = checkout.querySelector('.new-address');
    var syncAddr = function () {
      var picked = checkout.querySelector('input[name=address_id]:checked');
      var isNew = !picked || picked.value === 'new';
      if (newAddr) {
        newAddr.hidden = !isNew;
        newAddr.querySelectorAll('input[data-req]').forEach(function (i) { i.required = isNew; });
      }
    };
    var rp = function (n) { return 'Rp' + Math.round(n).toLocaleString('id-ID'); };
    var syncTotal = function () {
      var c = checkout.querySelector('input[name=courier]:checked');
      var ship = c ? parseFloat(c.dataset.cost) : 0;
      var sub = parseFloat(checkout.dataset.subtotal);
      var sEl = document.querySelector('[data-ship]'), tEl = document.querySelector('[data-total]');
      if (sEl) sEl.textContent = c ? rp(ship) : '–';
      if (tEl) tEl.textContent = rp(sub + ship);
    };
    checkout.addEventListener('change', function () { syncAddr(); syncTotal(); });
    syncAddr(); syncTotal();
  }

  // 8. Pratinjau gambar sebelum upload: <input type=file data-preview="#img">
  document.addEventListener('change', function (e) {
    var input = e.target.closest('input[type=file][data-preview]');
    if (!input || !input.files || !input.files[0]) return;
    var img = document.querySelector(input.dataset.preview);
    if (!img) return;
    if (input.files[0].size > 2 * 1024 * 1024) { alertBox('Ukuran gambar maksimal 2 MB.'); input.value = ''; return; }
    var r = new FileReader();
    r.onload = function () { img.src = r.result; };
    r.readAsDataURL(input.files[0]);
  });
  function alertBox(msg) {
    var d = document.getElementById('confirm-dialog');
    if (!d || !d.showModal) return;
    d.querySelector('h3').textContent = 'Perhatian';
    d.querySelector('p').textContent = msg;
    d.querySelector('[data-cancel]').hidden = true;
    var ok = d.querySelector('[data-ok]');
    ok.textContent = 'OK';
    ok.onclick = function () { d.close(); d.querySelector('[data-cancel]').hidden = false; ok.onclick = null; };
    d.showModal();
  }

  // 9. Menu akun: tutup saat klik di luar
  document.addEventListener('click', function (e) {
    document.querySelectorAll('details.menu[open]').forEach(function (m) {
      if (!m.contains(e.target)) m.removeAttribute('open');
    });
  });
})();
