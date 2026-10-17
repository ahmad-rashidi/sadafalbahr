<?php
require_once __DIR__ . '/includes/db.php';

$page_title = 'Gallery / Our Projects | Wallpaper, Curtains & Carpets in Dubai | Sadaf Al Bahr';
$page_description = 'See Sadaf Al Bahr projects in Dubai: wallpaper, curtains and carpet installations from our Naif Road shop in Deira.';
$canonical = sabAbsolute('gallery.php');
require __DIR__ . '/includes/header.php';

$live = sab_projects_public($pdo);
?>

<section class="page-banner banner-gallery">
  <div class="container">
    <p class="eyebrow">Gallery / Our Projects</p>
    <h1>Products in the shop, rooms after we finish.</h1>
    <p>Each job has its own page — wallpaper, curtains and carpets installed from Naif Road, Deira.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if ($live): ?>
      <div class="filter-bar" data-filters>
        <button type="button" class="is-active" data-filter="all">All</button>
        <?php foreach (SAB_PROJECT_CATEGORIES as $cat): ?>
          <button type="button" data-filter="<?= e($cat) ?>"><?= e($cat) ?></button>
        <?php endforeach; ?>
      </div>
      <div class="gallery-grid">
        <?php foreach ($live as $item): ?>
          <a class="project-tile" data-category="<?= e((string) $item['category']) ?>" href="<?= e(sabProjectUrl((string) $item['slug'])) ?>">
            <?php if (!empty($item['cover'])): ?>
              <img src="<?= e(sabMediaUrl((string) $item['cover'])) ?>" alt="<?= e((string) $item['title']) ?>">
            <?php else: ?>
              <img src="https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1000&q=80" alt="<?= e((string) $item['title']) ?>">
            <?php endif; ?>
            <span class="project-tile-copy">
              <strong><?= e((string) $item['title']) ?></strong>
              <?= e((string) $item['category']) ?><?= $item['location'] ? ' · ' . e((string) $item['location']) : '' ?>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="muted">Project photos will appear here as we add completed jobs. Meanwhile, visit the shop on Naif Road or <a href="<?= e(sabPageHref('contact')) ?>">ask for a quote</a>.</p>
    <?php endif; ?>
  </div>
</section>

<section class="section cta-band" style="padding:56px 0">
  <div class="container">
    <div>
      <h2 style="color:#fff;margin:0 0 8px">Have photos of your room?</h2>
      <p>Send them on WhatsApp and we will suggest wallpaper, curtain or carpet options.</p>
    </div>
    <a class="btn btn-gold" href="<?= e(sabWhatsApp('Hello Sadaf Al Bahr, I am sending room photos for a quote.')) ?>" target="_blank" rel="noopener">WhatsApp photos</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
