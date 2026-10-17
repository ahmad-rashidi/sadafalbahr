<?php
require_once __DIR__ . '/db.php';

$current = $nav_current ?? basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
$page_title = $page_title ?? SAB_NAME . ' | Wallpaper, Carpets & Curtains in Dubai';
$page_description = $page_description ?? 'Sadaf Al Bahr on Naif Road, Deira — wallpaper, carpets, curtains with professional fixing, and interior decoration in Dubai.';
$canonicalPath = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
if ($canonicalPath === 'index.php') {
    $canonicalPath = '';
}
$canonical = $canonical ?? sabAbsolute($canonicalPath);
$og_image = $og_image ?? 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1200&q=80';
$og_type = $og_type ?? 'website';
$robots = $robots ?? 'index,follow';
$focus_keyword = $focus_keyword ?? '';

$seo_key = $seo_page_key ?? sab_script_page_key();
if ($seo_key && empty($skip_page_seo)) {
    $seo_row = sab_page_seo($pdo, $seo_key);
    if ($seo_row) {
        if (trim((string) $seo_row['meta_title']) !== '') {
            $page_title = (string) $seo_row['meta_title'];
        }
        if (trim((string) $seo_row['meta_description']) !== '') {
            $page_description = (string) $seo_row['meta_description'];
        }
        if (trim((string) $seo_row['robots']) !== '') {
            $robots = (string) $seo_row['robots'];
        }
        if (trim((string) $seo_row['focus_keyword']) !== '') {
            $focus_keyword = (string) $seo_row['focus_keyword'];
        }
        if (trim((string) $seo_row['og_image']) !== '') {
            $og = (string) $seo_row['og_image'];
            $og_image = str_starts_with($og, 'http') ? $og : sabAbsolute($og);
        }
        $canonical = sab_page_canonical($seo_row);
    }
}
if (!isset($schema_json)) {
    $schema_json = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'HomeAndConstructionBusiness',
        'name' => SAB_NAME,
        'image' => $og_image,
        'telephone' => SAB_PHONE_DISPLAY,
        'email' => SAB_EMAIL,
        'url' => sabAbsolute(''),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Naif Road, Dr. Ibrahim Al Marzouqi Building 187, Ground Floor',
            'addressLocality' => 'Deira',
            'addressRegion' => 'Dubai',
            'addressCountry' => 'AE',
        ],
        'areaServed' => 'Dubai',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($page_title) ?></title>
  <meta name="description" content="<?= e($page_description) ?>">
  <?php if ($focus_keyword !== ''): ?>
  <meta name="keywords" content="<?= e($focus_keyword) ?>">
  <?php endif; ?>
  <meta name="robots" content="<?= e($robots) ?>">
  <link rel="canonical" href="<?= e($canonical) ?>">
  <meta property="og:site_name" content="<?= e(SAB_NAME) ?>">
  <meta property="og:type" content="<?= e($og_type) ?>">
  <meta property="og:title" content="<?= e($page_title) ?>">
  <meta property="og:description" content="<?= e($page_description) ?>">
  <meta property="og:url" content="<?= e($canonical) ?>">
  <meta property="og:image" content="<?= e($og_image) ?>">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= e($page_title) ?>">
  <meta name="twitter:description" content="<?= e($page_description) ?>">
  <meta name="twitter:image" content="<?= e($og_image) ?>">
  <?php if (!empty($schema_json)): ?>
  <script type="application/ld+json"><?= $schema_json ?></script>
  <?php endif; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Noto+Naskh+Arabic:wght@500;600&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(sabHref('assets/css/style.css')) ?>">
</head>
<body>
  <a class="skip-link" href="#main">Skip to content</a>
  <header class="site-header" data-header>
    <div class="container header-inner">
      <a class="logo" href="<?= e(sabPageHref('home')) ?>">
        <span class="logo-mark" aria-hidden="true">ص</span>
        <span class="logo-text">
          <strong><?= e(SAB_NAME) ?></strong>
          <span class="logo-ar"><?= SAB_NAME_AR ?></span>
        </span>
      </a>
      <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" data-nav-toggle>
        <span></span><span></span><span></span>
        <span class="sr-only">Menu</span>
      </button>
      <nav class="site-nav" id="site-nav" data-nav>
        <a href="<?= e(sabPageHref('home')) ?>" <?= $current === 'index.php' ? 'aria-current="page"' : '' ?>>Home</a>
        <a href="<?= e(sabPageHref('wallpaper')) ?>" <?= $current === 'wallpaper.php' ? 'aria-current="page"' : '' ?>>Wallpaper</a>
        <a href="<?= e(sabPageHref('curtains')) ?>" <?= $current === 'curtains.php' ? 'aria-current="page"' : '' ?>>Curtains</a>
        <a href="<?= e(sabPageHref('carpets')) ?>" <?= $current === 'carpets.php' ? 'aria-current="page"' : '' ?>>Carpets</a>
        <a href="<?= e(sabPageHref('gallery')) ?>" <?= $current === 'gallery.php' ? 'aria-current="page"' : '' ?>>Projects</a>
        <a href="<?= e(sabPageHref('blog')) ?>" <?= $current === 'blog.php' ? 'aria-current="page"' : '' ?>>Blog</a>
        <a href="<?= e(sabPageHref('about')) ?>" <?= $current === 'about.php' ? 'aria-current="page"' : '' ?>>About Us</a>
        <a href="<?= e(sabPageHref('contact')) ?>" <?= $current === 'contact.php' ? 'aria-current="page"' : '' ?>>Contact Us</a>
        <a class="btn btn-gold nav-cta" href="<?= e(sabWhatsApp()) ?>" target="_blank" rel="noopener">WhatsApp</a>
      </nav>
    </div>
  </header>
  <main id="main">
