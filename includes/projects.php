<?php
declare(strict_types=1);

function sab_ensure_project_tables(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS projects (
            id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title            VARCHAR(180) NOT NULL,
            slug             VARCHAR(180) NOT NULL UNIQUE,
            category         VARCHAR(40)  NOT NULL DEFAULT 'Installation',
            location         VARCHAR(160) NOT NULL DEFAULT '',
            summary          VARCHAR(300) NOT NULL DEFAULT '',
            description      TEXT         NOT NULL,
            meta_title       VARCHAR(180) NOT NULL DEFAULT '',
            meta_description VARCHAR(320) NOT NULL DEFAULT '',
            focus_keyword    VARCHAR(120) NOT NULL DEFAULT '',
            is_published     TINYINT(1)   NOT NULL DEFAULT 1,
            sort_order       INT          NOT NULL DEFAULT 0,
            created_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_projects_pub (is_published, sort_order, id),
            INDEX idx_projects_cat (category)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS project_images (
            id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            project_id INT UNSIGNED NOT NULL,
            image_path VARCHAR(255) NOT NULL,
            alt_text   VARCHAR(180) NOT NULL DEFAULT '',
            caption    VARCHAR(180) NOT NULL DEFAULT '',
            sort_order INT          NOT NULL DEFAULT 0,
            CONSTRAINT fk_project_images_project
              FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

function sab_slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text) ?? '';
    $text = trim($text, '-');
    return $text === '' ? 'project' : $text;
}

function sab_unique_slug(PDO $pdo, string $slug, ?int $ignoreId = null): string
{
    $base = sab_slugify($slug);
    $try = $base;
    $n = 2;
    while (true) {
        $sql = 'SELECT id FROM projects WHERE slug = :slug';
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

function sab_project_upload_dir(): string
{
    $dir = dirname(__DIR__) . '/assets/img/projects';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    return $dir;
}

function sab_projects_public(PDO $pdo): array
{
    $stmt = $pdo->query(
        'SELECT p.*,
                (SELECT image_path FROM project_images i WHERE i.project_id = p.id ORDER BY sort_order, id LIMIT 1) AS cover
         FROM projects p
         WHERE p.is_published = 1
         ORDER BY p.sort_order ASC, p.id DESC'
    );
    return $stmt->fetchAll();
}

function sab_project_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE slug = :slug AND is_published = 1 LIMIT 1');
    $stmt->execute([':slug' => $slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function sab_project_by_id(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function sab_project_images(PDO $pdo, int $projectId): array
{
    $stmt = $pdo->prepare('SELECT * FROM project_images WHERE project_id = :id ORDER BY sort_order ASC, id ASC');
    $stmt->execute([':id' => $projectId]);
    return $stmt->fetchAll();
}

function sab_save_uploaded_images(PDO $pdo, int $projectId, array $files, string $defaultAlt): void
{
    if (($files['error'][0] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return;
    }
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];
    $count = count($files['name']);
    $next = (int) $pdo->query('SELECT COALESCE(MAX(sort_order), 0) FROM project_images WHERE project_id = ' . $projectId)->fetchColumn();
    $insert = $pdo->prepare(
        'INSERT INTO project_images (project_id, image_path, alt_text, caption, sort_order)
         VALUES (:project_id, :image_path, :alt_text, :caption, :sort_order)'
    );

    for ($i = 0; $i < $count; $i++) {
        if ((int) $files['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }
        if ((int) $files['size'][$i] > 8 * 1024 * 1024) {
            continue;
        }
        $tmp = (string) $files['tmp_name'][$i];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp);
        if (!isset($allowed[$mime])) {
            continue;
        }
        $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
        $dest = sab_project_upload_dir() . '/' . $name;
        if (!move_uploaded_file($tmp, $dest)) {
            continue;
        }
        $next++;
        $insert->execute([
            ':project_id' => $projectId,
            ':image_path' => 'assets/img/projects/' . $name,
            ':alt_text'   => $defaultAlt,
            ':caption'    => '',
            ':sort_order' => $next,
        ]);
    }
}

function sab_delete_image_file(string $relative): void
{
    $full = dirname(__DIR__) . '/' . ltrim($relative, '/');
    if (is_file($full) && str_contains(str_replace('\\', '/', $full), '/assets/img/projects/')) {
        unlink($full);
    }
}
