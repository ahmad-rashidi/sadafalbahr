<?php
require_once __DIR__ . '/../includes/admin-auth.php';
require_once __DIR__ . '/../includes/db.php';
sab_require_admin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$project = [
    'id' => 0,
    'title' => '',
    'slug' => '',
    'category' => 'Installation',
    'location' => '',
    'summary' => '',
    'description' => '',
    'meta_title' => '',
    'meta_description' => '',
    'focus_keyword' => '',
    'is_published' => 1,
    'sort_order' => 0,
];
$images = [];
if ($id > 0) {
    $found = sab_project_by_id($pdo, $id);
    if (!$found) {
        header('Location: projects.php');
        exit;
    }
    $project = $found;
    $images = sab_project_images($pdo, $id);
}
$error = (string) ($_GET['error'] ?? '');
$saved = isset($_GET['saved']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $id ? 'Edit project' : 'Add project' ?> | Admin</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="wrap wrap-wide">
    <?php include __DIR__ . '/_nav.php'; ?>
    <div class="top">
      <h1><?= $id ? 'Edit project' : 'Add project' ?></h1>
      <a href="projects.php">Back to list</a>
    </div>
    <?php if ($saved): ?><p class="ok">Saved. The public page is ready with the SEO fields below.</p><?php endif; ?>
    <?php if ($error): ?><p class="err"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form class="admin-form" method="post" action="project-save.php" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars(sab_admin_csrf()) ?>">
      <input type="hidden" name="id" value="<?= (int) $project['id'] ?>">

      <fieldset>
        <legend>Project content</legend>
        <label>Title (H1 on the page)
          <input name="title" required maxlength="180" value="<?= htmlspecialchars((string) $project['title']) ?>" placeholder="e.g. Blackout curtains in a Deira apartment">
        </label>
        <label>Category
          <select name="category">
            <?php foreach (SAB_PROJECT_CATEGORIES as $cat): ?>
              <option <?= $project['category'] === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Location (helps local Google searches)
          <input name="location" maxlength="160" value="<?= htmlspecialchars((string) $project['location']) ?>" placeholder="e.g. Naif, Deira, Dubai">
        </label>
        <label>Short summary (gallery card)
          <input name="summary" maxlength="300" value="<?= htmlspecialchars((string) $project['summary']) ?>" placeholder="One or two sentences for the projects list">
        </label>
        <label>Full description
          <textarea name="description" rows="8" required><?= htmlspecialchars((string) $project['description']) ?></textarea>
        </label>
        <label>Sort order
          <input type="number" name="sort_order" value="<?= (int) $project['sort_order'] ?>">
        </label>
        <label class="check">
          <input type="checkbox" name="is_published" value="1" <?= !empty($project['is_published']) ? 'checked' : '' ?>>
          Published (visible on the website and sitemap)
        </label>
      </fieldset>

      <fieldset>
        <legend>SEO — how Google sees this page</legend>
        <label>URL slug
          <input name="slug" maxlength="180" value="<?= htmlspecialchars((string) $project['slug']) ?>" placeholder="blackout-curtains-deira-apartment">
          <span class="hint">Letters, numbers and dashes only. Leave blank to generate from the title. Public URL: /project/your-slug</span>
        </label>
        <label>Meta title
          <input name="meta_title" maxlength="180" value="<?= htmlspecialchars((string) $project['meta_title']) ?>" placeholder="Blackout Curtains in Deira | Sadaf Al Bahr Dubai">
          <span class="hint">Browser tab and Google headline. About 50–60 characters. Falls back to the project title.</span>
        </label>
        <label>Meta description
          <textarea name="meta_description" rows="3" maxlength="320"><?= htmlspecialchars((string) $project['meta_description']) ?></textarea>
          <span class="hint">Google snippet. About 140–160 characters. Mention wallpaper, curtains or carpets plus Dubai / Naif Road if it fits.</span>
        </label>
        <label>Focus keyword
          <input name="focus_keyword" maxlength="120" value="<?= htmlspecialchars((string) $project['focus_keyword']) ?>" placeholder="e.g. curtain installation Deira">
        </label>
      </fieldset>

      <fieldset>
        <legend>Photos</legend>
        <?php if ($images): ?>
          <div class="image-grid">
            <?php foreach ($images as $img): ?>
              <div class="image-card">
                <img src="../<?= htmlspecialchars((string) $img['image_path']) ?>" alt="">
                <label>Alt text (SEO)
                  <input name="alt[<?= (int) $img['id'] ?>]" value="<?= htmlspecialchars((string) $img['alt_text']) ?>">
                </label>
                <label>Caption
                  <input name="caption[<?= (int) $img['id'] ?>]" value="<?= htmlspecialchars((string) $img['caption']) ?>">
                </label>
                <label>Order
                  <input type="number" name="img_order[<?= (int) $img['id'] ?>]" value="<?= (int) $img['sort_order'] ?>">
                </label>
                <label class="check">
                  <input type="checkbox" name="delete_image[]" value="<?= (int) $img['id'] ?>">
                  Delete this photo
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <label>Add more photos
          <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
          <span class="hint">JPG, PNG or WebP, up to 8 MB each. Alt text defaults to the project title; you can edit it after saving.</span>
        </label>
      </fieldset>

      <button class="btn" type="submit">Save project</button>
      <?php if ($id): ?>
        <button class="btn btn-danger" type="submit" name="delete_project" value="1" onclick="return confirm('Delete this project and its photos?')">Delete project</button>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>
