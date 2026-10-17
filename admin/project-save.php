<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin-auth.php';
require_once __DIR__ . '/../includes/db.php';
sab_require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !sab_admin_csrf_ok((string) ($_POST['csrf'] ?? ''))) {
    header('Location: projects.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0 && isset($_POST['delete_project'])) {
    $images = sab_project_images($pdo, $id);
    foreach ($images as $img) {
        sab_delete_image_file((string) $img['image_path']);
    }
    $pdo->prepare('DELETE FROM projects WHERE id = :id')->execute([':id' => $id]);
    header('Location: projects.php');
    exit;
}

$title = trim((string) ($_POST['title'] ?? ''));
$description = trim((string) ($_POST['description'] ?? ''));
if ($title === '' || $description === '') {
    header('Location: project-edit.php' . ($id ? '?id=' . $id . '&error=' . rawurlencode('Title and description are required.') : '?error=' . rawurlencode('Title and description are required.')));
    exit;
}

$category = (string) ($_POST['category'] ?? 'Installation');
if (!in_array($category, SAB_PROJECT_CATEGORIES, true)) {
    $category = 'Installation';
}

$slugInput = trim((string) ($_POST['slug'] ?? ''));
$slug = sab_unique_slug($pdo, $slugInput !== '' ? $slugInput : $title, $id > 0 ? $id : null);
$metaTitle = trim((string) ($_POST['meta_title'] ?? ''));
$metaDesc = trim((string) ($_POST['meta_description'] ?? ''));
if ($metaTitle === '') {
    $metaTitle = $title . ' | ' . SAB_NAME . ' Dubai';
}
if ($metaDesc === '') {
    $metaDesc = substr(preg_replace('/\s+/', ' ', $description) ?? $description, 0, 160);
}

$fields = [
    ':title' => $title,
    ':slug' => $slug,
    ':category' => $category,
    ':location' => trim((string) ($_POST['location'] ?? '')),
    ':summary' => trim((string) ($_POST['summary'] ?? '')),
    ':description' => $description,
    ':meta_title' => $metaTitle,
    ':meta_description' => $metaDesc,
    ':focus_keyword' => trim((string) ($_POST['focus_keyword'] ?? '')),
    ':is_published' => isset($_POST['is_published']) ? 1 : 0,
    ':sort_order' => (int) ($_POST['sort_order'] ?? 0),
];

if ($id > 0) {
    $sql = 'UPDATE projects SET
        title = :title, slug = :slug, category = :category, location = :location,
        summary = :summary, description = :description, meta_title = :meta_title,
        meta_description = :meta_description, focus_keyword = :focus_keyword,
        is_published = :is_published, sort_order = :sort_order
        WHERE id = :id';
    $fields[':id'] = $id;
    $pdo->prepare($sql)->execute($fields);
} else {
    $sql = 'INSERT INTO projects
        (title, slug, category, location, summary, description, meta_title, meta_description, focus_keyword, is_published, sort_order)
        VALUES
        (:title, :slug, :category, :location, :summary, :description, :meta_title, :meta_description, :focus_keyword, :is_published, :sort_order)';
    $pdo->prepare($sql)->execute($fields);
    $id = (int) $pdo->lastInsertId();
}

if (!empty($_POST['delete_image']) && is_array($_POST['delete_image'])) {
    $del = $pdo->prepare('SELECT id, image_path FROM project_images WHERE id = :id AND project_id = :pid');
    $rm = $pdo->prepare('DELETE FROM project_images WHERE id = :id AND project_id = :pid');
    foreach ($_POST['delete_image'] as $imgId) {
        $imgId = (int) $imgId;
        $del->execute([':id' => $imgId, ':pid' => $id]);
        $row = $del->fetch();
        if ($row) {
            sab_delete_image_file((string) $row['image_path']);
            $rm->execute([':id' => $imgId, ':pid' => $id]);
        }
    }
}

$alts = $_POST['alt'] ?? [];
$captions = $_POST['caption'] ?? [];
$orders = $_POST['img_order'] ?? [];
if (is_array($alts)) {
    $upd = $pdo->prepare(
        'UPDATE project_images SET alt_text = :alt, caption = :caption, sort_order = :sort
         WHERE id = :id AND project_id = :pid'
    );
    foreach ($alts as $imgId => $alt) {
        $imgId = (int) $imgId;
        $upd->execute([
            ':alt' => trim((string) $alt),
            ':caption' => trim((string) ($captions[$imgId] ?? '')),
            ':sort' => (int) ($orders[$imgId] ?? 0),
            ':id' => $imgId,
            ':pid' => $id,
        ]);
    }
}

if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
    sab_save_uploaded_images($pdo, $id, $_FILES['images'], $title);
}

header('Location: project-edit.php?id=' . $id . '&saved=1');
exit;
