<?php
require_once __DIR__ . '/includes/db.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
if ($slug === '' && preg_match('#/catalog/([a-z0-9-]+)#i', (string) ($_SERVER['REQUEST_URI'] ?? ''), $m)) {
    $slug = strtolower($m[1]);
}

$item = $slug !== '' ? sab_catalog_by_slug($pdo, $slug) : null;
$cats = sab_catalog_categories();

if (!$item) {
    http_response_code(404);
    $page_title = 'Catalogue not found | ' . SAB_NAME;
    $page_description = 'This catalogue was not found.';
    $robots = 'noindex,follow';
    $nav_current = 'catalog.php';
    require __DIR__ . '/includes/header.php';
    echo '<section class="page-hero"><div class="container"><h1>Catalogue not found</h1><p class="muted">This book may have been removed. See the <a href="' . e(sabPageHref('catalog')) . '">catalogue</a>.</p></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$images = sab_catalog_images($pdo, (int) $item['id']);
$pickId = (int) ($_GET['pick'] ?? 0);
$picked = null;
foreach ($images as $img) {
    if ((int) $img['id'] === $pickId) {
        $picked = $img;
        break;
    }
}
if (!$picked && $images) {
    $picked = $images[0];
    $pickId = (int) $picked['id'];
}

$cover = $picked['image_path'] ?? ($images[0]['image_path'] ?? '');
$nav_current = 'catalog.php';
$catLabel = $cats[$item['category']] ?? ucfirst((string) $item['category']);
$page_title = (string) ($item['meta_title'] ?: ($item['design_name'] . ' | ' . $catLabel . ' | ' . SAB_NAME));
$page_description = (string) ($item['meta_description'] ?: mb_substr(preg_replace('/\s+/', ' ', (string) $item['details']) ?: $item['design_name'], 0, 160));
$canonical = sabAbsolute('catalog/' . $item['slug']);
$og_type = 'product';
$og_image = $cover !== ''
    ? (str_starts_with($cover, 'http') ? $cover : sabAbsolute($cover))
    : null;
$imageUrls = [];
foreach ($images as $img) {
    $path = (string) $img['image_path'];
    $imageUrls[] = str_starts_with($path, 'http') ? $path : sabAbsolute($path);
}
$offer = [
    '@type' => 'Offer',
    'priceCurrency' => 'AED',
    'availability' => 'https://schema.org/InStoreOnly',
    'url' => sabAbsolute('catalog/' . $item['slug'] . '/order'),
];
if ((float) $item['price_amount'] > 0) {
    $offer['price'] = (float) $item['price_amount'];
    $offer['unitText'] = sab_price_unit_label((string) $item['price_unit']);
}
$schema_json = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $item['design_name'],
    'description' => $page_description,
    'brand' => $item['company_name'] ?: SAB_NAME,
    'category' => $catLabel,
    'image' => $imageUrls ?: $og_image,
    'url' => $canonical,
    'offers' => $offer,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$bannerClass = match ((string) $item['category']) {
    'curtains' => 'banner-curtains',
    'carpets' => 'banner-carpets',
    default => 'banner-wallpaper',
};

require __DIR__ . '/includes/header.php';
?>

<section class="page-banner <?= e($bannerClass) ?>" <?= $cover ? 'style="background-image:linear-gradient(180deg, rgba(7,38,46,.25), rgba(7,38,46,.78)), url(\'' . e(sabMediaUrl($cover)) . '\')"' : '' ?>>
  <div class="container">
    <p class="eyebrow"><?= e($catLabel) ?><?= $item['company_name'] ? ' · ' . e((string) $item['company_name']) : '' ?></p>
    <h1><?= e((string) $item['design_name']) ?></h1>
    <p><?= count($images) ?> <?= count($images) === 1 ? 'design' : 'designs' ?> · <?= e(sab_catalog_format_price($item)) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <p class="muted" style="margin-top:0"><a href="<?= e(sabPageHref('catalog')) ?>">Catalogue</a> / <a href="<?= e(sabPageHref('catalog') . '#' . $item['category']) ?>"><?= e($catLabel) ?></a></p>
    <div class="project-copy" style="max-width:720px">
      <?= nl2br(e((string) $item['details'])) ?>
    </div>
    <p class="muted">Use the arrows to look through every sample in this book. The design on screen is the one you order — then enter metres or inches on the next page.</p>
  </div>
</section>

<?php if ($images): ?>
<section class="section" style="padding-top:0">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" style="color:var(--gold)">Choose your design</p>
      <h2>Browse with the arrows, then make your order</h2>
    </div>

    <div class="design-viewer" data-design-viewer>
      <div class="design-stage">
        <button class="design-arrow design-arrow-prev" type="button" data-prev aria-label="Previous design">
          <span aria-hidden="true">‹</span>
        </button>
        <div class="design-frame">
          <?php foreach ($images as $index => $img): ?>
            <?php
            $id = (int) $img['id'];
            $label = sab_pattern_label($img, $item);
            $active = $picked && (int) $picked['id'] === $id;
            ?>
            <figure class="design-slide<?= $active ? ' is-active' : '' ?>"
                    data-slide
                    data-id="<?= $id ?>"
                    data-name="<?= e($label) ?>"
                    data-order="<?= e(sabCatalogOrderUrl((string) $item['slug'], $id)) ?>"
                    <?= $active ? '' : 'hidden' ?>>
              <img src="<?= e(sabMediaUrl((string) $img['image_path'])) ?>" alt="<?= e($label) ?>">
            </figure>
          <?php endforeach; ?>
        </div>
        <button class="design-arrow design-arrow-next" type="button" data-next aria-label="Next design">
          <span aria-hidden="true">›</span>
        </button>
      </div>

      <div class="design-caption">
        <p class="design-count"><span data-counter><?php
          $start = 1;
          foreach ($images as $i => $img) {
              if ((int) $img['id'] === $pickId) {
                  $start = $i + 1;
                  break;
              }
          }
          echo $start;
        ?></span> / <?= count($images) ?></p>
        <h3 data-design-name><?= e($picked ? sab_pattern_label($picked, $item) : sab_pattern_label($images[0], $item)) ?></h3>
        <p class="muted"><?= e($catLabel) ?><?= $item['company_name'] ? ' · ' . e((string) $item['company_name']) : '' ?> · <?= e(sab_catalog_format_price($item)) ?></p>
        <p>
          <a class="btn btn-gold" data-order-btn href="<?= e(sabCatalogOrderUrl((string) $item['slug'], (int) $pickId)) ?>">Make your custom order</a>
        </p>
      </div>

      <?php if (count($images) > 1): ?>
        <div class="design-thumbs" data-thumbs>
          <?php foreach ($images as $img): ?>
            <?php
            $id = (int) $img['id'];
            $label = sab_pattern_label($img, $item);
            $active = $picked && (int) $picked['id'] === $id;
            ?>
            <button type="button" class="design-thumb<?= $active ? ' is-active' : '' ?>" data-thumb data-id="<?= $id ?>" aria-label="<?= e($label) ?>">
              <img src="<?= e(sabMediaUrl((string) $img['image_path'])) ?>" alt="">
            </button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
