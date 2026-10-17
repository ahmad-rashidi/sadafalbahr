<?php
require_once __DIR__ . '/../includes/admin-auth.php';
require_once __DIR__ . '/../includes/db.php';
sab_require_admin();

$pages = sab_page_seo_all($pdo);
$projects = $pdo->query('SELECT id, title, slug, meta_title, meta_description, is_published FROM projects ORDER BY id DESC')->fetchAll();
$posts = $pdo->query('SELECT id, title, slug, meta_title, meta_description, is_published FROM blog_posts ORDER BY id DESC')->fetchAll();
$saved = isset($_GET['saved']);
$error = (string) ($_GET['error'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SEO | Admin</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="wrap wrap-wide">
    <?php include __DIR__ . '/_nav.php'; ?>
    <div class="top">
      <h1>SEO for all pages</h1>
      <a href="../sitemap.php" target="_blank" rel="noopener">Open sitemap</a>
    </div>
    <?php if ($saved): ?><p class="ok">SEO saved. Sitemap now uses these titles, slugs and URLs.</p><?php endif; ?>
    <?php if ($error): ?><p class="err"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <p class="hint">Set meta title, meta description, slug (pretty URL), keyword and robots for every main page. Google reads the sitemap at <code>/sitemap.php</code>.</p>

    <form class="admin-form" method="post" action="seo-save.php">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars(sab_admin_csrf()) ?>">
      <?php foreach ($pages as $row): ?>
        <fieldset>
          <legend><?= htmlspecialchars((string) $row['label']) ?></legend>
          <input type="hidden" name="page_key[]" value="<?= htmlspecialchars((string) $row['page_key']) ?>">
          <label>URL slug
            <input name="slug[<?= htmlspecialchars((string) $row['page_key']) ?>]" maxlength="180" value="<?= htmlspecialchars((string) $row['slug']) ?>" <?= $row['page_key'] === 'home' ? 'placeholder="(home has no slug)" disabled' : '' ?>>
            <?php if ($row['page_key'] === 'home'): ?>
              <input type="hidden" name="slug[home]" value="">
              <span class="hint">Home URL is the site root.</span>
            <?php else: ?>
              <span class="hint">Public URL: /<?= htmlspecialchars((string) $row['slug']) ?></span>
            <?php endif; ?>
          </label>
          <label>Meta title
            <input name="meta_title[<?= htmlspecialchars((string) $row['page_key']) ?>]" maxlength="180" value="<?= htmlspecialchars((string) $row['meta_title']) ?>">
          </label>
          <label>Meta description
            <textarea name="meta_description[<?= htmlspecialchars((string) $row['page_key']) ?>]" rows="3" maxlength="320"><?= htmlspecialchars((string) $row['meta_description']) ?></textarea>
          </label>
          <label>Focus keyword
            <input name="focus_keyword[<?= htmlspecialchars((string) $row['page_key']) ?>]" maxlength="120" value="<?= htmlspecialchars((string) $row['focus_keyword']) ?>">
          </label>
          <label>Robots
            <select name="robots[<?= htmlspecialchars((string) $row['page_key']) ?>]">
              <?php foreach (['index,follow', 'noindex,follow', 'index,nofollow', 'noindex,nofollow'] as $opt): ?>
                <option <?= $row['robots'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Open Graph image URL (optional)
            <input name="og_image[<?= htmlspecialchars((string) $row['page_key']) ?>]" value="<?= htmlspecialchars((string) $row['og_image']) ?>">
          </label>
          <label>Sitemap priority
            <input name="priority[<?= htmlspecialchars((string) $row['page_key']) ?>]" value="<?= htmlspecialchars((string) $row['priority']) ?>">
          </label>
          <label>Sitemap changefreq
            <select name="changefreq[<?= htmlspecialchars((string) $row['page_key']) ?>]">
              <?php foreach (['daily', 'weekly', 'monthly', 'yearly'] as $opt): ?>
                <option <?= $row['changefreq'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
              <?php endforeach; ?>
            </select>
          </label>
        </fieldset>
      <?php endforeach; ?>
      <button class="btn" type="submit">Save all page SEO</button>
    </form>

    <h2>Project pages</h2>
    <p class="hint">Each project already has its own slug, meta title and meta description. Edit them under Projects.</p>
    <?php if ($projects): ?>
      <table>
        <thead><tr><th>Title</th><th>Slug</th><th>Meta title</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($projects as $row): ?>
            <tr>
              <td><?= htmlspecialchars((string) $row['title']) ?></td>
              <td><code><?= htmlspecialchars((string) $row['slug']) ?></code></td>
              <td><?= htmlspecialchars((string) $row['meta_title']) ?></td>
              <td><a href="project-edit.php?id=<?= (int) $row['id'] ?>">Edit SEO</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <h2>Blog articles</h2>
    <p class="hint">Each article has slug, meta title and meta description. Edit them under Blog.</p>
    <?php if ($posts): ?>
      <table>
        <thead><tr><th>Title</th><th>Slug</th><th>Meta title</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($posts as $row): ?>
            <tr>
              <td><?= htmlspecialchars((string) $row['title']) ?></td>
              <td><code><?= htmlspecialchars((string) $row['slug']) ?></code></td>
              <td><?= htmlspecialchars((string) $row['meta_title']) ?></td>
              <td><a href="blog-edit.php?id=<?= (int) $row['id'] ?>">Edit SEO</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>
</html>
