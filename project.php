<?php
require_once __DIR__ . '/includes/db.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
if ($slug === '' && preg_match('#/project/([a-z0-9-]+)#i', (string) ($_SERVER['REQUEST_URI'] ?? ''), $m)) {
    $slug = strtolower($m[1]);
}

$project = $slug !== '' ? sab_project_by_slug($pdo, $slug) : null;
if (!$project) {
    http_response_code(404);
    $page_title = 'Project not found | ' . SAB_NAME;
    $page_description = 'This project page was not found.';
    $robots = 'noindex,follow';
    require __DIR__ . '/includes/header.php';
    echo '<section class="page-hero"><div class="container"><h1>Project not found</h1><p class="muted">This job may have been removed. See <a href="' . e(sabPageHref('gallery')) . '">our projects</a>.</p></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$images = sab_project_images($pdo, (int) $project['id']);
$cover = $images[0]['image_path'] ?? '';
$nav_current = 'gallery.php';
$page_title = (string) ($project['meta_title'] ?: ($project['title'] . ' | ' . SAB_NAME . ' Dubai'));
$page_description = (string) ($project['meta_description'] ?: $project['summary']);
$canonical = sabAbsolute('project/' . $project['slug']);
$og_type = 'article';
$og_image = $cover !== ''
    ? (str_starts_with($cover, 'http') ? $cover : sabAbsolute($cover))
    : null;
$imageUrls = [];
foreach ($images as $img) {
    $path = (string) $img['image_path'];
    $imageUrls[] = str_starts_with($path, 'http') ? $path : sabAbsolute($path);
}
$schema_json = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'CreativeWork',
    'name' => $project['title'],
    'headline' => $project['meta_title'] ?: $project['title'],
    'description' => $page_description,
    'image' => $imageUrls ?: $og_image,
    'url' => $canonical,
    'keywords' => $project['focus_keyword'] ?: $project['category'],
    'contentLocation' => $project['location'] ?: 'Naif Road, Deira, Dubai',
    'creator' => [
        '@type' => 'HomeAndConstructionBusiness',
        'name' => SAB_NAME,
        'telephone' => SAB_PHONE_DISPLAY,
        'address' => SAB_ADDRESS,
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

require __DIR__ . '/includes/header.php';
?>

<section class="page-banner banner-gallery" <?= $cover ? 'style="background-image:linear-gradient(180deg, rgba(7,38,46,.25), rgba(7,38,46,.78)), url(\'' . e(sabMediaUrl($cover)) . '\')"' : '' ?>>
  <div class="container">
    <p class="eyebrow"><?= e((string) $project['category']) ?><?= $project['location'] ? ' · ' . e((string) $project['location']) : '' ?></p>
    <h1><?= e((string) $project['title']) ?></h1>
    <?php if ($project['summary']): ?>
      <p><?= e((string) $project['summary']) ?></p>
    <?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container project-layout">
    <article>
      <div class="project-copy">
        <?= nl2br(e((string) $project['description'])) ?>
      </div>
      <p style="margin-top:28px">
        <a class="btn btn-gold" href="<?= e(sabPageHref('contact') . '?service=' . rawurlencode((string) $project['category'])) ?>">Ask for a similar job</a>
        <a class="btn btn-outline" href="<?= e(sabPageHref('gallery')) ?>">All projects</a>
      </p>
    </article>
    <aside>
      <div class="feature">
        <strong>This job</strong>
        <?= e((string) $project['category']) ?><br>
        <?= $project['location'] ? e((string) $project['location']) : 'Dubai' ?>
      </div>
    </aside>
  </div>
</section>

<?php if ($images): ?>
<section class="section" style="padding-top:0">
  <div class="container">
    <h2>Photos</h2>
    <div class="gallery-grid">
      <?php foreach ($images as $img): ?>
        <figure>
          <img src="<?= e(sabMediaUrl((string) $img['image_path'])) ?>" alt="<?= e((string) ($img['alt_text'] ?: $project['title'])) ?>">
          <?php if ($img['caption'] || $img['alt_text']): ?>
            <figcaption><?= e((string) ($img['caption'] ?: $img['alt_text'])) ?></figcaption>
          <?php endif; ?>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
