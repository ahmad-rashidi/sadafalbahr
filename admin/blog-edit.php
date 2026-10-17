<?php
require_once __DIR__ . '/../includes/admin-auth.php';
require_once __DIR__ . '/../includes/db.php';
sab_require_admin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$post = [
    'id' => 0,
    'title' => '',
    'slug' => '',
    'excerpt' => '',
    'cover_image' => '',
    'cover_alt' => '',
    'category' => 'Guides',
    'meta_title' => '',
    'meta_description' => '',
    'focus_keyword' => '',
    'is_published' => 1,
    'published_at' => date('Y-m-d'),
];
$sections = [
    ['id' => 0, 'heading' => '', 'body' => '', 'image_path' => '', 'image_alt' => '', 'sort_order' => 1],
    ['id' => 0, 'heading' => '', 'body' => '', 'image_path' => '', 'image_alt' => '', 'sort_order' => 2],
];
if ($id > 0) {
    $found = sab_post_by_id($pdo, $id);
    if (!$found) {
        header('Location: blog.php');
        exit;
    }
    $post = $found;
    $got = sab_ensure_two_sections($pdo, $id);
    if ($got) {
        $sections = array_pad($got, 2, $sections[1]);
        $sections = array_slice($sections, 0, 2);
    }
}
$error = (string) ($_GET['error'] ?? '');
$saved = isset($_GET['saved']);
$categories = ['Guides', 'Wallpaper', 'Curtains', 'Carpets', 'Installation'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $id ? 'Edit article' : 'Add article' ?> | Admin</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="wrap wrap-wide">
    <?php include __DIR__ . '/_nav.php'; ?>
    <div class="top">
      <h1><?= $id ? 'Edit article' : 'Add article' ?></h1>
      <a href="blog.php">Back to blog</a>
    </div>
    <?php if ($saved): ?><p class="ok">Saved. The article page uses two image sections and the SEO fields below.</p><?php endif; ?>
    <?php if ($error): ?><p class="err"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form class="admin-form" method="post" action="blog-save.php" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars(sab_admin_csrf()) ?>">
      <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">

      <fieldset>
        <legend>Article</legend>
        <label>Title (H1)
          <input name="title" required maxlength="180" value="<?= htmlspecialchars((string) $post['title']) ?>">
        </label>
        <label>Category
          <select name="category">
            <?php foreach ($categories as $cat): ?>
              <option <?= $post['category'] === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Short excerpt (for the blog list and Google if meta description is empty)
          <textarea name="excerpt" rows="2" maxlength="320"><?= htmlspecialchars((string) $post['excerpt']) ?></textarea>
        </label>
        <label>Cover image alt
          <input name="cover_alt" maxlength="180" value="<?= htmlspecialchars((string) $post['cover_alt']) ?>">
        </label>
        <?php if ($post['cover_image']): ?>
          <p><img src="../<?= htmlspecialchars((string) $post['cover_image']) ?>" alt="" style="max-width:240px;border-radius:10px"></p>
        <?php endif; ?>
        <label>Cover image
          <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp">
        </label>
        <label>Publish date
          <input type="date" name="published_at" value="<?= htmlspecialchars((string) ($post['published_at'] ?: date('Y-m-d'))) ?>">
        </label>
        <label class="check">
          <input type="checkbox" name="is_published" value="1" <?= !empty($post['is_published']) ? 'checked' : '' ?>>
          Published
        </label>
      </fieldset>

      <fieldset>
        <legend>SEO</legend>
        <label>URL slug
          <input name="slug" maxlength="180" value="<?= htmlspecialchars((string) $post['slug']) ?>" placeholder="how-to-choose-blackout-curtains-dubai">
          <span class="hint">Public URL: /blog/your-slug</span>
        </label>
        <label>Meta title
          <input name="meta_title" maxlength="180" value="<?= htmlspecialchars((string) $post['meta_title']) ?>" placeholder="How to Choose Blackout Curtains in Dubai | Sadaf Al Bahr">
        </label>
        <label>Meta description
          <textarea name="meta_description" rows="3" maxlength="320"><?= htmlspecialchars((string) $post['meta_description']) ?></textarea>
          <span class="hint">About 140–160 characters. Include Dubai / Naif Road and the service if it fits.</span>
        </label>
        <label>Focus keyword
          <input name="focus_keyword" maxlength="120" value="<?= htmlspecialchars((string) $post['focus_keyword']) ?>">
        </label>
      </fieldset>

      <?php foreach ([1, 2] as $n): ?>
        <?php $sec = $sections[$n - 1]; ?>
        <fieldset>
          <legend>Section <?= $n ?> — image + text</legend>
          <input type="hidden" name="section_id[<?= $n ?>]" value="<?= (int) $sec['id'] ?>">
          <label>Section heading
            <input name="section_heading[<?= $n ?>]" maxlength="180" value="<?= htmlspecialchars((string) $sec['heading']) ?>">
          </label>
          <label>Section text
            <textarea name="section_body[<?= $n ?>]" rows="6"><?= htmlspecialchars((string) $sec['body']) ?></textarea>
          </label>
          <label>Image alt text
            <input name="section_alt[<?= $n ?>]" maxlength="180" value="<?= htmlspecialchars((string) $sec['image_alt']) ?>">
          </label>
          <?php if ($sec['image_path']): ?>
            <p><img src="../<?= htmlspecialchars((string) $sec['image_path']) ?>" alt="" style="max-width:240px;border-radius:10px"></p>
          <?php endif; ?>
          <label>Section image
            <input type="file" name="section_image_<?= $n ?>" accept="image/jpeg,image/png,image/webp">
          </label>
        </fieldset>
      <?php endforeach; ?>

      <button class="btn" type="submit">Save article</button>
      <?php if ($id): ?>
        <button class="btn btn-danger" type="submit" name="delete_post" value="1" onclick="return confirm('Delete this article?')">Delete</button>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>
