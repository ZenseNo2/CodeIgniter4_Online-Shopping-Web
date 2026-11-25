<?= $this->extend('layouts/store') ?>
<?= $this->section('content') ?>
<h1 class="page-title">Kontak</h1>
<p class="page-sub">Butuh bantuan terkait pesanan? Hubungi layanan pelanggan kami.</p>
<div class="two-col" style="margin-top:18px">
  <section class="section" style="margin-top:0">
    <div class="group">
      <div class="row"><span class="tile t-blue"><?= icon('mail') ?></span><span class="grow"><span class="title">Email</span><span class="sub">cs@shopease.test</span></span></div>
      <div class="row"><span class="tile t-green"><?= icon('phone') ?></span><span class="grow"><span class="title">Telepon / WhatsApp</span><span class="sub">0800-0000-0000 (contoh)</span></span></div>
      <div class="row"><span class="tile t-orange"><?= icon('clock') ?></span><span class="grow"><span class="title">Jam layanan</span><span class="sub">Senin–Jumat, 09.00–17.00 WIB</span></span></div>
      <div class="row"><span class="tile t-red"><?= icon('pin') ?></span><span class="grow"><span class="title">Alamat</span><span class="sub">Jl. Contoh Praktikum No. 1, Jakarta</span></span></div>
    </div>
    <p class="section-foot">Sertakan nomor pesanan (mis. SE-20261125-0001) agar kami dapat membantu lebih cepat.</p>
  </section>
  <section class="section" style="margin-top:0">
    <div class="section-head"><h2>Pertanyaan umum</h2></div>
    <div class="prose">
      <p><b>Kapan pesanan diproses?</b><br>Setelah bukti transfer diverifikasi admin, biasanya kurang dari 1×24 jam.</p>
      <p><b>Bisakah membatalkan pesanan?</b><br>Bisa, selama pesanan masih berstatus Menunggu Pembayaran.</p>
      <p><b>Bukti transfer ditolak?</b><br>Unggah ulang bukti yang jelas dari halaman detail pesanan.</p>
    </div>
  </section>
</div>
<?= $this->endSection() ?>
