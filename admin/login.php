<?php
require_once __DIR__ . '/../includes/admin-auth.php';

if (!empty($_SESSION['sab_admin'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = (string) ($_POST['username'] ?? '');
    $pass = (string) ($_POST['password'] ?? '');
    if ($user === SAB_ADMIN_USERNAME && password_verify($pass, SAB_ADMIN_PASSWORD_HASH)) {
        $_SESSION['sab_admin'] = true;
        header('Location: index.php');
        exit;
    }
    $error = 'Incorrect username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Sadaf Al Bahr</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="wrap">
    <div class="box">
      <h1>Sadaf Al Bahr admin</h1>
      <p class="hint">After you log in, open <strong>Guide</strong> in the top menu. It explains catalogues, blog, photos, and SEO.</p>
      <?php if ($error): ?><p class="err"><?= htmlspecialchars($error) ?></p><?php endif; ?>
      <form method="post">
        <label>Username <input name="username" autocomplete="username" required></label>
        <label>Password <input type="password" name="password" autocomplete="current-password" required></label>
        <button class="btn" type="submit">Log in</button>
      </form>
    </div>
  </div>
</body>
</html>
