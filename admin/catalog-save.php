<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin-auth.php';
require_once __DIR__ . '/../includes/db.php';
sab_require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !sab_admin_csrf_ok((string) ($_POST['csrf'] ?? ''))) {
    header('Location: catalog.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$cats = sab_catalog_categories();
$units = sab_price_units();

if ($id > 0 && isset($_POST['delete_item'])) {
    $images = sab_catalog_images($pdo, $id);
    foreach ($images as $img) {
        sab_delete_catalog_image_file((string) $img['image_path']);
    }
    $pdo->prepare('DELETE FROM catalog_items WHERE id = :id')->execute([':id' => $id]);
    header('Location: catalog.php');
    exit;
}

$design = trim((string) ($_POST['design_name'] ?? ''));
$details = trim((string) ($_POST['details'] ?? ''));
if ($design === '' || $details === '') {
    $q = $id ? '?id=' . $id . '&' : '?';
    header('Location: catalog-edit.php' . $q . 'error=' . rawurlencode('Catalogue title and details are required.'));
    exit;
}

$category = (string) ($_POST['category'] ?? 'wallpaper');
if (!isset($cats[$category])) {
    $category = 'wallpaper';
}
$priceUnit = (string) ($_POST['price_unit'] ?? 'metre');
if (!isset($units[$priceUnit])) {
    $priceUnit = 'metre';
}

$slugInput = trim((string) ($_POST['slug'] ?? ''));
$slug = sab_unique_catalog_slug($pdo, $slugInput !== '' ? $slugInput : $design, $id > 0 ? $id : null);
$metaTitle = trim((string) ($_POST['meta_title'] ?? ''));
$metaDesc = trim((string) ($_POST['meta_description'] ?? ''));
if ($metaTitle === '') {
    $metaTitle = $design . ' | ' . $cats[$category] . ' | ' . SAB_NAME;
}
if ($metaDesc === '') {
    $metaDesc = substr(preg_replace('/\s+/', ' ', $details) ?? $details, 0, 160);
}

$fields = [
    ':category' => $category,
    ':company_name' => trim((string) ($_POST['company_name'] ?? '')),
    ':design_name' => $design,
    ':slug' => $slug,
    ':details' => $details,
    ':price_amount' => (float) ($_POST['price_amount'] ?? 0),
    ':price_unit' => $priceUnit,
    ':meta_title' => $metaTitle,
    ':meta_description' => $metaDesc,
    ':focus_keyword' => trim((string) ($_POST['focus_keyword'] ?? '')),
    ':is_published' => isset($_POST['is_published']) ? 1 : 0,
    ':sort_order' => (int) ($_POST['sort_order'] ?? 0),
];

if ($id > 0) {
    $sql = 'UPDATE catalog_items SET
        category = :category, company_name = :company_name, design_name = :design_name, slug = :slug,
        details = :details, price_amount = :price_amount, price_unit = :price_unit,
        meta_title = :meta_title, meta_description = :meta_description, focus_keyword = :focus_keyword,
        is_published = :is_published, sort_order = :sort_order
        WHERE id = :id';
    $fields[':id'] = $id;
    $pdo->prepare($sql)->execute($fields);
} else {
    $sql = 'INSERT INTO catalog_items
        (category, company_name, design_name, slug, details, price_amount, price_unit,
         meta_title, meta_description, focus_keyword, is_published, sort_order)
        VALUES
        (:category, :company_name, :design_name, :slug, :details, :price_amount, :price_unit,
         :meta_title, :meta_description, :focus_keyword, :is_published, :sort_order)';
    $pdo->prepare($sql)->execute($fields);
    $id = (int) $pdo->lastInsertId();
}

if (!empty($_POST['delete_image']) && is_array($_POST['delete_image'])) {
    $del = $pdo->prepare('SELECT id, image_path FROM catalog_images WHERE id = :id AND catalog_id = :pid');
    $rm = $pdo->prepare('DELETE FROM catalog_images WHERE id = :id AND catalog_id = :pid');
    foreach ($_POST['delete_image'] as $imgId) {
        $imgId = (int) $imgId;
        $del->execute([':id' => $imgId, ':pid' => $id]);
        $row = $del->fetch();
        if ($row) {
            sab_delete_catalog_image_file((string) $row['image_path']);
            $rm->execute([':id' => $imgId, ':pid' => $id]);
        }
    }
}

$alts = $_POST['alt'] ?? [];
$captions = $_POST['caption'] ?? [];
$orders = $_POST['img_order'] ?? [];
$names = $_POST['pattern_name'] ?? [];
if (is_array($alts) || is_array($names)) {
    $upd = $pdo->prepare(
        'UPDATE catalog_images SET design_name = :design_name, alt_text = :alt, caption = :caption, sort_order = :sort
         WHERE id = :id AND catalog_id = :pid'
    );
    $ids = array_unique(array_merge(array_keys(is_array($alts) ? $alts : []), array_keys(is_array($names) ? $names : [])));
    foreach ($ids as $imgId) {
        $imgId = (int) $imgId;
        $upd->execute([
            ':design_name' => trim((string) ($names[$imgId] ?? '')),
            ':alt' => trim((string) ($alts[$imgId] ?? '')),
            ':caption' => trim((string) ($captions[$imgId] ?? '')),
            ':sort' => (int) ($orders[$imgId] ?? 0),
            ':id' => $imgId,
            ':pid' => $id,
        ]);
    }
}

foreach (['images', 'images_more', 'images_batch3'] as $field) {
    if (!empty($_FILES[$field]) && is_array($_FILES[$field]['name'])) {
        sab_save_catalog_images($pdo, $id, $_FILES[$field], $design);
    }
}

header('Location: catalog-edit.php?id=' . $id . '&saved=1');
exit;
