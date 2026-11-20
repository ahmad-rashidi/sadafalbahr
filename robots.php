<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/config.php';

header('Content-Type: text/plain; charset=UTF-8');
$sitemap = sabAbsolute('sitemap.xml');
echo "User-agent: *\n";
echo "Allow: /\n";
echo "Disallow: /admin/\n";
echo "Disallow: /includes/\n";
echo "\n";
echo 'Sitemap: ' . $sitemap . "\n";
