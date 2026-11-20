<?php
require_once __DIR__ . '/../includes/admin-auth.php';
require_once __DIR__ . '/../includes/db.php';
sab_require_admin();

$cats = sab_catalog_categories();

if (isset($_GET['read'])) {
    $id = (int) $_GET['read'];
    if ($id > 0) {
        $pdo->prepare('UPDATE catalog_orders SET is_read = 1 WHERE id = :id')->execute([':id' => $id]);
    }
    header('Location: orders.php');
    exit;
}

$rows = $pdo->query('SELECT * FROM catalog_orders ORDER BY created_at DESC, id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Custom orders | Admin</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="wrap wrap-wide">
    <?php include __DIR__ . '/_nav.php'; ?>
    <div class="top">
      <h1>Custom orders</h1>
    </div>
    <p class="hint">Orders from the catalogue: design, size, and estimated price. Confirm on site before invoicing.</p>
    <?php if (!$rows): ?>
      <p>No catalogue orders yet.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>When</th>
            <th>Customer</th>
            <th>Design</th>
            <th>Size</th>
            <th>Estimate</th>
            <th>Notes</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <tr class="<?= empty($row['is_read']) ? 'unread' : '' ?>">
              <td><?= htmlspecialchars((string) $row['created_at']) ?></td>
              <td>
                <?= htmlspecialchars((string) $row['name']) ?><br>
                <?= htmlspecialchars((string) $row['phone']) ?>
                <?php if ($row['email']): ?><br><?= htmlspecialchars((string) $row['email']) ?><?php endif; ?>
              </td>
              <td>
                <?= htmlspecialchars($cats[$row['category']] ?? (string) $row['category']) ?><br>
                <?= htmlspecialchars((string) $row['company_name']) ?>
                <?= $row['company_name'] ? '<br>' : '' ?>
                <?= htmlspecialchars((string) $row['design_name']) ?>
                <?php if (!empty($row['pattern_name'])): ?>
                  <br><strong><?= htmlspecialchars((string) $row['pattern_name']) ?></strong>
                <?php endif; ?>
                <?php if (!empty($row['image_path'])): ?>
                  <br><img src="<?= htmlspecialchars(sabMediaUrl((string) $row['image_path'])) ?>" alt="" style="width:72px;height:72px;object-fit:cover;border-radius:8px;margin-top:6px">
                <?php endif; ?>
              </td>
              <td>
                <?= htmlspecialchars((string) $row['width_value']) ?>
                <?= $row['height_value'] > 0 ? ' × ' . htmlspecialchars((string) $row['height_value']) : '' ?>
                <?= htmlspecialchars((string) $row['measure_unit']) ?>
                <?= (float) $row['quantity'] !== 1.0 ? '<br>× ' . htmlspecialchars((string) $row['quantity']) : '' ?>
              </td>
              <td>
                <?= $row['estimate_aed'] !== null ? htmlspecialchars(sab_format_aed((float) $row['estimate_aed'])) : '—' ?><br>
                <span class="hint"><?= htmlspecialchars(sab_format_aed((float) $row['price_amount']) . ' ' . sab_price_unit_label((string) $row['price_unit'])) ?></span>
              </td>
              <td><?= nl2br(htmlspecialchars((string) $row['notes'])) ?></td>
              <td>
                <?php if (empty($row['is_read'])): ?>
                  <a href="?read=<?= (int) $row['id'] ?>">Mark read</a>
                <?php else: ?>
                  Read
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>
</html>
