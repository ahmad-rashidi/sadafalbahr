<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/inquiry.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
if ($slug === '' && preg_match('#/catalog/([a-z0-9-]+)#i', (string) ($_SERVER['REQUEST_URI'] ?? ''), $m)) {
    $slug = strtolower($m[1]);
}

$item = $slug !== '' ? sab_catalog_by_slug($pdo, $slug) : null;
$cats = sab_catalog_categories();
$nav_current = 'catalog.php';
$sent = isset($_GET['sent']);

if (!$item && !$sent) {
    http_response_code(404);
    $page_title = 'Order not available | ' . SAB_NAME;
    $robots = 'noindex,follow';
    require __DIR__ . '/includes/header.php';
    echo '<section class="page-hero"><div class="container"><h1>Design not found</h1><p class="muted"><a href="' . e(sabPageHref('catalog')) . '">Back to the catalogue</a>.</p></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$images = $item ? sab_catalog_images($pdo, (int) $item['id']) : [];
$pickId = (int) ($_POST['image_id'] ?? $_GET['pick'] ?? 0);
$picked = ($item && $pickId > 0) ? sab_catalog_image_by_id($pdo, $pickId, (int) $item['id']) : null;
if (!$picked && count($images) === 1) {
    $picked = $images[0];
    $pickId = (int) $picked['id'];
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $item) {
    $result = sab_save_catalog_order($pdo, $item, $_POST);
    if ($result['ok']) {
        $next = sabCatalogOrderUrl((string) $item['slug'], (int) ($_POST['image_id'] ?? 0));
        $next .= str_contains($next, '?') ? '&sent=1' : '?sent=1';
        header('Location: ' . $next);
        exit;
    }
    $errors = $result['errors'];
}

$csrf = sab_csrf_token();
$area = $item ? sab_price_is_area((string) $item['price_unit']) : true;
$catLabel = $item ? ($cats[$item['category']] ?? 'Design') : 'Catalogue';
$measure = (string) ($_POST['measure_unit'] ?? 'metre');
if ($measure !== 'inch') {
    $measure = 'metre';
}

$robots = 'noindex,follow';
$patternLabel = $picked && $item ? sab_pattern_label($picked, $item) : '';
$page_title = $item
    ? ('Order ' . ($patternLabel ?: $item['design_name']) . ' | ' . SAB_NAME)
    : ('Order received | ' . SAB_NAME);
$page_description = 'Send width, height and contact details for a custom catalogue order at Sadaf Al Bahr, Naif Road, Dubai.';
$skip_page_seo = true;

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <p class="eyebrow">Make your order</p>
    <?php if ($sent): ?>
      <h1>We have your order request.</h1>
      <p class="muted">Thank you. We will call or WhatsApp to confirm the selected design, size, stock and the final price.</p>
    <?php else: ?>
      <h1><?= e($patternLabel ?: (string) $item['design_name']) ?></h1>
      <p class="muted"><?= e($catLabel) ?><?= $item['company_name'] ? ' · ' . e((string) $item['company_name']) : '' ?> · <?= e(sab_catalog_format_price($item)) ?></p>
    <?php endif; ?>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container" style="max-width:720px">
    <?php if ($sent): ?>
      <p>
        <a class="btn btn-gold" href="<?= e(sabPageHref('catalog')) ?>">Back to catalogue</a>
        <a class="btn btn-outline" href="<?= e(sabWhatsApp('Hello Sadaf Al Bahr, I just sent a catalogue order.')) ?>" target="_blank" rel="noopener">WhatsApp us</a>
      </p>
    <?php elseif ($images && !$picked): ?>
      <div class="alert alert-error">Please select a design from this catalogue first.</div>
      <p><a class="btn btn-gold" href="<?= e(sabCatalogUrl((string) $item['slug'])) ?>">Choose a design</a></p>
    <?php else: ?>
      <?php if ($errors): ?>
        <div class="alert alert-error"><?= e(implode(' ', $errors)) ?></div>
      <?php endif; ?>
      <?php if ($picked): ?>
        <div class="order-pick">
          <img src="<?= e(sabMediaUrl((string) $picked['image_path'])) ?>" alt="<?= e($patternLabel) ?>">
          <div>
            <p class="catalog-company"><?= e((string) $item['company_name']) ?></p>
            <h2><?= e($patternLabel) ?></h2>
            <p class="muted"><?= e((string) $item['design_name']) ?> · <?= e($catLabel) ?></p>
            <p><a href="<?= e(sabCatalogUrl((string) $item['slug']) . '?pick=' . (int) $picked['id']) ?>">Change design</a></p>
          </div>
        </div>
      <?php endif; ?>
      <form class="form" method="post" action="<?= e(sabCatalogOrderUrl((string) $item['slug'], $pickId)) ?>" data-order-form
            data-price="<?= e((string) $item['price_amount']) ?>"
            data-unit="<?= e((string) $item['price_unit']) ?>"
            data-area="<?= $area ? '1' : '0' ?>">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <input type="hidden" name="image_id" value="<?= (int) $pickId ?>">
        <p class="muted" style="margin:0">Step 2 — sizes. The estimate is a guide only; we confirm on site.</p>

        <label>Measure in
          <select name="measure_unit" data-measure>
            <option value="metre" <?= $measure === 'metre' ? 'selected' : '' ?>>Metres</option>
            <option value="inch" <?= $measure === 'inch' ? 'selected' : '' ?>>Inches</option>
          </select>
        </label>

        <div class="form-row">
          <label><?= $area ? 'Width' : 'Length / width needed' ?>
            <input name="width" type="number" min="0.01" step="0.01" required value="<?= e((string) ($_POST['width'] ?? '')) ?>" data-width>
          </label>
          <label><?= $area ? 'Height / length' : 'Height / drop (optional)' ?>
            <input name="height" type="number" min="0" step="0.01" <?= $area ? 'required' : '' ?> value="<?= e((string) ($_POST['height'] ?? '')) ?>" data-height>
          </label>
        </div>

        <label>Quantity
          <input name="quantity" type="number" min="1" step="1" value="<?= e((string) ($_POST['quantity'] ?? '1')) ?>" data-qty>
          <span class="hint">Windows, rooms, or extra drops.</span>
        </label>

        <p class="catalog-estimate" data-estimate>
          Estimated total: <strong>—</strong>
          <span class="muted"> (<?= e(sab_catalog_format_price($item)) ?>)</span>
        </p>

        <label>Name
          <input name="name" required value="<?= e((string) ($_POST['name'] ?? '')) ?>">
        </label>
        <label>Phone / WhatsApp
          <input name="phone" required value="<?= e((string) ($_POST['phone'] ?? '')) ?>">
        </label>
        <label>Email (optional)
          <input name="email" type="email" value="<?= e((string) ($_POST['email'] ?? '')) ?>">
        </label>
        <label>Notes (colour, room, installation)
          <textarea name="notes"><?= e((string) ($_POST['notes'] ?? '')) ?></textarea>
        </label>
        <button class="btn btn-gold" type="submit">Send custom order</button>
        <p><a href="<?= e(sabCatalogUrl((string) $item['slug']) . ($pickId ? '?pick=' . $pickId : '')) ?>">Back to this catalogue</a></p>
      </form>
      <script>
        (function () {
          var form = document.querySelector('[data-order-form]');
          if (!form) return;
          var price = parseFloat(form.getAttribute('data-price') || '0');
          var unit = form.getAttribute('data-unit') || 'metre';
          var area = form.getAttribute('data-area') === '1';
          var out = form.querySelector('[data-estimate] strong');
          function toM(v, from) { return from === 'inch' ? v / 39.37007874 : v; }
          function toIn(v, from) { return from === 'metre' ? v * 39.37007874 : v; }
          function calc() {
            var from = form.querySelector('[data-measure]').value;
            var w = parseFloat(form.querySelector('[data-width]').value) || 0;
            var h = parseFloat(form.querySelector('[data-height]').value) || 0;
            var q = parseFloat(form.querySelector('[data-qty]').value) || 1;
            var total = null;
            if (price > 0 && w > 0 && q > 0) {
              if (area) {
                if (h > 0) {
                  var ww = unit === 'sqinch' ? toIn(w, from) : toM(w, from);
                  var hh = unit === 'sqinch' ? toIn(h, from) : toM(h, from);
                  total = ww * hh * price * q;
                }
              } else {
                var len = unit === 'inch' ? toIn(w, from) : toM(w, from);
                total = len * price * q;
              }
            }
            out.textContent = total === null ? '—' : ('AED ' + total.toFixed(2));
          }
          form.addEventListener('input', calc);
          form.addEventListener('change', calc);
          calc();
        })();
      </script>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
