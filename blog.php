<?php
require_once __DIR__ . '/includes/db.php';

$landing = sab_blog_landing($pdo);
$posts = sab_posts_public($pdo);

$page_title = (string) ($landing['meta_title'] ?: ('Blog | ' . SAB_NAME . ' Dubai'));
$page_description = (string) ($landing['meta_description'] ?: 'Guides on wallpaper, curtains and carpets in Dubai from Sadaf Al Bahr, Naif Road.');
$canonical = sabAbsolute('blog.php');
$og_type = 'website';
if (!empty($landing['cover_image'])) {
    $og_image = sabMediaUrl((string) $landing['cover_image']);
    if (!str_starts_with($og_image, 'http')) {
        $og_image = sabAbsolute((string) $landing['cover_image']);
    }
}
$itemList = [];
$n = 1;
foreach ($posts as $post) {
    $itemList[] = [
        '@type' => 'ListItem',
        'position' => $n++,
        'url' => sabAbsolute('blog/' . $post['slug']),
        'name' => $post['title'],
    ];
}
$schema_json = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Blog',
    'name' => $page_title,
    'description' => $page_description,
    'url' => $canonical,
    'publisher' => ['@type' => 'Organization', 'name' => SAB_NAME],
    'blogPost' => array_map(static function (array $post): array {
        return [
            '@type' => 'BlogPosting',
            'headline' => $post['title'],
            'url' => sabAbsolute('blog/' . $post['slug']),
            'datePublished' => $post['published_at'] ?: substr((string) $post['created_at'], 0, 10),
        ];
    }, $posts),
    'mainEntity' => [
        '@type' => 'ItemList',
        'itemListElement' => $itemList,
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

require __DIR__ . '/includes/header.php';
$cover = (string) ($landing['cover_image'] ?? '');
?>

<section class="page-banner banner-gallery" <?= $cover ? 'style="background-image:linear-gradient(180deg, rgba(7,38,46,.25), rgba(7,38,46,.78)), url(\'' . e(sabMediaUrl($cover)) . '\')"' : '' ?>>
  <div class="container">
    <p class="eyebrow">Blog</p>
    <h1><?= e((string) ($landing['heading'] ?: 'Blog')) ?></h1>
    <?php if (!empty($landing['intro'])): ?>
      <p><?= e((string) $landing['intro']) ?></p>
    <?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (!$posts): ?>
      <p class="muted">Articles will appear here. Ask in the shop on Naif Road, or <a href="<?= e(sabPageHref('contact')) ?>">send a quote request</a>.</p>
    <?php else: ?>
      <div class="catalog-grid">
        <?php foreach ($posts as $post): ?>
          <article class="card">
            <a href="<?= e(sabBlogUrl((string) $post['slug'])) ?>">
              <?php if ($post['cover_image']): ?>
                <img src="<?= e(sabMediaUrl((string) $post['cover_image'])) ?>" alt="<?= e((string) ($post['cover_alt'] ?: $post['title'])) ?>">
              <?php else: ?>
                <img src="https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=900&q=80" alt="<?= e((string) $post['title']) ?>">
              <?php endif; ?>
            </a>
            <div class="card-body">
              <p class="eyebrow" style="color:var(--gold)"><?= e((string) $post['category']) ?></p>
              <h2 style="font-size:1.6rem;margin:0 0 8px">
                <a href="<?= e(sabBlogUrl((string) $post['slug'])) ?>" style="text-decoration:none;color:inherit"><?= e((string) $post['title']) ?></a>
              </h2>
              <p class="muted"><?= e((string) $post['excerpt']) ?></p>
              <a class="more" href="<?= e(sabBlogUrl((string) $post['slug'])) ?>">Read article</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
