<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$slug = strtolower(trim((string) ($_GET['slug'] ?? ''), '/'));
if ($slug === '' || !preg_match('/^[a-z0-9-]+$/', $slug)) {
    http_response_code(404);
    $page_title = 'Page not found | ' . SAB_NAME;
    $robots = 'noindex,follow';
    require __DIR__ . '/includes/header.php';
    echo '<section class="page-hero"><div class="container"><h1>Page not found</h1></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$row = sab_page_seo_by_slug($pdo, $slug);
$file = $row ? basename((string) $row['php_file']) : '';
$allowed = ['index.php', 'wallpaper.php', 'curtains.php', 'carpets.php', 'gallery.php', 'blog.php', 'about.php', 'contact.php'];
$path = __DIR__ . '/' . $file;

if (!$row || !in_array($file, $allowed, true) || !is_file($path)) {
    http_response_code(404);
    $page_title = 'Page not found | ' . SAB_NAME;
    $robots = 'noindex,follow';
    require __DIR__ . '/includes/header.php';
    echo '<section class="page-hero"><div class="container"><h1>Page not found</h1></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$_SERVER['SCRIPT_NAME'] = rtrim(sabBasePath(), '/') . '/' . $file;
require $path;
