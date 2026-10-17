<?php
$page_title = 'About Us | Sadaf Al Bahr Naif Road Dubai';
$page_description = 'About Sadaf Al Bahr — wallpaper, carpets and curtains with professional installation from Naif Road, Deira, Dubai.';
require __DIR__ . '/includes/header.php';
?>

<section class="page-banner banner-about">
  <div class="container">
    <p class="eyebrow"><?= SAB_NAME_AR ?></p>
    <h1>About Sadaf Al Bahr</h1>
    <p>A Naif Road house for wallpaper, carpets, curtains and the fixing that makes a room finished.</p>
  </div>
</section>

<section class="section">
  <div class="container split">
    <img src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1200&q=80" alt="Interior fabrics and furnishings">
    <div>
      <p class="eyebrow" style="color:var(--gold)">Who we are</p>
      <h2>Pearl of the sea — on Deira’s fabric street.</h2>
      <p>Sadaf Al Bahr (صدف البحر) supplies wallpaper, carpets and curtains from the ground floor of Dr. Ibrahim Al Marzouqi Building 187, Naif Road, Al Murar, Deira. Customers come for samples, then we measure, make and install in homes, offices and shops across Dubai.</p>
      <p class="muted">The work is practical: pattern books you can touch, fabric you can drape, carpet pile you can feel — and a team that hangs, pastes and lays so you are not left with a roll and a drill.</p>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" style="color:var(--gold)">Experience</p>
      <h2>Why customers choose this shop.</h2>
    </div>
    <div class="feature-grid">
      <div class="feature">
        <strong>One address for three finishes</strong>
        Wallpaper, carpet and curtains together, so colours and textures can be chosen in the same visit.
      </div>
      <div class="feature">
        <strong>Supply plus installation</strong>
        We stitch curtains, paste wallpaper and lay carpet. Fixing is part of the job, not an extra mystery.
      </div>
      <div class="feature">
        <strong>Naif Road showroom</strong>
        Ground-floor access on a street people already use for fabrics. Walk in with a photo or a tape measure.
      </div>
      <div class="feature">
        <strong>Homes and commercial</strong>
        Apartments and villas, as well as offices and shops that need durable carpet and clean window dressing.
      </div>
    </div>
  </div>
</section>

<section class="section cta-band" style="padding:56px 0">
  <div class="container">
    <div>
      <h2 style="color:#fff;margin:0 0 8px">Come to the shop, or send sizes on WhatsApp.</h2>
      <p><?= e(SAB_ADDRESS) ?></p>
    </div>
    <a class="btn btn-gold" href="<?= e(sabPageHref('contact')) ?>">Contact Us</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
