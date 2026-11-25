<?php /** Distribusi status pesanan. @var list<array> $status */
$maxS = max(1, max(array_column($status, 'count'))); ?>
<div class="group" style="padding:6px 0">
  <?php foreach ($status as $s): ?>
    <a class="hbar" href="<?= site_url('admin/orders?status=' . $s['status']) ?>" style="color:inherit;text-decoration:none">
      <span><?= esc($s['label']) ?></span>
      <span class="meter c-<?= esc($s['color'], 'attr') ?>"><span style="width:<?= round($s['count'] / $maxS * 100) ?>%"></span></span>
      <span class="n"><?= $s['count'] ?></span>
    </a>
  <?php endforeach ?>
</div>
