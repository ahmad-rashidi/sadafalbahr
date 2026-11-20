<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

try {
    $pdo = new PDO(
        "mysql:host={$sab_db_host};dbname={$sab_db_name};charset=utf8mb4",
        $sab_db_user,
        $sab_db_pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die('Sadaf Al Bahr database connection failed. Copy includes/local.example.php to includes/local.php and enter your Hostinger MySQL details. On XAMPP, create the database sadaf_al_bahr (see schema.sql).');
}

require_once __DIR__ . '/projects.php';
require_once __DIR__ . '/blog.php';
require_once __DIR__ . '/seo.php';
require_once __DIR__ . '/catalog.php';
sab_ensure_core_tables($pdo);
sab_ensure_project_tables($pdo);
sab_ensure_blog_tables($pdo);
sab_ensure_seo_tables($pdo);
sab_ensure_catalog_tables($pdo);

function sab_ensure_core_tables(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS inquiries (
            id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name       VARCHAR(120) NOT NULL,
            phone      VARCHAR(40)  NOT NULL,
            service    VARCHAR(80)  NOT NULL DEFAULT '',
            message    TEXT         NOT NULL,
            ip_address VARCHAR(45)  DEFAULT NULL,
            is_read    TINYINT(1)   NOT NULL DEFAULT 0,
            created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_inquiries_created (created_at),
            INDEX idx_inquiries_read (is_read)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

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
