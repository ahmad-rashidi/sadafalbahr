<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/inquiry.php';

$page_title = 'Contact Us | Sadaf Al Bahr Naif Road Dubai';
$page_description = 'Contact Sadaf Al Bahr on Naif Road, Deira — Google Maps, phone, WhatsApp, opening hours and a quote request form.';

$errors = [];
$sent = isset($_GET['sent']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = sab_save_inquiry($pdo, $_POST);
    if ($result['ok']) {
        header('Location: ' . sabPageHref('contact') . '?sent=1');
        exit;
    }
    $errors = $result['errors'];
}

$csrf = sab_csrf_token();
$picked = (string) ($_POST['service'] ?? $_GET['service'] ?? '');
require __DIR__ . '/includes/header.php';
?>

<section class="page-banner banner-contact">
  <div class="container">
    <p class="eyebrow">Contact Us</p>
    <h1>Naif Road, ground floor. Call, WhatsApp, or ask for a quote.</h1>
    <p>Al Murar, Deira — samples in the shop, measures at your place.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="contact-facts">
      <article>
        <h2>Location</h2>
        <p class="muted"><?= e(SAB_ADDRESS) ?></p>
        <p><a href="<?= e(SAB_MAPS) ?>" target="_blank" rel="noopener">Open in Google Maps</a></p>
      </article>
      <article>
        <h2>Phone &amp; WhatsApp</h2>
        <p><a href="tel:<?= e(SAB_PHONE) ?>"><?= e(SAB_PHONE_DISPLAY) ?></a></p>
        <p><a href="<?= e(sabWhatsApp()) ?>" target="_blank" rel="noopener">Chat on WhatsApp</a></p>
        <p class="muted"><a href="mailto:<?= e(SAB_EMAIL) ?>"><?= e(SAB_EMAIL) ?></a></p>
      </article>
      <article>
        <h2>Opening hours</h2>
        <ul class="hours-list">
          <li>Saturday – Thursday: 9:00 AM – 10:00 PM</li>
          <li>Friday: 9:00 AM – 10:00 PM</li>
          <li>Midday break: 1:30 PM – 4:30 PM</li>
        </ul>
        <p class="muted" style="margin-top:8px"><?= e(SAB_HOURS) ?></p>
      </article>
      <article>
        <h2>What to bring</h2>
        <p class="muted">Window or wall sizes, a photo of the room, and the finish you need — wallpaper, curtains or carpet. We will quote supply and installation together.</p>
      </article>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container split">
    <div>
      <div class="section-head" style="margin-bottom:20px">
        <p class="eyebrow" style="color:var(--gold)">Quote request</p>
        <h2>Tell us the room. We will call you back.</h2>
      </div>
      <?php if ($sent): ?>
        <p class="alert">Thank you. We have your quote request and will call or WhatsApp you shortly.</p>
      <?php endif; ?>
      <?php if ($errors): ?>
        <div class="alert alert-error"><?= e(implode(' ', $errors)) ?></div>
      <?php endif; ?>
      <form class="form" method="post" action="<?= e(sabPageHref('contact')) ?>">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <label>Name
          <input name="name" required value="<?= e((string) ($_POST['name'] ?? '')) ?>">
        </label>
        <label>Phone / WhatsApp
          <input name="phone" required value="<?= e((string) ($_POST['phone'] ?? '')) ?>">
        </label>
        <label>Service
          <select name="service">
            <?php
            $services = ['Wallpaper', 'Carpets & flooring', 'Curtains & blinds', 'Decoration', 'Fixing / installation', 'Not sure yet'];
            foreach ($services as $s) {
                $sel = $picked === $s ? ' selected' : '';
                echo '<option' . $sel . '>' . e($s) . '</option>';
            }
            ?>
          </select>
        </label>
        <label>Quote details (room, sizes, photos you can describe)
          <textarea name="message" required><?= e((string) ($_POST['message'] ?? '')) ?></textarea>
        </label>
        <button class="btn btn-gold" type="submit">Send quote request</button>
      </form>
    </div>
    <div class="map-wrap">
      <iframe title="Sadaf Al Bahr, Naif Road, Deira, Dubai" src="<?= e(SAB_MAPS_EMBED) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
