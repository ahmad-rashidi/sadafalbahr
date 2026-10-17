<?php
require_once __DIR__ . '/includes/db.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
if ($slug === '' && preg_match('#/blog/([a-z0-9-]+)#i', (string) ($_SERVER['REQUEST_URI'] ?? ''), $m)) {
    $slug = strtolower($m[1]);
}

$post = $slug !== '' ? sab_post_by_slug($pdo, $slug) : null;
if (!$post) {
    http_response_code(404);
    $page_title = 'Article not found | ' . SAB_NAME;
    $page_description = 'This blog article was not found.';
    $robots = 'noindex,follow';
    require __DIR__ . '/includes/header.php';
    echo '<section class="page-hero"><div class="container"><h1>Article not found</h1><p class="muted"><a href="' . e(sabPageHref('blog')) . '">Back to the blog</a>.</p></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$sections = sab_post_sections($pdo, (int) $post['id']);
$nav_current = 'blog.php';
$page_title = (string) ($post['meta_title'] ?: ($post['title'] . ' | ' . SAB_NAME . ' Blog'));
$page_description = (string) ($post['meta_description'] ?: $post['excerpt']);
$canonical = sabAbsolute('blog/' . $post['slug']);
$og_type = 'article';
$cover = (string) $post['cover_image'];
if ($cover === '' && !empty($sections[0]['image_path'])) {
    $cover = (string) $sections[0]['image_path'];
}
$og_image = $cover !== ''
    ? (str_starts_with($cover, 'http') ? $cover : sabAbsolute($cover))
    : null;
$date = (string) ($post['published_at'] ?: substr((string) $post['created_at'], 0, 10));
$images = [];
if ($cover) {
    $images[] = str_starts_with($cover, 'http') ? $cover : sabAbsolute($cover);
}
foreach ($sections as $sec) {
    if (!empty($sec['image_path'])) {
        $p = (string) $sec['image_path'];
        $images[] = str_starts_with($p, 'http') ? $p : sabAbsolute($p);
    }
}
$schema_json = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $post['meta_title'] ?: $post['title'],
    'name' => $post['title'],
    'description' => $page_description,
    'image' => array_values(array_unique($images)),
    'datePublished' => $date,
    'dateModified' => substr((string) $post['updated_at'], 0, 10),
    'mainEntityOfPage' => $canonical,
    'url' => $canonical,
    'keywords' => $post['focus_keyword'] ?: $post['category'],
    'articleSection' => $post['category'],
    'author' => ['@type' => 'Organization', 'name' => SAB_NAME],
    'publisher' => [
        '@type' => 'Organization',
        'name' => SAB_NAME,
        'telephone' => SAB_PHONE_DISPLAY,
        'address' => SAB_ADDRESS,
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

require __DIR__ . '/includes/header.php';
?>

<article>
  <header class="page-banner banner-gallery" <?= $cover ? 'style="background-image:linear-gradient(180deg, rgba(7,38,46,.25), rgba(7,38,46,.78)), url(\'' . e(sabMediaUrl($cover)) . '\')"' : '' ?>>
    <div class="container">
      <p class="eyebrow"><?= e((string) $post['category']) ?> · <?= e($date) ?></p>
      <h1><?= e((string) $post['title']) ?></h1>
      <?php if ($post['excerpt']): ?>
        <p><?= e((string) $post['excerpt']) ?></p>
      <?php endif; ?>
    </div>
  </header>

  <?php foreach ($sections as $i => $sec): ?>
    <?php if (trim((string) $sec['heading'] . $sec['body'] . $sec['image_path']) === '') {
        continue;
    } ?>
    <section class="section" <?= $i % 2 === 1 ? 'style="background:#efe6d6"' : '' ?>>
      <div class="container split<?= $i % 2 === 1 ? ' split-flip' : '' ?>">
        <?php if ($i % 2 === 0): ?>
          <?php if ($sec['image_path']): ?>
            <img src="<?= e(sabMediaUrl((string) $sec['image_path'])) ?>" alt="<?= e((string) ($sec['image_alt'] ?: $sec['heading'] ?: $post['title'])) ?>">
          <?php endif; ?>
          <div>
            <?php if ($sec['heading']): ?><h2><?= e((string) $sec['heading']) ?></h2><?php endif; ?>
            <div class="muted project-copy"><?= nl2br(e((string) $sec['body'])) ?></div>
          </div>
        <?php else: ?>
          <div>
            <?php if ($sec['heading']): ?><h2><?= e((string) $sec['heading']) ?></h2><?php endif; ?>
            <div class="muted project-copy"><?= nl2br(e((string) $sec['body'])) ?></div>
          </div>
          <?php if ($sec['image_path']): ?>
            <img src="<?= e(sabMediaUrl((string) $sec['image_path'])) ?>" alt="<?= e((string) ($sec['image_alt'] ?: $sec['heading'] ?: $post['title'])) ?>">
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </section>
  <?php endforeach; ?>

  <section class="section cta-band" style="padding:56px 0">
    <div class="container">
      <div>
        <h2 style="color:#fff;margin:0 0 8px">Need this done in your room?</h2>
        <p>We supply and install from Naif Road, Deira.</p>
      </div>
      <a class="btn btn-gold" href="<?= e(sabPageHref('contact')) ?>">Request a quote</a>
    </div>
  </section>
</article>

<?php require __DIR__ . '/includes/footer.php'; ?>
