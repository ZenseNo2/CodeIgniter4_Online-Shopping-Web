<?php
/**
 * Grafik batang SVG tanpa library.
 * $points: list<[label pendek, nilai, tooltip]>   $height: tinggi area (px)
 */
$height = $height ?? 180;
$n      = max(1, count($points));
$max    = max(1, max(array_map(static fn ($p) => $p[1], $points ?: [[0, 0, '']])));
// Bulatkan batas atas ke angka "rapi" agar garis bantu enak dibaca.
$mag   = 10 ** max(0, floor(log10($max)));
$top   = ceil($max / $mag) * $mag;
$w     = 640;
$left  = 46;
$slot  = ($w - $left) / $n;
$bw    = max(4, $slot * .62);
$every = (int) ceil($n / 10);
$fmt   = $fmt ?? static fn ($v) => $v >= 1e6 ? round($v / 1e6, 1) . ' jt' : ($v >= 1e3 ? round($v / 1e3) . ' rb' : (string) $v);
?>
<svg viewBox="0 0 <?= $w ?> <?= $height + 26 ?>" role="img" aria-label="<?= esc($label ?? 'Grafik', 'attr') ?>">
  <g class="grid">
    <?php for ($i = 0; $i <= 4; $i++): $y = 6 + ($height - 6) * (1 - $i / 4); ?>
      <line x1="<?= $left ?>" x2="<?= $w ?>" y1="<?= round($y, 1) ?>" y2="<?= round($y, 1) ?>"/>
      <text x="<?= $left - 8 ?>" y="<?= round($y + 4, 1) ?>" text-anchor="end"><?= esc($fmt($top * $i / 4)) ?></text>
    <?php endfor ?>
  </g>
  <?php foreach ($points as $i => [$lbl, $val, $tip]):
      $h = ($height - 6) * ($val / $top);
      $x = $left + $slot * $i + ($slot - $bw) / 2; ?>
    <rect class="bar" x="<?= round($x, 1) ?>" y="<?= round($height - $h, 1) ?>" width="<?= round($bw, 1) ?>" height="<?= round(max($h, $val > 0 ? 2 : 0), 1) ?>" rx="2"><title><?= esc($tip) ?></title></rect>
    <?php if ($i % $every === 0): ?>
      <text x="<?= round($x + $bw / 2, 1) ?>" y="<?= $height + 18 ?>" text-anchor="middle"><?= esc($lbl) ?></text>
    <?php endif ?>
  <?php endforeach ?>
</svg>
