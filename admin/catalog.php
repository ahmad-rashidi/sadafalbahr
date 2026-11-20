<?php
require_once __DIR__ . '/../includes/admin-auth.php';
require_once __DIR__ . '/../includes/db.php';
sab_require_admin();

$rows = sab_catalog_admin_list($pdo);
$cats = sab_catalog_categories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Catalogue | Admin</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="wrap wrap-wide">
    <?php include __DIR__ . '/_nav.php'; ?>
    <div class="top">
      <h1>Catalogue</h1>
      <a class="btn" href="catalog-edit.php">Add another company catalogue</a>
    </div>
    <p class="hint">Add as many company books as you have. Each book is Wallpaper, Curtains or Carpets. Inside one book you can upload 40 (or more) design photos. Customers browse with arrows and order the one they pick.</p>
    <?php if (!$rows): ?>
      <p>No catalogues yet. <a href="catalog-edit.php">Add the first company book</a>.</p>
    <?php else: ?>
      <?php foreach ($cats as $key => $label): ?>
        <?php
        $group = array_values(array_filter($rows, static fn($row) => (string) $row['category'] === $key));
        if (!$group) {
            continue;
        }
        ?>
        <h2><?= htmlspecialchars($label) ?></h2>
        <table>
          <thead>
            <tr>
              <th>Catalogue / book</th>
              <th>Company</th>
              <th>Price</th>
              <th>Designs in this book</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($group as $row): ?>
              <tr>
                <td>
                  <?= htmlspecialchars((string) $row['design_name']) ?><br>
                  <a href="<?= htmlspecialchars(sabCatalogUrl((string) $row['slug'])) ?>" target="_blank" rel="noopener">View on site</a>
                </td>
                <td><?= htmlspecialchars((string) $row['company_name']) ?></td>
                <td><?= htmlspecialchars(sab_catalog_format_price($row)) ?></td>
                <td><?= (int) $row['image_count'] ?></td>
                <td><?= !empty($row['is_published']) ? 'Published' : 'Draft' ?></td>
                <td><a href="catalog-edit.php?id=<?= (int) $row['id'] ?>">Edit / add designs</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>
