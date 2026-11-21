<?php
$admin_page = basename($_SERVER['SCRIPT_NAME'] ?? '');
?>
<div class="admin-bar">
  <strong>Sadaf Al Bahr admin</strong>
  <nav>
    <a href="index.php" <?= $admin_page === 'index.php' ? 'aria-current="page"' : '' ?>>Inquiries</a>
    <a href="projects.php" <?= in_array($admin_page, ['projects.php', 'project-edit.php'], true) ? 'aria-current="page"' : '' ?>>Projects</a>
    <a href="catalog.php" <?= in_array($admin_page, ['catalog.php', 'catalog-edit.php'], true) ? 'aria-current="page"' : '' ?>>Catalogue</a>
    <a href="orders.php" <?= $admin_page === 'orders.php' ? 'aria-current="page"' : '' ?>>Orders</a>
    <a href="blog.php" <?= in_array($admin_page, ['blog.php', 'blog-edit.php'], true) ? 'aria-current="page"' : '' ?>>Blog</a>
    <a href="seo.php" <?= in_array($admin_page, ['seo.php'], true) ? 'aria-current="page"' : '' ?>>SEO</a>
    <a href="guide.php" <?= $admin_page === 'guide.php' ? 'aria-current="page"' : '' ?>>Guide</a>
    <a href="<?= htmlspecialchars('../') ?>" target="_blank" rel="noopener">View site</a>
    <a href="logout.php">Log out</a>
  </nav>
</div>
<script src="admin.js" defer></script>
