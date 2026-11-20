<?php
$page_title = 'Wallpaper | Designs, Patterns, Supply & Installation | Sadaf Al Bahr';
$page_description = 'Wallpaper designs and patterns from Sadaf Al Bahr on Naif Road, Dubai — vinyl, fabric, 3D and decorative wallpapers, supplied and installed.';
require __DIR__ . '/includes/header.php';

$designs = [
    ['Vinyl wallpaper', 'Washable, durable finishes for living rooms, corridors and commercial walls.', 'https://images.unsplash.com/photo-1615874959474-d609969a20d7?auto=format&fit=crop&w=900&q=80'],
    ['Fabric wallpaper', 'Soft texture and rich colour for bedrooms, majlis and feature walls.', 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=900&q=80'],
    ['3D & textured', 'Depth and shadow for reception areas and modern interiors.', 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=900&q=80'],
    ['Pattern books', 'Florals, geometrics, stripes, marble looks and children’s prints in the shop.', 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=900&q=80'],
    ['Feature walls', 'One statement wall behind a sofa, bed or TV — measured and aligned on site.', 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=900&q=80'],
    ['Wall stickers', 'Simple decorative overlays when a full wallpaper job is not needed.', 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=900&q=80'],
];
?>

<section class="page-banner banner-wallpaper">
  <div class="container">
    <p class="eyebrow">Wallpaper</p>
    <h1>Designs, patterns, supply and installation.</h1>
    <p>Choose from books in our Naif Road shop. We supply the rolls and paste them so seams stay quiet and corners stay sharp.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" style="color:var(--gold)">Product designs</p>
      <h2>A wall covering for every room.</h2>
      <p class="muted">Bring a photo of the space, or visit with measurements. We help you pick a pattern that suits light, furniture and budget. <a href="<?= e(sabPageHref('catalog')) ?>#wallpaper">Browse wallpaper designs in the catalogue</a>.</p>
    </div>
    <div class="catalog-grid">
      <?php foreach ($designs as [$title, $text, $img]): ?>
        <article class="card">
          <img src="<?= e($img) ?>" alt="<?= e($title) ?>">
          <div class="card-body">
            <h3><?= e($title) ?></h3>
            <p class="muted"><?= e($text) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container split">
    <div>
      <p class="eyebrow" style="color:var(--gold)">Supply &amp; installation</p>
      <h2>We do not leave you with rolls and a ladder.</h2>
      <p class="muted">Wallpaper supply includes advice on quantity, paste type and which walls to cover. Installation includes surface check, lining where needed, pattern matching, trimming at skirting and ceiling, and a clean finish around switches and corners.</p>
      <ul class="muted">
        <li>Site measure before we order extra rolls</li>
        <li>Pattern match across doors and windows</li>
        <li>Residential apartments, villas, offices and shops</li>
      </ul>
      <p>
        <a class="btn btn-gold" href="<?= e(sabPageHref('contact') . '?service=Wallpaper') ?>">Request a wallpaper quote</a>
        <a class="btn btn-outline" href="<?= e(sabPageHref('catalog')) ?>#wallpaper">Catalogue</a>
      </p>
    </div>
    <img src="https://images.unsplash.com/photo-1615874959474-d609969a20d7?auto=format&fit=crop&w=1200&q=80" alt="Installed wallpaper in a living room">
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
