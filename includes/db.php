<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

$db_host = 'localhost';
$db_name = 'sadaf_al_bahr';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO(
        "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Sadaf Al Bahr database connection failed. Create the MySQL database sadaf_al_bahr (see schema.sql).');
}

require_once __DIR__ . '/projects.php';
require_once __DIR__ . '/blog.php';
require_once __DIR__ . '/seo.php';
sab_ensure_project_tables($pdo);
sab_ensure_blog_tables($pdo);
sab_ensure_seo_tables($pdo);

function sab_services(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT slug, title, summary, bullets, image_url FROM services WHERE is_active = 1 ORDER BY sort_order, id');
    return $stmt->fetchAll();
}

function sab_gallery(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT image_url, caption FROM gallery WHERE is_active = 1 ORDER BY sort_order, id');
    return $stmt->fetchAll();
}
