<?php
require_once __DIR__ . '/../includes/admin-auth.php';
require_once __DIR__ . '/../includes/db.php';
sab_require_admin();

$rows = $pdo->query(
    'SELECT p.*,
            (SELECT COUNT(*) FROM project_images i WHERE i.project_id = p.id) AS image_count
     FROM projects p
     ORDER BY p.sort_order ASC, p.id DESC'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Projects | Admin</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="wrap wrap-wide">
    <?php include __DIR__ . '/_nav.php'; ?>
    <div class="top">
      <h1>Projects</h1>
      <a class="btn" href="project-edit.php">Add project</a>
    </div>
    <p class="hint">Each project gets its own page with a Google-friendly URL, title, slug and meta description. Add several photos and a description so people can find the work.</p>
    <?php if (!$rows): ?>
      <p>No projects yet. <a href="project-edit.php">Add the first one</a>.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Title</th>
            <th>Slug / URL</th>
            <th>Category</th>
            <th>Photos</th>
            <th>SEO</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <tr>
              <td><?= htmlspecialchars((string) $row['title']) ?></td>
              <td>
                <code><?= htmlspecialchars((string) $row['slug']) ?></code><br>
                <a href="<?= htmlspecialchars(sabProjectUrl((string) $row['slug'])) ?>" target="_blank" rel="noopener">View page</a>
              </td>
              <td><?= htmlspecialchars((string) $row['category']) ?></td>
              <td><?= (int) $row['image_count'] ?></td>
              <td>
                <?= $row['meta_title'] ? 'Title' : 'No meta title' ?><br>
                <?= $row['meta_description'] ? 'Description' : 'No meta description' ?>
              </td>
              <td><?= !empty($row['is_published']) ? 'Published' : 'Draft' ?></td>
              <td>
                <a href="project-edit.php?id=<?= (int) $row['id'] ?>">Edit</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>
</html>
