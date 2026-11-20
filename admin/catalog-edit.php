<?php
require_once __DIR__ . '/../includes/admin-auth.php';
require_once __DIR__ . '/../includes/db.php';
sab_require_admin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$item = [
    'id' => 0,
    'category' => 'wallpaper',
    'company_name' => '',
    'design_name' => '',
    'slug' => '',
    'details' => '',
    'price_amount' => '0.00',
    'price_unit' => 'metre',
    'meta_title' => '',
    'meta_description' => '',
    'focus_keyword' => '',
    'is_published' => 1,
    'sort_order' => 0,
];
$images = [];
if ($id > 0) {
    $found = sab_catalog_by_id($pdo, $id);
    if (!$found) {
        header('Location: catalog.php');
        exit;
    }
    $item = $found;
    $images = sab_catalog_images($pdo, $id);
}
$error = (string) ($_GET['error'] ?? '');
$saved = isset($_GET['saved']);
$cats = sab_catalog_categories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $id ? 'Edit catalogue' : 'Add catalogue' ?> | Admin</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="wrap wrap-wide">
    <?php include __DIR__ . '/_nav.php'; ?>
    <div class="top">
      <h1><?= $id ? 'Edit catalogue' : 'Add catalogue' ?></h1>
      <a href="catalog.php">Back to catalogue</a>
    </div>
    <?php if ($saved): ?><p class="ok">Saved. Customers can open this book, select a design, and order.</p><?php endif; ?>
    <?php if ($error): ?><p class="err"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form class="admin-form" method="post" action="catalog-save.php" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars(sab_admin_csrf()) ?>">
      <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">

      <fieldset>
        <legend>Company catalogue</legend>
        <label>Section
          <select name="category">
            <?php foreach ($cats as $key => $label): ?>
              <option value="<?= htmlspecialchars($key) ?>" <?= $item['category'] === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Company name
          <input name="company_name" maxlength="160" value="<?= htmlspecialchars((string) $item['company_name']) ?>" placeholder="e.g. the supplier on the book">
        </label>
        <label>Catalogue / book title
          <input name="design_name" required maxlength="180" value="<?= htmlspecialchars((string) $item['design_name']) ?>" placeholder="e.g. Wallpaper Collection 2026">
        </label>
        <label>Details
          <textarea name="details" rows="7" required><?= htmlspecialchars((string) $item['details']) ?></textarea>
        </label>
        <label>Price (AED)
          <input type="number" name="price_amount" min="0" step="0.01" value="<?= htmlspecialchars((string) $item['price_amount']) ?>">
        </label>
        <label>Priced
          <select name="price_unit">
            <?php foreach (sab_price_units() as $key => $label): ?>
              <option value="<?= htmlspecialchars($key) ?>" <?= $item['price_unit'] === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
          </select>
          <span class="hint">Use per metre or per inch for fabric and wallpaper rolls. Use per m² or per sq. inch for carpets and wall area.</span>
        </label>
        <label>Sort order
          <input type="number" name="sort_order" value="<?= (int) $item['sort_order'] ?>">
        </label>
        <label class="check">
          <input type="checkbox" name="is_published" value="1" <?= !empty($item['is_published']) ? 'checked' : '' ?>>
          Published (visible on the website)
        </label>
      </fieldset>

      <fieldset>
        <legend>SEO</legend>
        <label>URL slug
          <input name="slug" maxlength="180" value="<?= htmlspecialchars((string) $item['slug']) ?>" placeholder="marble-grey-204">
          <span class="hint">Public URL: /catalog/your-slug</span>
        </label>
        <label>Meta title
          <input name="meta_title" maxlength="180" value="<?= htmlspecialchars((string) $item['meta_title']) ?>">
        </label>
        <label>Meta description
          <textarea name="meta_description" rows="3" maxlength="320"><?= htmlspecialchars((string) $item['meta_description']) ?></textarea>
        </label>
        <label>Focus keyword
          <input name="focus_keyword" maxlength="120" value="<?= htmlspecialchars((string) $item['focus_keyword']) ?>">
        </label>
      </fieldset>

      <fieldset>
        <legend>Designs in this book (<?= count($images) ?> so far — 40 is fine)</legend>
        <p class="hint">Upload every sample photo from this company catalogue. Customers will flip left and right through these designs, then order the one on screen. You can save, then come back and add more photos until you reach 40 or more.</p>
        <?php if ($images): ?>
          <div class="image-grid">
            <?php foreach ($images as $img): ?>
              <div class="image-card">
                <img src="<?= htmlspecialchars(sabMediaUrl((string) $img['image_path'])) ?>" alt="">
                <label>Design name / code
                  <input name="pattern_name[<?= (int) $img['id'] ?>]" value="<?= htmlspecialchars((string) ($img['design_name'] ?? '')) ?>" placeholder="e.g. 204 Grey Linen">
                </label>
                <label>Alt text
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
        <label>Add design photos (batch 1)
          <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
        </label>
        <label>Add more (batch 2)
          <input type="file" name="images_more[]" accept="image/jpeg,image/png,image/webp" multiple>
        </label>
        <label>Add more (batch 3)
          <input type="file" name="images_batch3[]" accept="image/jpeg,image/png,image/webp" multiple>
          <span class="hint">JPG, PNG or WebP, up to 8 MB each. A browser often allows about 20 files per box — use three boxes, or save and upload again, to reach 40 designs.</span>
        </label>
      </fieldset>

      <button class="btn" type="submit">Save catalogue</button>
      <?php if ($id): ?>
        <button class="btn btn-danger" type="submit" name="delete_item" value="1" onclick="return confirm('Delete this catalogue and its photos?')">Delete catalogue</button>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>
