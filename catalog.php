<?php
require_once __DIR__ . '/includes/db.php';

$nav_current = 'catalog.php';
$page_title = 'Catalogue | Wallpaper, Curtains & Carpet Designs | Sadaf Al Bahr';
$page_description = 'Browse wallpaper, curtain and carpet designs from supplier catalogues at Sadaf Al Bahr on Naif Road, Dubai. Choose a design and place a custom size order.';
require __DIR__ . '/includes/header.php';

$grouped = sab_catalog_grouped($pdo);
?>

<section class="page-banner banner-catalog">
  <div class="container">
    <p class="eyebrow">Catalogue</p>
    <h1>Designs from the houses we stock.</h1>
    <p>Wallpaper, curtains and carpets — pick a design, then send the size for a custom order.</p>
  </div>
</section>

<section class="section" style="padding-bottom:0">
  <div class="container">
    <nav class="filter-bar catalog-jump" aria-label="Catalogue sections">
      <?php foreach (sab_catalog_categories() as $key => $label): ?>
        <a href="#<?= e($key) ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
  </div>
</section>

<?php foreach (sab_catalog_categories() as $key => $label): ?>
  <?php $items = $grouped[$key] ?? []; ?>
  <section class="section catalog-section" id="<?= e($key) ?>">
    <div class="container">
      <div class="section-head">
        <p class="eyebrow" style="color:var(--gold)"><?= e($label) ?></p>
        <h2><?= e($label) ?> catalogues</h2>
        <p class="muted">Each card is a company book. Open it, use the arrows to pick a sample, then make your order with size.</p>
      </div>
      <?php if (!$items): ?>
        <p class="muted">Designs for this section will appear here as we add supplier catalogues. Visit the shop on Naif Road or <a href="<?= e(sabPageHref('contact')) ?>">ask us</a>.</p>
      <?php else: ?>
        <div class="catalog-grid">
          <?php foreach ($items as $item): ?>
            <a class="card catalog-card" href="<?= e(sabCatalogUrl((string) $item['slug'])) ?>">
              <?php if (!empty($item['cover'])): ?>
                <img src="<?= e(sabMediaUrl((string) $item['cover'])) ?>" alt="<?= e((string) $item['design_name']) ?>">
              <?php else: ?>
                <img src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=900&q=80" alt="<?= e((string) $item['design_name']) ?>">
              <?php endif; ?>
              <div class="card-body">
                <?php if ($item['company_name']): ?>
                  <p class="catalog-company"><?= e((string) $item['company_name']) ?></p>
                <?php endif; ?>
                <h3><?= e((string) $item['design_name']) ?></h3>
                <p class="catalog-price"><?= e(sab_catalog_format_price($item)) ?></p>
                <span class="more"><?= (int) ($item['image_count'] ?? 0) ?> designs — open book</span>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>
<?php endforeach; ?>

<section class="section cta-band" style="padding:56px 0">
  <div class="container">
    <div>
      <h2 style="color:#fff;margin:0 0 8px">Need help choosing?</h2>
      <p>Bring a photo of the room, or WhatsApp us. We match the design, then confirm size on site.</p>
    </div>
    <a class="btn btn-gold" href="<?= e(sabWhatsApp('Hello Sadaf Al Bahr, I would like help choosing a catalogue design.')) ?>" target="_blank" rel="noopener">WhatsApp the shop</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
