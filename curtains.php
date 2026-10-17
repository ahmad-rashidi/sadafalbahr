<?php
$page_title = 'Curtains | Styles, Fabrics, Blackout & Sheer | Sadaf Al Bahr';
$page_description = 'Custom curtains in Dubai from Sadaf Al Bahr — blackout, sheer, fabrics, styles, measurements and professional installation on Naif Road.';
require __DIR__ . '/includes/header.php';

$styles = [
    ['Blackout curtains', 'Sleep, cinema rooms and street-facing windows. Heavy lining to cut light and heat.', 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=900&q=80'],
    ['Sheer curtains', 'Soft daylight with privacy. Layered over blackout or on their own.', 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=900&q=80'],
    ['Classic pinch pleat', 'Formal folds for majlis, dining rooms and hotel-style windows.', 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=900&q=80'],
    ['Eyelet & wave', 'Modern drop, easy to draw. Good for apartments and offices.', 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=900&q=80'],
    ['Blinds', 'Roller, vertical and fabric blinds where a full curtain stack is not wanted.', 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=900&q=80'],
    ['Upholstery fabrics', 'Matching or coordinating fabrics for cushions and furniture from the same books.', 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=900&q=80'],
];
?>

<section class="page-banner banner-curtains">
  <div class="container">
    <p class="eyebrow">Curtains</p>
    <h1>Styles, fabrics, blackout and sheer — stitched to your window.</h1>
    <p>Custom measurements from our team, sewing to size, then tracks, rods and hanging on site.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" style="color:var(--gold)">Curtain styles &amp; fabrics</p>
      <h2>Choose the drop, then the cloth.</h2>
      <p class="muted">Books of curtain fabric are in the Naif Road shop: plains, textures, embroidered, blackout-ready and light sheers.</p>
    </div>
    <div class="catalog-grid">
      <?php foreach ($styles as [$title, $text, $img]): ?>
        <article class="card">
          <img src="<?= e($img) ?>" alt="<?= e($title) ?>">
          <div class="card-body">
            <h3><?= e($title) ?></h3>
            <p class="muted"><?= e($text) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" style="background:#efe6d6">
  <div class="container">
    <div class="section-head">
      <h2>Custom measurements and installation</h2>
      <p class="muted">Windows in Dubai vary: bay, sliding, high ceilings, plaster pelmets. We measure, confirm fullness, then hang so the hem sits even.</p>
    </div>
    <div class="steps">
      <div>
        <div class="step-num">01</div>
        <h3 class="serif">Measure</h3>
        <p class="muted">Width, drop, stack-back and how you want them to close.</p>
      </div>
      <div>
        <div class="step-num">02</div>
        <h3 class="serif">Fabric</h3>
        <p class="muted">Blackout, sheer or both — with lining if the sun is strong.</p>
      </div>
      <div>
        <div class="step-num">03</div>
        <h3 class="serif">Stitch</h3>
        <p class="muted">Sewn to the sizes we took, not off-the-shelf lengths.</p>
      </div>
      <div>
        <div class="step-num">04</div>
        <h3 class="serif">Install</h3>
        <p class="muted">Tracks or rods, hanging, dressing and small adjustments.</p>
      </div>
    </div>
    <p style="margin-top:32px">
      <a class="btn btn-gold" href="<?= e(sabPageHref('contact') . '?service=' . rawurlencode('Curtains & blinds')) ?>">Request a curtain quote</a>
    </p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
