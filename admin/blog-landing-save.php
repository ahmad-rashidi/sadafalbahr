<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin-auth.php';
require_once __DIR__ . '/../includes/db.php';
sab_require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !sab_admin_csrf_ok((string) ($_POST['csrf'] ?? ''))) {
    header('Location: blog.php');
    exit;
}

$landing = sab_blog_landing($pdo);
$cover = (string) ($landing['cover_image'] ?? '');
$uploaded = sab_store_upload($_FILES['cover_image'] ?? null, 'blog-landing');
if ($uploaded !== '') {
    $cover = $uploaded;
}

$pdo->prepare(
    'UPDATE blog_landing SET heading=:heading, intro=:intro, cover_image=:cover_image, cover_alt=:cover_alt,
     meta_title=:meta_title, meta_description=:meta_description, focus_keyword=:focus_keyword WHERE id=1'
)->execute([
    ':heading' => trim((string) ($_POST['heading'] ?? '')),
    ':intro' => trim((string) ($_POST['intro'] ?? '')),
    ':cover_image' => $cover,
    ':cover_alt' => trim((string) ($_POST['cover_alt'] ?? '')),
    ':meta_title' => trim((string) ($_POST['meta_title'] ?? '')),
    ':meta_description' => trim((string) ($_POST['meta_description'] ?? '')),
    ':focus_keyword' => trim((string) ($_POST['focus_keyword'] ?? '')),
]);

header('Location: blog.php?saved=1');
exit;
