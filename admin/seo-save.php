<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin-auth.php';
require_once __DIR__ . '/../includes/db.php';
sab_require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !sab_admin_csrf_ok((string) ($_POST['csrf'] ?? ''))) {
    header('Location: seo.php');
    exit;
}

$reserved = ['admin', 'assets', 'project', 'storage', 'sitemap', 'includes', 'route', 'post'];
$keys = $_POST['page_key'] ?? [];
if (!is_array($keys)) {
    header('Location: seo.php?error=' . rawurlencode('Nothing to save.'));
    exit;
}

$seen = [];
$upd = $pdo->prepare(
    'UPDATE page_seo SET slug=:slug, meta_title=:meta_title, meta_description=:meta_description,
     focus_keyword=:focus_keyword, robots=:robots, og_image=:og_image, priority=:priority, changefreq=:changefreq
     WHERE page_key=:page_key'
);

foreach ($keys as $key) {
    $key = (string) $key;
    $row = sab_page_seo($pdo, $key);
    if (!$row) {
        continue;
    }
    $slug = $key === 'home' ? '' : sab_slugify((string) ($_POST['slug'][$key] ?? $row['slug']));
    if ($slug !== '' && in_array($slug, $reserved, true)) {
        header('Location: seo.php?error=' . rawurlencode('Slug "' . $slug . '" is reserved.'));
        exit;
    }
    if ($slug !== '') {
        if (isset($seen[$slug])) {
            header('Location: seo.php?error=' . rawurlencode('Slug "' . $slug . '" is used twice.'));
            exit;
        }
        $seen[$slug] = $key;
    }
    $prio = trim((string) ($_POST['priority'][$key] ?? '0.8'));
    if (!preg_match('/^(0(\.\d)?|1(\.0)?)$/', $prio)) {
        $prio = '0.8';
    }
    $freq = (string) ($_POST['changefreq'][$key] ?? 'weekly');
    if (!in_array($freq, ['daily', 'weekly', 'monthly', 'yearly'], true)) {
        $freq = 'weekly';
    }
    $robots = (string) ($_POST['robots'][$key] ?? 'index,follow');
    $upd->execute([
        ':slug' => $slug,
        ':meta_title' => trim((string) ($_POST['meta_title'][$key] ?? '')),
        ':meta_description' => trim((string) ($_POST['meta_description'][$key] ?? '')),
        ':focus_keyword' => trim((string) ($_POST['focus_keyword'][$key] ?? '')),
        ':robots' => $robots,
        ':og_image' => trim((string) ($_POST['og_image'][$key] ?? '')),
        ':priority' => $prio,
        ':changefreq' => $freq,
        ':page_key' => $key,
    ]);
}

header('Location: seo.php?saved=1');
exit;
