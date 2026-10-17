<?php
declare(strict_types=1);

function sab_ensure_blog_tables(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS blog_landing (
            id               TINYINT UNSIGNED PRIMARY KEY DEFAULT 1,
            heading          VARCHAR(180) NOT NULL DEFAULT 'Blog',
            intro            TEXT         NOT NULL,
            cover_image      VARCHAR(255) NOT NULL DEFAULT '',
            cover_alt        VARCHAR(180) NOT NULL DEFAULT '',
            meta_title       VARCHAR(180) NOT NULL DEFAULT '',
            meta_description VARCHAR(320) NOT NULL DEFAULT '',
            focus_keyword    VARCHAR(120) NOT NULL DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    $pdo->exec("
        INSERT IGNORE INTO blog_landing (id, heading, intro, meta_title, meta_description, focus_keyword)
        VALUES (
            1,
            'Ideas for walls, windows and floors in Dubai',
            'Guides from Sadaf Al Bahr on Naif Road: wallpaper, curtains, carpets and how we install them in Dubai homes and shops.',
            'Blog | Wallpaper, Curtains & Carpets in Dubai | Sadaf Al Bahr',
            'Read Sadaf Al Bahr guides on wallpaper, curtain and carpet installation in Dubai. Advice from our Naif Road, Deira shop.',
            'curtain wallpaper carpet Dubai blog'
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS blog_posts (
            id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title            VARCHAR(180) NOT NULL,
            slug             VARCHAR(180) NOT NULL UNIQUE,
            excerpt          VARCHAR(320) NOT NULL DEFAULT '',
            cover_image      VARCHAR(255) NOT NULL DEFAULT '',
            cover_alt        VARCHAR(180) NOT NULL DEFAULT '',
            category         VARCHAR(40)  NOT NULL DEFAULT 'Guides',
            meta_title       VARCHAR(180) NOT NULL DEFAULT '',
            meta_description VARCHAR(320) NOT NULL DEFAULT '',
            focus_keyword    VARCHAR(120) NOT NULL DEFAULT '',
            is_published     TINYINT(1)   NOT NULL DEFAULT 1,
            published_at     DATE         DEFAULT NULL,
            created_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_blog_pub (is_published, published_at, id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS blog_sections (
            id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            post_id    INT UNSIGNED NOT NULL,
            heading    VARCHAR(180) NOT NULL DEFAULT '',
            body       TEXT         NOT NULL,
            image_path VARCHAR(255) NOT NULL DEFAULT '',
            image_alt  VARCHAR(180) NOT NULL DEFAULT '',
            sort_order TINYINT UNSIGNED NOT NULL DEFAULT 1,
            CONSTRAINT fk_blog_sections_post
              FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

function sab_blog_upload_dir(): string
{
    $dir = dirname(__DIR__) . '/assets/img/blog';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    return $dir;
}

function sab_store_upload(?array $file, string $defaultName = 'image'): string
{
    if (!$file || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return '';
    }
    if ((int) $file['size'] > 8 * 1024 * 1024) {
        return '';
    }
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file((string) $file['tmp_name']);
    if (!isset($allowed[$mime])) {
        return '';
    }
    $name = sab_slugify($defaultName) . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    $dest = sab_blog_upload_dir() . '/' . $name;
    if (!move_uploaded_file((string) $file['tmp_name'], $dest)) {
        return '';
    }
    return 'assets/img/blog/' . $name;
}

function sab_blog_landing(PDO $pdo): array
{
    $row = $pdo->query('SELECT * FROM blog_landing WHERE id = 1')->fetch();
    return is_array($row) ? $row : [];
}

function sab_posts_public(PDO $pdo): array
{
    return $pdo->query(
        'SELECT * FROM blog_posts WHERE is_published = 1 ORDER BY COALESCE(published_at, created_at) DESC, id DESC'
    )->fetchAll();
}

function sab_post_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE slug = :slug AND is_published = 1 LIMIT 1');
    $stmt->execute([':slug' => $slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function sab_post_by_id(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function sab_post_sections(PDO $pdo, int $postId): array
{
    $stmt = $pdo->prepare('SELECT * FROM blog_sections WHERE post_id = :id ORDER BY sort_order ASC, id ASC');
    $stmt->execute([':id' => $postId]);
    return $stmt->fetchAll();
}

function sab_unique_blog_slug(PDO $pdo, string $slug, ?int $ignoreId = null): string
{
    $base = sab_slugify($slug);
    $try = $base;
    $n = 2;
    while (true) {
        $sql = 'SELECT id FROM blog_posts WHERE slug = :slug';
        if ($ignoreId) {
            $sql .= ' AND id != :id';
        }
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':slug', $try);
        if ($ignoreId) {
            $stmt->bindValue(':id', $ignoreId, PDO::PARAM_INT);
        }
        $stmt->execute();
        if (!$stmt->fetch()) {
            return $try;
        }
        $try = $base . '-' . $n;
        $n++;
    }
}

function sab_ensure_two_sections(PDO $pdo, int $postId): array
{
    $sections = sab_post_sections($pdo, $postId);
    $insert = $pdo->prepare(
        'INSERT INTO blog_sections (post_id, heading, body, image_path, image_alt, sort_order)
         VALUES (:post_id, :heading, :body, :image_path, :image_alt, :sort_order)'
    );
    for ($i = count($sections); $i < 2; $i++) {
        $insert->execute([
            ':post_id' => $postId,
            ':heading' => '',
            ':body' => '',
            ':image_path' => '',
            ':image_alt' => '',
            ':sort_order' => $i + 1,
        ]);
    }
    return sab_post_sections($pdo, $postId);
}
