<?php
$page_title = 'Carpets | Types, Colours, Residential & Commercial | Sadaf Al Bahr';
$page_description = 'Carpet supply and installation in Dubai from Sadaf Al Bahr — types, colours, residential and commercial flooring on Naif Road.';
require __DIR__ . '/includes/header.php';

$types = [
    ['Wall-to-wall carpet', 'Rooms, stairs and corridors with underlay and neat edges at the door.', 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=900&q=80'],
    ['Area rugs', 'Sized rugs for living rooms and bedrooms, cut and bound when needed.', 'https://images.unsplash.com/photo-1600166898405-da9535204843?auto=format&fit=crop&w=900&q=80'],
    ['Residential colours', 'Neutrals, warm sand, deep blues and patterned pieces for homes.', 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=900&q=80'],
    ['Commercial carpet', 'Offices, shops and hotels — denser pile and practical colours that hide traffic.', 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=900&q=80'],
    ['SPC & PVC flooring', 'Hard-wearing click or glue-down floors where carpet is not the right finish.', 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=900&q=80'],
    ['Artificial grass', 'Indoor play areas, balconies and outdoor edges from the same shop.', 'https://images.unsplash.com/photo-1615874959474-d609969a20d7?auto=format&fit=crop&w=900&q=80'],
];
?>

<section class="page-banner banner-carpets">
  <div class="container">
    <p class="eyebrow">Carpets</p>
    <h1>Types, colours, homes and workplaces — supplied and laid.</h1>
    <p>From the Naif Road shop we supply carpet, rugs and flooring, then cut, stretch and finish on site.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" style="color:var(--gold)">Carpet types &amp; colours</p>
      <h2>Feel the pile before you decide.</h2>
      <p class="muted">Samples are in the shop. We help match colour to wallpaper, curtains and existing furniture so the floor does not fight the room.</p>
    </div>
    <div class="catalog-grid">
      <?php foreach ($types as [$title, $text, $img]): ?>
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

<section class="section" style="padding-top:0">
  <div class="container split">
    <img src="https://images.unsplash.com/photo-1600166898405-da9535204843?auto=format&fit=crop&w=1200&q=80" alt="Carpeted living space">
    <div>
      <p class="eyebrow" style="color:var(--gold)">Residential &amp; commercial</p>
      <h2>Apartments, villas, offices and shops.</h2>
      <p class="muted">Home jobs need comfort and colour. Commercial jobs need durability and a clean install around desks, counters and corridors. We supply both, with the same measuring and laying team.</p>
      <ul class="muted">
        <li>Accurate measure so you do not over-buy</li>
        <li>Underlay, joining and door bars</li>
        <li>Installation included — stretching, cutting and finishing</li>
      </ul>
      <p>
        <a class="btn btn-gold" href="<?= e(sabPageHref('contact') . '?service=' . rawurlencode('Carpets & flooring')) ?>">Request a carpet quote</a>
      </p>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
