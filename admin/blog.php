<?php
require_once __DIR__ . '/../includes/admin-auth.php';
require_once __DIR__ . '/../includes/db.php';
sab_require_admin();

$landing = sab_blog_landing($pdo);
$posts = $pdo->query('SELECT * FROM blog_posts ORDER BY id DESC')->fetchAll();
$saved = isset($_GET['saved']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Blog | Admin</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="wrap wrap-wide">
    <?php include __DIR__ . '/_nav.php'; ?>
    <div class="top">
      <h1>Blog</h1>
      <a class="btn" href="blog-edit.php">Add article</a>
    </div>
    <?php if ($saved): ?><p class="ok">Landing page saved.</p><?php endif; ?>

    <form class="admin-form" method="post" action="blog-landing-save.php" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars(sab_admin_csrf()) ?>">
      <fieldset>
        <legend>Blog landing page (list) — SEO + intro</legend>
        <label>H1 heading
          <input name="heading" maxlength="180" value="<?= htmlspecialchars((string) ($landing['heading'] ?? '')) ?>">
        </label>
        <label>Intro text
          <textarea name="intro" rows="3"><?= htmlspecialchars((string) ($landing['intro'] ?? '')) ?></textarea>
        </label>
        <label>Meta title
          <input name="meta_title" maxlength="180" value="<?= htmlspecialchars((string) ($landing['meta_title'] ?? '')) ?>">
        </label>
        <label>Meta description
          <textarea name="meta_description" rows="3" maxlength="320"><?= htmlspecialchars((string) ($landing['meta_description'] ?? '')) ?></textarea>
        </label>
        <label>Focus keyword
          <input name="focus_keyword" maxlength="120" value="<?= htmlspecialchars((string) ($landing['focus_keyword'] ?? '')) ?>">
        </label>
        <label>Cover image alt
          <input name="cover_alt" maxlength="180" value="<?= htmlspecialchars((string) ($landing['cover_alt'] ?? '')) ?>">
        </label>
        <?php if (!empty($landing['cover_image'])): ?>
          <p><img src="../<?= htmlspecialchars((string) $landing['cover_image']) ?>" alt="" style="max-width:240px;border-radius:10px"></p>
        <?php endif; ?>
        <label>Cover image
          <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp">
        </label>
        <button class="btn" type="submit">Save landing page</button>
        <a href="../blog.php" target="_blank" rel="noopener">View blog</a>
      </fieldset>
    </form>

    <h2>Articles</h2>
    <p class="hint">Each article is its own URL with two image + text sections and full SEO fields.</p>
    <?php if (!$posts): ?>
      <p>No articles yet. <a href="blog-edit.php">Write the first one</a>.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Title</th>
            <th>Slug</th>
            <th>SEO</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $row): ?>
            <tr>
              <td><?= htmlspecialchars((string) $row['title']) ?></td>
              <td>
                <code><?= htmlspecialchars((string) $row['slug']) ?></code><br>
                <a href="<?= htmlspecialchars(sabBlogUrl((string) $row['slug'])) ?>" target="_blank" rel="noopener">View</a>
              </td>
              <td>
                <?= $row['meta_title'] ? 'Title' : 'No meta title' ?><br>
                <?= $row['meta_description'] ? 'Description' : 'No meta description' ?>
              </td>
              <td><?= !empty($row['is_published']) ? 'Published' : 'Draft' ?></td>
              <td><a href="blog-edit.php?id=<?= (int) $row['id'] ?>">Edit</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>
</html>
