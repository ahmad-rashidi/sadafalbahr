<?php
declare(strict_types=1);

function sab_catalog_categories(): array
{
    return [
        'wallpaper' => 'Wallpaper',
        'curtains'  => 'Curtains',
        'carpets'   => 'Carpets',
    ];
}

function sab_price_units(): array
{
    return [
        'metre'  => 'Per metre (linear)',
        'sqm'    => 'Per square metre',
        'inch'   => 'Per inch (linear)',
        'sqinch' => 'Per square inch',
    ];
}

function sab_price_unit_label(string $unit): string
{
    return match ($unit) {
        'sqm'    => 'per m²',
        'inch'   => 'per inch',
        'sqinch' => 'per sq. inch',
        default  => 'per metre',
    };
}

function sab_price_is_area(string $unit): bool
{
    return in_array($unit, ['sqm', 'sqinch'], true);
}

function sab_format_aed(float $amount): string
{
    return 'AED ' . number_format($amount, 2);
}

function sab_catalog_format_price(array $item): string
{
    $amount = (float) ($item['price_amount'] ?? 0);
    if ($amount <= 0) {
        return 'Price on request';
    }
    return sab_format_aed($amount) . ' ' . sab_price_unit_label((string) $item['price_unit']);
}

function sab_catalog_has_column(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND COLUMN_NAME = :c'
    );
    $stmt->execute([':t' => $table, ':c' => $column]);
    return (int) $stmt->fetchColumn() > 0;
}

function sab_ensure_catalog_tables(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS catalog_items (
            id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            category         VARCHAR(20)  NOT NULL,
            company_name     VARCHAR(160) NOT NULL DEFAULT '',
            design_name      VARCHAR(180) NOT NULL,
            slug             VARCHAR(180) NOT NULL UNIQUE,
            details          TEXT         NOT NULL,
            price_amount     DECIMAL(10,2) NOT NULL DEFAULT 0,
            price_unit       VARCHAR(20)  NOT NULL DEFAULT 'metre',
            meta_title       VARCHAR(180) NOT NULL DEFAULT '',
            meta_description VARCHAR(320) NOT NULL DEFAULT '',
            focus_keyword    VARCHAR(120) NOT NULL DEFAULT '',
            is_published     TINYINT(1)   NOT NULL DEFAULT 1,
            sort_order       INT          NOT NULL DEFAULT 0,
            created_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_catalog_pub (is_published, category, sort_order, id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS catalog_images (
            id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            catalog_id INT UNSIGNED NOT NULL,
            image_path VARCHAR(255) NOT NULL,
            alt_text   VARCHAR(180) NOT NULL DEFAULT '',
            caption    VARCHAR(180) NOT NULL DEFAULT '',
            sort_order INT          NOT NULL DEFAULT 0,
            design_name VARCHAR(180) NOT NULL DEFAULT '',
            CONSTRAINT fk_catalog_images_item
              FOREIGN KEY (catalog_id) REFERENCES catalog_items(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    if (!sab_catalog_has_column($pdo, 'catalog_images', 'design_name')) {
        $pdo->exec("ALTER TABLE catalog_images ADD COLUMN design_name VARCHAR(180) NOT NULL DEFAULT '' AFTER image_path");
    }
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS catalog_orders (
            id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            catalog_id     INT UNSIGNED DEFAULT NULL,
            category       VARCHAR(20)  NOT NULL DEFAULT '',
            company_name   VARCHAR(160) NOT NULL DEFAULT '',
            design_name    VARCHAR(180) NOT NULL DEFAULT '',
            pattern_name   VARCHAR(180) NOT NULL DEFAULT '',
            image_id       INT UNSIGNED DEFAULT NULL,
            image_path     VARCHAR(255) NOT NULL DEFAULT '',
            price_amount   DECIMAL(10,2) NOT NULL DEFAULT 0,
            price_unit     VARCHAR(20)  NOT NULL DEFAULT 'metre',
            name           VARCHAR(120) NOT NULL,
            phone          VARCHAR(40)  NOT NULL,
            email          VARCHAR(160) NOT NULL DEFAULT '',
            width_value    DECIMAL(10,2) NOT NULL DEFAULT 0,
            height_value   DECIMAL(10,2) NOT NULL DEFAULT 0,
            measure_unit   VARCHAR(20)  NOT NULL DEFAULT 'metre',
            quantity       DECIMAL(10,2) NOT NULL DEFAULT 1,
            notes          TEXT         NOT NULL,
            estimate_aed   DECIMAL(12,2) DEFAULT NULL,
            ip_address     VARCHAR(45)  DEFAULT NULL,
            is_read        TINYINT(1)   NOT NULL DEFAULT 0,
            created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_catalog_orders_created (created_at),
            CONSTRAINT fk_catalog_orders_item
              FOREIGN KEY (catalog_id) REFERENCES catalog_items(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    if (!sab_catalog_has_column($pdo, 'catalog_orders', 'pattern_name')) {
        $pdo->exec("ALTER TABLE catalog_orders ADD COLUMN pattern_name VARCHAR(180) NOT NULL DEFAULT '' AFTER design_name");
    }
    if (!sab_catalog_has_column($pdo, 'catalog_orders', 'image_id')) {
        $pdo->exec("ALTER TABLE catalog_orders ADD COLUMN image_id INT UNSIGNED DEFAULT NULL AFTER pattern_name");
    }
    if (!sab_catalog_has_column($pdo, 'catalog_orders', 'image_path')) {
        $pdo->exec("ALTER TABLE catalog_orders ADD COLUMN image_path VARCHAR(255) NOT NULL DEFAULT '' AFTER image_id");
    }
}

function sab_unique_catalog_slug(PDO $pdo, string $slug, ?int $ignoreId = null): string
{
    $base = sab_slugify($slug);
    if ($base === 'project') {
        $base = 'design';
    }
    $try = $base;
    $n = 2;
    while (true) {
        $sql = 'SELECT id FROM catalog_items WHERE slug = :slug';
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

function sab_catalog_upload_dir(): string
{
    $dir = dirname(__DIR__) . '/assets/img/catalog';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    return $dir;
}

function sabCatalogUrl(string $slug): string
{
    return sabHref('catalog/' . rawurlencode($slug));
}

function sabCatalogOrderUrl(string $slug, int $imageId = 0): string
{
    $path = 'catalog/' . rawurlencode($slug) . '/order';
    if ($imageId > 0) {
        $path .= '?pick=' . $imageId;
    }
    return sabHref($path);
}

function sab_pattern_label(array $img, array $item = []): string
{
    foreach (['design_name', 'caption', 'alt_text'] as $key) {
        $value = trim((string) ($img[$key] ?? ''));
        if ($value !== '') {
            return $value;
        }
    }
    return trim((string) ($item['design_name'] ?? 'Selected design')) ?: 'Selected design';
}

function sab_filename_label(string $filename): string
{
    $base = pathinfo($filename, PATHINFO_FILENAME);
    $base = preg_replace('/[_]+/', ' ', $base) ?? $base;
    $base = trim($base);
    return mb_substr($base, 0, 180);
}

function sab_catalog_public(PDO $pdo, ?string $category = null): array
{
    $sql = 'SELECT c.*,
                   (SELECT image_path FROM catalog_images i WHERE i.catalog_id = c.id ORDER BY sort_order, id LIMIT 1) AS cover,
                   (SELECT COUNT(*) FROM catalog_images i WHERE i.catalog_id = c.id) AS image_count
            FROM catalog_items c
            WHERE c.is_published = 1';
    if ($category) {
        $sql .= ' AND c.category = :cat';
    }
    $sql .= ' ORDER BY c.sort_order ASC, c.id DESC';
    $stmt = $pdo->prepare($sql);
    if ($category) {
        $stmt->execute([':cat' => $category]);
    } else {
        $stmt->execute();
    }
    return $stmt->fetchAll();
}

function sab_catalog_grouped(PDO $pdo): array
{
    $grouped = [];
    foreach (array_keys(sab_catalog_categories()) as $key) {
        $grouped[$key] = [];
    }
    foreach (sab_catalog_public($pdo) as $row) {
        $cat = (string) $row['category'];
        if (!isset($grouped[$cat])) {
            $grouped[$cat] = [];
        }
        $grouped[$cat][] = $row;
    }
    return $grouped;
}

function sab_catalog_by_slug(PDO $pdo, string $slug, bool $publishedOnly = true): ?array
{
    $sql = 'SELECT * FROM catalog_items WHERE slug = :slug';
    if ($publishedOnly) {
        $sql .= ' AND is_published = 1';
    }
    $sql .= ' LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':slug' => $slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function sab_catalog_by_id(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM catalog_items WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function sab_catalog_images(PDO $pdo, int $catalogId): array
{
    $stmt = $pdo->prepare('SELECT * FROM catalog_images WHERE catalog_id = :id ORDER BY sort_order ASC, id ASC');
    $stmt->execute([':id' => $catalogId]);
    return $stmt->fetchAll();
}

function sab_catalog_image_by_id(PDO $pdo, int $imageId, int $catalogId): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM catalog_images WHERE id = :id AND catalog_id = :cid LIMIT 1');
    $stmt->execute([':id' => $imageId, ':cid' => $catalogId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function sab_catalog_admin_list(PDO $pdo): array
{
    return $pdo->query(
        'SELECT c.*,
                (SELECT COUNT(*) FROM catalog_images i WHERE i.catalog_id = c.id) AS image_count
         FROM catalog_items c
         ORDER BY c.category ASC, c.sort_order ASC, c.id DESC'
    )->fetchAll();
}

function sab_save_catalog_images(PDO $pdo, int $catalogId, array $files, string $defaultAlt): void
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
    $next = (int) $pdo->query('SELECT COALESCE(MAX(sort_order), 0) FROM catalog_images WHERE catalog_id = ' . $catalogId)->fetchColumn();
    $insert = $pdo->prepare(
        'INSERT INTO catalog_images (catalog_id, image_path, design_name, alt_text, caption, sort_order)
         VALUES (:catalog_id, :image_path, :design_name, :alt_text, :caption, :sort_order)'
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
        $dest = sab_catalog_upload_dir() . '/' . $name;
        if (!move_uploaded_file($tmp, $dest)) {
            continue;
        }
        $next++;
        $label = sab_filename_label((string) $files['name'][$i]);
        $insert->execute([
            ':catalog_id'  => $catalogId,
            ':image_path'  => 'assets/img/catalog/' . $name,
            ':design_name' => $label,
            ':alt_text'    => $label !== '' ? $label : $defaultAlt,
            ':caption'     => '',
            ':sort_order'  => $next,
        ]);
    }
}

function sab_delete_catalog_image_file(string $relative): void
{
    $full = dirname(__DIR__) . '/' . ltrim($relative, '/');
    $norm = str_replace('\\', '/', $full);
    if (is_file($full) && str_contains($norm, '/assets/img/catalog/')) {
        unlink($full);
    }
}

function sab_length_to_metre(float $value, string $from): float
{
    return $from === 'inch' ? $value / 39.37007874 : $value;
}

function sab_length_to_inch(float $value, string $from): float
{
    return $from === 'metre' ? $value * 39.37007874 : $value;
}

function sab_catalog_estimate(array $item, float $width, float $height, string $measureUnit, float $quantity = 1): ?float
{
    $price = (float) ($item['price_amount'] ?? 0);
    if ($price <= 0 || $width <= 0) {
        return null;
    }
    $quantity = $quantity > 0 ? $quantity : 1;
    $unit = (string) ($item['price_unit'] ?? 'metre');
    $measureUnit = $measureUnit === 'inch' ? 'inch' : 'metre';

    if (sab_price_is_area($unit)) {
        if ($height <= 0) {
            return null;
        }
        if ($unit === 'sqm') {
            $w = sab_length_to_metre($width, $measureUnit);
            $h = sab_length_to_metre($height, $measureUnit);
        } else {
            $w = sab_length_to_inch($width, $measureUnit);
            $h = sab_length_to_inch($height, $measureUnit);
        }
        return round($w * $h * $price * $quantity, 2);
    }

    $length = $unit === 'inch'
        ? sab_length_to_inch($width, $measureUnit)
        : sab_length_to_metre($width, $measureUnit);
    return round($length * $price * $quantity, 2);
}

function sab_save_catalog_order(PDO $pdo, array $item, array $post): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $errors = [];
    $token = (string) ($post['csrf'] ?? '');
    if ($token === '' || !hash_equals($_SESSION['sab_csrf'] ?? '', $token)) {
        $errors[] = 'Please refresh the page and try again.';
    }

    $name = trim((string) ($post['name'] ?? ''));
    $phone = trim((string) ($post['phone'] ?? ''));
    $email = trim((string) ($post['email'] ?? ''));
    $notes = trim((string) ($post['notes'] ?? ''));
    $measureUnit = ((string) ($post['measure_unit'] ?? 'metre')) === 'inch' ? 'inch' : 'metre';
    $width = (float) str_replace(',', '.', (string) ($post['width'] ?? '0'));
    $height = (float) str_replace(',', '.', (string) ($post['height'] ?? '0'));
    $quantity = (float) str_replace(',', '.', (string) ($post['quantity'] ?? '1'));
    $imageId = (int) ($post['image_id'] ?? 0);
    $image = $imageId > 0 ? sab_catalog_image_by_id($pdo, $imageId, (int) $item['id']) : null;
    $allImages = sab_catalog_images($pdo, (int) $item['id']);
    if ($allImages && !$image) {
        $errors[] = 'Please select a design from this catalogue first.';
    }

    if (mb_strlen($name) < 2) {
        $errors[] = 'Please enter your name.';
    }
    if (mb_strlen($phone) < 7) {
        $errors[] = 'Please enter a valid phone number.';
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email, or leave it blank.';
    }
    if ($width <= 0) {
        $errors[] = sab_price_is_area((string) $item['price_unit'])
            ? 'Please enter the width.'
            : 'Please enter the length or width you need.';
    }
    if (sab_price_is_area((string) $item['price_unit']) && $height <= 0) {
        $errors[] = 'Please enter the height or length of the area.';
    }
    if ($quantity <= 0) {
        $quantity = 1;
    }

    if ($errors) {
        return ['ok' => false, 'errors' => $errors];
    }

    $estimate = sab_catalog_estimate($item, $width, $height, $measureUnit, $quantity);

    $patternName = $image ? sab_pattern_label($image, $item) : (string) $item['design_name'];
    $stmt = $pdo->prepare(
        'INSERT INTO catalog_orders
         (catalog_id, category, company_name, design_name, pattern_name, image_id, image_path, price_amount, price_unit,
          name, phone, email, width_value, height_value, measure_unit, quantity, notes, estimate_aed, ip_address)
         VALUES
         (:catalog_id, :category, :company_name, :design_name, :pattern_name, :image_id, :image_path, :price_amount, :price_unit,
          :name, :phone, :email, :width_value, :height_value, :measure_unit, :quantity, :notes, :estimate_aed, :ip)'
    );
    $stmt->execute([
        ':catalog_id'   => (int) $item['id'],
        ':category'     => (string) $item['category'],
        ':company_name' => (string) $item['company_name'],
        ':design_name'  => (string) $item['design_name'],
        ':pattern_name' => $patternName,
        ':image_id'     => $image ? (int) $image['id'] : null,
        ':image_path'   => $image ? (string) $image['image_path'] : '',
        ':price_amount' => (float) $item['price_amount'],
        ':price_unit'   => (string) $item['price_unit'],
        ':name'         => $name,
        ':phone'        => $phone,
        ':email'        => $email,
        ':width_value'  => $width,
        ':height_value' => $height,
        ':measure_unit' => $measureUnit,
        ':quantity'     => $quantity,
        ':notes'        => $notes,
        ':estimate_aed' => $estimate,
        ':ip'           => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);

    return ['ok' => true, 'errors' => [], 'estimate' => $estimate];
}
