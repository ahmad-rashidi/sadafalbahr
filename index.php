<?php
$page_title = 'Sadaf Al Bahr | Wallpaper, Carpets & Curtains on Naif Road, Dubai';
$page_description = 'Sadaf Al Bahr — wallpaper, carpets, curtains with fixing, and interior decoration from Naif Road, Al Murar, Deira, Dubai.';
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container hero-inner">
    <p class="eyebrow">Naif Road · Deira · Dubai</p>
    <h1>Rooms finished with fabric, floor and wall.</h1>
    <p>Wallpaper, carpets, curtains with professional fixing, and decoration — measured, made and installed from our shop on Naif Road.</p>
    <div class="hero-actions">
      <a class="btn btn-gold" href="<?= e(sabPageHref('contact')) ?>">Ask for a visit</a>
      <a class="btn btn-ghost" href="<?= e(sabPageHref('catalog')) ?>">Browse the catalogue</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" style="color:var(--gold)">What we do</p>
      <h2>One shop for walls, floors, windows and décor.</h2>
      <p class="muted">Come to Naif Road for samples, or call us to measure at your home, office or hotel.</p>
    </div>
    <div class="service-grid">
      <article class="card">
        <img src="https://images.unsplash.com/photo-1615874959474-d609969a20d7?auto=format&fit=crop&w=900&q=80" alt="Textured wallpaper in a living room">
        <div class="card-body">
          <h3>Wallpaper</h3>
          <p class="muted">Vinyl, fabric, 3D and decorative wall coverings, supplied and installed with clean finishing.</p>
          <a class="more" href="<?= e(sabPageHref('wallpaper')) ?>">Wallpaper details</a>
        </div>
      </article>
      <article class="card">
        <img src="https://images.unsplash.com/photo-1600166898405-da9535204843?auto=format&fit=crop&w=900&q=80" alt="Layered carpets in a styled interior">
        <div class="card-body">
          <h3>Carpets</h3>
          <p class="muted">Wall-to-wall carpet, rugs, SPC and PVC flooring, plus artificial grass for indoor and outdoor use.</p>
          <a class="more" href="<?= e(sabPageHref('carpets')) ?>">Carpet details</a>
        </div>
      </article>
      <article class="card">
        <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=900&q=80" alt="Floor-length curtains at a window">
        <div class="card-body">
          <h3>Curtains &amp; blinds</h3>
          <p class="muted">Blackout, sheer, motorized and classic curtains — stitched, hung and adjusted on site.</p>
          <a class="more" href="<?= e(sabPageHref('curtains')) ?>">Curtain details</a>
        </div>
      </article>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container split">
    <img src="https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1200&q=80" alt="Decorated living room with curtains and soft furnishings">
    <div>
      <p class="eyebrow" style="color:var(--gold)">Fixing &amp; decoration</p>
      <h2>We do not only sell. We measure, sew, hang and finish.</h2>
      <p class="muted">Professional fixing is part of every job: tracks, rods, wallpaper paste, carpet stretching and neat edges. We also advise on colours, fabrics and decoration so the whole room holds together.</p>
      <p><a class="btn btn-outline" href="<?= e(sabPageHref('about')) ?>">About the shop</a></p>
    </div>
  </div>
</section>

<section class="section" style="background:#efe6d6">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" style="color:var(--gold)">How it works</p>
      <h2>From Naif Road to your doorway.</h2>
    </div>
    <div class="steps">
      <div>
        <div class="step-num">01</div>
        <h3 class="serif">Visit or call</h3>
        <p class="muted">See samples in the shop on Naif Road, or send photos on WhatsApp.</p>
      </div>
      <div>
        <div class="step-num">02</div>
        <h3 class="serif">Measure</h3>
        <p class="muted">We visit your space, take sizes, and confirm fabric, wallpaper or carpet.</p>
      </div>
      <div>
        <div class="step-num">03</div>
        <h3 class="serif">Make</h3>
        <p class="muted">Curtains are stitched to size. Wallpaper and flooring are prepared for a clean install.</p>
      </div>
      <div>
        <div class="step-num">04</div>
        <h3 class="serif">Fix</h3>
        <p class="muted">Our team hangs, pastes, stretches and finishes — ready to live in.</p>
      </div>
    </div>
  </div>
</section>

<section class="section cta-band">
  <div class="container">
    <div>
      <p class="eyebrow">Sadaf Al Bahr · صدف البحر</p>
      <h2 style="color:#fff;margin:0 0 8px">Bring your room measurements. Leave with a plan.</h2>
      <p>Ground floor, Naif Road, Al Murar, Deira. Open daily.</p>
    </div>
    <a class="btn btn-gold" href="<?= e(sabWhatsApp()) ?>" target="_blank" rel="noopener">WhatsApp <?= e(SAB_PHONE_DISPLAY) ?></a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
