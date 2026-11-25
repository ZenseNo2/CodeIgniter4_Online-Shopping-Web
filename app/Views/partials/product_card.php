<?php /** @var array $p produk (hasil ProductModel::catalog) */ ?>
<a class="product-card" href="<?= site_url('product/' . $p['id']) ?>">
  <div class="thumb">
    <img src="<?= product_image($p['image']) ?>" alt="<?= esc($p['name'], 'attr') ?>" loading="lazy">
    <?php if ((int) $p['stock'] < 1): ?><span class="badge red">Stok habis</span>
    <?php elseif ((int) $p['stock'] <= 5): ?><span class="badge orange">Sisa <?= (int) $p['stock'] ?></span><?php endif ?>
  </div>
  <div class="body">
    <span class="cat-name"><?= esc($p['category_name'] ?? '') ?></span>
    <span class="name"><?= esc($p['name']) ?></span>
    <span class="price"><?= rupiah($p['price']) ?></span>
  </div>
</a>
