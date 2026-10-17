<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/xml; charset=UTF-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach (sab_page_seo_all($pdo) as $page) {
    if (($page['robots'] ?? '') !== '' && str_contains((string) $page['robots'], 'noindex')) {
        continue;
    }
    $loc = sab_page_canonical($page);
    $prio = htmlspecialchars((string) ($page['priority'] ?: '0.8'));
    $freq = htmlspecialchars((string) ($page['changefreq'] ?: 'weekly'));
    echo '  <url><loc>' . htmlspecialchars($loc) . '</loc><changefreq>' . $freq . '</changefreq><priority>' . $prio . '</priority></url>' . "\n";
}

foreach (sab_projects_public($pdo) as $project) {
    $loc = sabAbsolute('project/' . $project['slug']);
    $last = substr((string) ($project['updated_at'] ?? ''), 0, 10);
    echo '  <url><loc>' . htmlspecialchars($loc) . '</loc>';
    if ($last) {
        echo '<lastmod>' . htmlspecialchars($last) . '</lastmod>';
    }
    echo '<changefreq>monthly</changefreq><priority>0.7</priority></url>' . "\n";
}

foreach (sab_posts_public($pdo) as $post) {
    $loc = sabAbsolute('blog/' . $post['slug']);
    $last = substr((string) ($post['updated_at'] ?? ''), 0, 10);
    echo '  <url><loc>' . htmlspecialchars($loc) . '</loc>';
    if ($last) {
        echo '<lastmod>' . htmlspecialchars($last) . '</lastmod>';
    }
    echo '<changefreq>weekly</changefreq><priority>0.8</priority></url>' . "\n";
}

echo '</urlset>';
