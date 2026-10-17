<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin-auth.php';
require_once __DIR__ . '/../includes/db.php';
sab_require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !sab_admin_csrf_ok((string) ($_POST['csrf'] ?? ''))) {
    header('Location: blog.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0 && isset($_POST['delete_post'])) {
    $pdo->prepare('DELETE FROM blog_posts WHERE id = :id')->execute([':id' => $id]);
    header('Location: blog.php');
    exit;
}

$title = trim((string) ($_POST['title'] ?? ''));
if ($title === '') {
    header('Location: blog-edit.php' . ($id ? '?id=' . $id . '&error=Title+required' : '?error=Title+required'));
    exit;
}

$slugInput = trim((string) ($_POST['slug'] ?? ''));
$slug = sab_unique_blog_slug($pdo, $slugInput !== '' ? $slugInput : $title, $id > 0 ? $id : null);
$metaTitle = trim((string) ($_POST['meta_title'] ?? ''));
$metaDesc = trim((string) ($_POST['meta_description'] ?? ''));
$excerpt = trim((string) ($_POST['excerpt'] ?? ''));
if ($metaTitle === '') {
    $metaTitle = $title . ' | ' . SAB_NAME . ' Dubai';
}
if ($metaDesc === '') {
    $metaDesc = substr($excerpt !== '' ? $excerpt : $title, 0, 160);
}

$existing = $id > 0 ? sab_post_by_id($pdo, $id) : null;
$cover = (string) ($existing['cover_image'] ?? '');
$uploaded = sab_store_upload($_FILES['cover_image'] ?? null, $title . '-cover');
if ($uploaded !== '') {
    $cover = $uploaded;
}

$fields = [
    ':title' => $title,
    ':slug' => $slug,
    ':excerpt' => $excerpt,
    ':cover_image' => $cover,
    ':cover_alt' => trim((string) ($_POST['cover_alt'] ?? '')),
    ':category' => trim((string) ($_POST['category'] ?? 'Guides')),
    ':meta_title' => $metaTitle,
    ':meta_description' => $metaDesc,
    ':focus_keyword' => trim((string) ($_POST['focus_keyword'] ?? '')),
    ':is_published' => isset($_POST['is_published']) ? 1 : 0,
    ':published_at' => trim((string) ($_POST['published_at'] ?? '')) ?: date('Y-m-d'),
];

if ($id > 0) {
    $sql = 'UPDATE blog_posts SET
        title=:title, slug=:slug, excerpt=:excerpt, cover_image=:cover_image, cover_alt=:cover_alt,
        category=:category, meta_title=:meta_title, meta_description=:meta_description,
        focus_keyword=:focus_keyword, is_published=:is_published, published_at=:published_at
        WHERE id=:id';
    $fields[':id'] = $id;
    $pdo->prepare($sql)->execute($fields);
} else {
    $sql = 'INSERT INTO blog_posts
        (title, slug, excerpt, cover_image, cover_alt, category, meta_title, meta_description, focus_keyword, is_published, published_at)
        VALUES
        (:title, :slug, :excerpt, :cover_image, :cover_alt, :category, :meta_title, :meta_description, :focus_keyword, :is_published, :published_at)';
    $pdo->prepare($sql)->execute($fields);
    $id = (int) $pdo->lastInsertId();
}

$sections = sab_ensure_two_sections($pdo, $id);
$update = $pdo->prepare(
    'UPDATE blog_sections SET heading=:heading, body=:body, image_path=:image_path, image_alt=:image_alt, sort_order=:sort
     WHERE id=:id AND post_id=:pid'
);

for ($n = 1; $n <= 2; $n++) {
    $sec = $sections[$n - 1] ?? null;
    if (!$sec) {
        continue;
    }
    $path = (string) $sec['image_path'];
    $fileKey = 'section_image_' . $n;
    $uploadedSec = sab_store_upload($_FILES[$fileKey] ?? null, $title . '-section-' . $n);
    if ($uploadedSec !== '') {
        $path = $uploadedSec;
    }
    $update->execute([
        ':heading' => trim((string) ($_POST['section_heading'][$n] ?? '')),
        ':body' => trim((string) ($_POST['section_body'][$n] ?? '')),
        ':image_path' => $path,
        ':image_alt' => trim((string) ($_POST['section_alt'][$n] ?? '')),
        ':sort' => $n,
        ':id' => (int) $sec['id'],
        ':pid' => $id,
    ]);
}

header('Location: blog-edit.php?id=' . $id . '&saved=1');
exit;
