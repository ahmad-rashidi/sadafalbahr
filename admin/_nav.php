<?php
$admin_page = basename($_SERVER['SCRIPT_NAME'] ?? '');
?>
<div class="admin-bar">
  <strong>Sadaf Al Bahr admin</strong>
  <nav>
    <a href="index.php" <?= $admin_page === 'index.php' ? 'aria-current="page"' : '' ?>>Inquiries</a>
    <a href="projects.php" <?= in_array($admin_page, ['projects.php', 'project-edit.php'], true) ? 'aria-current="page"' : '' ?>>Projects</a>
    <a href="blog.php" <?= in_array($admin_page, ['blog.php', 'blog-edit.php'], true) ? 'aria-current="page"' : '' ?>>Blog</a>
    <a href="seo.php" <?= in_array($admin_page, ['seo.php'], true) ? 'aria-current="page"' : '' ?>>SEO</a>
    <a href="logout.php">Log out</a>
  </nav>
</div>
