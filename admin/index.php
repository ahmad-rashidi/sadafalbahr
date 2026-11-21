<?php
require_once __DIR__ . '/../includes/admin-auth.php';
require_once __DIR__ . '/../includes/db.php';
sab_require_admin();

if (isset($_GET['read'])) {
    $id = (int) $_GET['read'];
    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE inquiries SET is_read = 1 WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
    header('Location: index.php');
    exit;
}

$rows = $pdo->query('SELECT * FROM inquiries ORDER BY created_at DESC, id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inquiries | Sadaf Al Bahr</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="wrap wrap-wide">
    <?php include __DIR__ . '/_nav.php'; ?>
    <div class="top">
      <h1>Inquiries</h1>
    </div>
    <div class="welcome">
      <p><strong>New here?</strong> Open the <a href="guide.php">Guide</a> first. That page shows how to add catalogues, blog posts, project photos, and SEO. Then come back here to answer customer messages.</p>
    </div>
    <?php if (!$rows): ?>
      <p>No messages yet.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>When</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Service</th>
            <th>Message</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <tr class="<?= empty($row['is_read']) ? 'unread' : '' ?>">
              <td><?= htmlspecialchars((string) $row['created_at']) ?></td>
              <td><?= htmlspecialchars((string) $row['name']) ?></td>
              <td><?= htmlspecialchars((string) $row['phone']) ?></td>
              <td><?= htmlspecialchars((string) $row['service']) ?></td>
              <td><?= nl2br(htmlspecialchars((string) $row['message'])) ?></td>
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
