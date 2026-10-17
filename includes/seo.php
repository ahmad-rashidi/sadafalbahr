<?php
declare(strict_types=1);

function sab_seo_pages_seed(): array
{
    return [
        [
            'page_key' => 'home',
            'label' => 'Home',
            'php_file' => 'index.php',
            'slug' => '',
            'meta_title' => 'Sadaf Al Bahr | Wallpaper, Carpets & Curtains on Naif Road, Dubai',
            'meta_description' => 'Sadaf Al Bahr on Naif Road, Deira — wallpaper, carpets, curtains with professional fixing, and interior decoration in Dubai.',
            'focus_keyword' => 'wallpaper carpets curtains Dubai',
            'robots' => 'index,follow',
            'priority' => '1.0',
            'changefreq' => 'weekly',
        ],
        [
            'page_key' => 'wallpaper',
            'label' => 'Wallpaper',
            'php_file' => 'wallpaper.php',
            'slug' => 'wallpaper',
            'meta_title' => 'Wallpaper | Designs, Patterns, Supply & Installation | Sadaf Al Bahr',
            'meta_description' => 'Wallpaper designs and patterns from Sadaf Al Bahr on Naif Road, Dubai — vinyl, fabric, 3D and decorative wallpapers, supplied and installed.',
            'focus_keyword' => 'wallpaper installation Dubai',
            'robots' => 'index,follow',
            'priority' => '0.9',
            'changefreq' => 'weekly',
        ],
        [
            'page_key' => 'curtains',
            'label' => 'Curtains',
            'php_file' => 'curtains.php',
            'slug' => 'curtains',
            'meta_title' => 'Curtains | Styles, Fabrics, Blackout & Sheer | Sadaf Al Bahr',
            'meta_description' => 'Custom curtains in Dubai from Sadaf Al Bahr — blackout, sheer, fabrics, styles, measurements and professional installation on Naif Road.',
            'focus_keyword' => 'curtains Dubai Naif Road',
            'robots' => 'index,follow',
            'priority' => '0.9',
            'changefreq' => 'weekly',
        ],
        [
            'page_key' => 'carpets',
            'label' => 'Carpets',
            'php_file' => 'carpets.php',
            'slug' => 'carpets',
            'meta_title' => 'Carpets | Types, Colours, Residential & Commercial | Sadaf Al Bahr',
            'meta_description' => 'Carpet supply and installation in Dubai from Sadaf Al Bahr — types, colours, residential and commercial flooring on Naif Road.',
            'focus_keyword' => 'carpet installation Dubai',
            'robots' => 'index,follow',
            'priority' => '0.9',
            'changefreq' => 'weekly',
        ],
        [
            'page_key' => 'gallery',
            'label' => 'Projects / Gallery',
            'php_file' => 'gallery.php',
            'slug' => 'projects',
            'meta_title' => 'Gallery / Our Projects | Wallpaper, Curtains & Carpets in Dubai | Sadaf Al Bahr',
            'meta_description' => 'See Sadaf Al Bahr projects in Dubai: wallpaper, curtains and carpet installations from our Naif Road shop in Deira.',
            'focus_keyword' => 'curtain wallpaper projects Dubai',
            'robots' => 'index,follow',
            'priority' => '0.8',
            'changefreq' => 'weekly',
        ],
        [
            'page_key' => 'blog',
            'label' => 'Blog',
            'php_file' => 'blog.php',
            'slug' => 'blog',
            'meta_title' => 'Blog | Wallpaper, Curtains & Carpets in Dubai | Sadaf Al Bahr',
            'meta_description' => 'Read Sadaf Al Bahr guides on wallpaper, curtain and carpet installation in Dubai. Advice from our Naif Road, Deira shop.',
            'focus_keyword' => 'interior guides Dubai',
            'robots' => 'index,follow',
            'priority' => '0.8',
            'changefreq' => 'weekly',
        ],
        [
            'page_key' => 'about',
            'label' => 'About Us',
            'php_file' => 'about.php',
            'slug' => 'about',
            'meta_title' => 'About Us | Sadaf Al Bahr Naif Road Dubai',
            'meta_description' => 'About Sadaf Al Bahr — wallpaper, carpets and curtains with professional installation from Naif Road, Deira, Dubai.',
            'focus_keyword' => 'Sadaf Al Bahr Deira',
            'robots' => 'index,follow',
            'priority' => '0.7',
            'changefreq' => 'monthly',
        ],
        [
            'page_key' => 'contact',
            'label' => 'Contact Us',
            'php_file' => 'contact.php',
            'slug' => 'contact',
            'meta_title' => 'Contact Us | Sadaf Al Bahr Naif Road Dubai',
            'meta_description' => 'Contact Sadaf Al Bahr on Naif Road, Deira — Google Maps, phone, WhatsApp, opening hours and a quote request form.',
            'focus_keyword' => 'Sadaf Al Bahr Naif Road contact',
            'robots' => 'index,follow',
            'priority' => '0.8',
            'changefreq' => 'monthly',
        ],
    ];
}

function sab_ensure_seo_tables(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS page_seo (
            page_key         VARCHAR(40)  NOT NULL PRIMARY KEY,
            label            VARCHAR(80)  NOT NULL,
            php_file         VARCHAR(80)  NOT NULL,
            slug             VARCHAR(180) NOT NULL DEFAULT '',
            meta_title       VARCHAR(180) NOT NULL DEFAULT '',
            meta_description VARCHAR(320) NOT NULL DEFAULT '',
            focus_keyword    VARCHAR(120) NOT NULL DEFAULT '',
            robots           VARCHAR(40)  NOT NULL DEFAULT 'index,follow',
            og_image         VARCHAR(500) NOT NULL DEFAULT '',
            priority         VARCHAR(4)   NOT NULL DEFAULT '0.8',
            changefreq       VARCHAR(20)  NOT NULL DEFAULT 'weekly'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $stmt = $pdo->prepare(
        'INSERT IGNORE INTO page_seo
         (page_key, label, php_file, slug, meta_title, meta_description, focus_keyword, robots, priority, changefreq)
         VALUES
         (:page_key, :label, :php_file, :slug, :meta_title, :meta_description, :focus_keyword, :robots, :priority, :changefreq)'
    );
    foreach (sab_seo_pages_seed() as $row) {
        $stmt->execute($row);
    }
}

function sab_page_seo_all(PDO $pdo): array
{
    return $pdo->query("SELECT * FROM page_seo ORDER BY FIELD(page_key, 'home','wallpaper','curtains','carpets','gallery','blog','about','contact'), page_key")->fetchAll();
}

function sab_page_seo(PDO $pdo, string $key): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM page_seo WHERE page_key = :k LIMIT 1');
    $stmt->execute([':k' => $key]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function sab_page_seo_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM page_seo WHERE slug = :s AND slug != "" LIMIT 1');
    $stmt->execute([':s' => $slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function sab_script_page_key(): ?string
{
    $map = [
        'index.php' => 'home',
        'wallpaper.php' => 'wallpaper',
        'curtains.php' => 'curtains',
        'carpets.php' => 'carpets',
        'gallery.php' => 'gallery',
        'blog.php' => 'blog',
        'about.php' => 'about',
        'contact.php' => 'contact',
    ];
    $base = basename($_SERVER['SCRIPT_NAME'] ?? '');
    return $map[$base] ?? null;
}

function sabPageHref(string $key): string
{
    global $pdo;
    static $slugs = null;
    if ($slugs === null) {
        $slugs = [];
        if (isset($pdo) && $pdo instanceof PDO) {
            foreach (sab_page_seo_all($pdo) as $row) {
                $slugs[$row['page_key']] = (string) $row['slug'];
            }
        }
    }
    $fallback = [
        'home' => 'index.php',
        'wallpaper' => 'wallpaper.php',
        'curtains' => 'curtains.php',
        'carpets' => 'carpets.php',
        'gallery' => 'gallery.php',
        'blog' => 'blog.php',
        'about' => 'about.php',
        'contact' => 'contact.php',
    ];
    if ($key === 'home') {
        return sabHref('');
    }
    $slug = $slugs[$key] ?? '';
    if ($slug !== '') {
        return sabHref($slug);
    }
    return sabHref($fallback[$key] ?? '');
}

function sab_page_canonical(array $row): string
{
    if ($row['page_key'] === 'home' || $row['slug'] === '') {
        return sabAbsolute('');
    }
    return sabAbsolute((string) $row['slug']);
}
