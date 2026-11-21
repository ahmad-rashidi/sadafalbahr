<?php
require_once __DIR__ . '/../includes/admin-auth.php';
sab_require_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>How to use this admin | Sadaf Al Bahr</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="wrap wrap-wide">
    <?php include __DIR__ . '/_nav.php'; ?>
    <h1>How to run the Sadaf Al Bahr website</h1>
    <p class="hint">You do not need to edit code. Work from this admin. Always open the public site in another tab and check your change after you save.</p>
    <p><a class="btn" href="<?= htmlspecialchars('../') ?>" target="_blank" rel="noopener">Open the live website</a></p>

    <div class="guide-card">
      <h2>1. Log in</h2>
      <ol>
        <li>Go to <code>/admin/login.php</code> on the live site (example: <code>https://sadafalbahr.com/admin/login.php</code>).</li>
        <li>Ask the owner for the username and password. Do not share them on WhatsApp groups or email screenshots.</li>
        <li>If you stay idle a long time you may need to log in again.</li>
      </ol>
    </div>

    <div class="guide-card">
      <h2>2. What each menu is for</h2>
      <table>
        <thead><tr><th>Menu</th><th>You use it to…</th></tr></thead>
        <tbody>
          <tr><td><strong>Inquiries</strong></td><td>Read messages from the contact form. Mark them read after you call or WhatsApp the customer.</td></tr>
          <tr><td><strong>Projects</strong></td><td>Show finished jobs (gallery). Add several photos of one room or villa.</td></tr>
          <tr><td><strong>Catalogue</strong></td><td>Add company books. Each book can have many design photos (40 is fine). Customers flip photos and order.</td></tr>
          <tr><td><strong>Orders</strong></td><td>See catalogue orders customers sent (size, metres/inches, design).</td></tr>
          <tr><td><strong>Blog</strong></td><td>Write articles that Google can find (guides, tips, “how we install…”).</td></tr>
          <tr><td><strong>SEO</strong></td><td>Set the Google title and description for Home, Wallpaper, Curtains, Carpets, About, Contact, and other main pages.</td></tr>
        </tbody>
      </table>
    </div>

    <div class="guide-card">
      <h2>3. Pictures (important)</h2>
      <ul>
        <li>Use <strong>JPG, PNG or WebP</strong>. Keep each file under about <strong>8 MB</strong>.</li>
        <li>Prefer landscape photos of the real shop, rooms, or samples. Do not upload blurry phone photos if you have a clearer one.</li>
        <li>Always fill <strong>alt text</strong> — a short English sentence of what is in the photo, e.g. <em>Grey wallpaper sample in our Naif Road shop</em>. This helps Google and people who cannot see the image.</li>
        <li>After upload, click the public page and confirm the photo appears. If it does not, save again or try a smaller file.</li>
      </ul>
    </div>

    <div class="guide-card">
      <h2>4. How to add a catalogue (company book)</h2>
      <ol>
        <li>Open <a href="catalog.php">Catalogue</a> → <strong>Add another company catalogue</strong>.</li>
        <li>Choose section: Wallpaper, Curtains, or Carpets.</li>
        <li>Write the company name and the book title (what customers should call this collection).</li>
        <li>Write details (material, where it is used, width, etc.).</li>
        <li>Set the price in AED and whether it is per metre, per inch, per m², and so on.</li>
        <li>Keep <strong>Published</strong> ticked when it should show on the website.</li>
        <li>SEO: slug like <code>luxury-wallpaper-book-2026</code>. Meta title about 50–60 characters. Meta description about 140–160 characters. Mention Dubai / Naif Road if it fits.</li>
        <li>Upload design photos (you can save, then come back and add more until you have 40+).</li>
        <li>Open the website → Catalogue and check the arrows and one sample order.</li>
      </ol>
    </div>

    <div class="guide-card">
      <h2>5. How to post a blog article</h2>
      <ol>
        <li>Open <a href="blog.php">Blog</a> → <strong>Add article</strong>.</li>
        <li>Write a clear title, e.g. <em>How to choose blackout curtains in Dubai</em>.</li>
        <li>Pick a category. Write a short excerpt (2 sentences).</li>
        <li>Upload a cover photo + alt text.</li>
        <li>Fill SEO: slug, meta title, meta description, focus keyword (one main phrase, e.g. <em>blackout curtains Dubai</em>).</li>
        <li>Fill Section 1 and Section 2 with heading, text, and a photo each. Write in simple English. Repeat the keyword naturally — do not stuff it.</li>
        <li>Keep <strong>Published</strong> ticked. Save. Open the article from the public Blog page.</li>
      </ol>
      <p>Good topics: how we measure, wallpaper vs paint, carpet for villas, curtains for apartments, Naif Road shop visit, before/after of a job (also add that job under Projects).</p>
    </div>

    <div class="guide-card">
      <h2>6. How to add a project (gallery)</h2>
      <ol>
        <li>Open <a href="projects.php">Projects</a> → add project.</li>
        <li>Title, category, location (e.g. Deira, Dubai), short summary, full description.</li>
        <li>Fill SEO the same way as blog.</li>
        <li>Upload several photos. Set alt text for each after saving.</li>
        <li>Published = it appears in Gallery / Our Projects and in the sitemap for Google.</li>
      </ol>
    </div>

    <div class="guide-card">
      <h2>7. SEO rules (do this every time)</h2>
      <ul>
        <li><strong>One page, one topic.</strong> Wallpaper page is about wallpaper. Do not copy the same text on every page.</li>
        <li><strong>Meta title:</strong> 50–60 characters. Put the main words first. End with Sadaf Al Bahr or Dubai if there is room.</li>
        <li><strong>Meta description:</strong> 140–160 characters. Say what we sell and that we are on Naif Road, Deira, Dubai. This is the grey text under Google results.</li>
        <li><strong>Slug:</strong> small letters, dashes, no spaces. Example: <code>wallpaper-dubai-naif-road</code>. Do not change a slug after Google already knows the page, unless the owner agrees.</li>
        <li><strong>Focus keyword:</strong> one phrase people would type, e.g. <em>curtains Naif Road</em>.</li>
        <li><strong>Robots</strong> on SEO page: leave as <code>index,follow</code> for public pages. Use noindex only if the owner asks to hide a page from Google.</li>
        <li>After you publish, Google learns from <a href="../sitemap.php" target="_blank" rel="noopener">/sitemap.php</a>. You do not need to build the sitemap by hand.</li>
      </ul>
    </div>

    <div class="guide-card">
      <h2>8. Weekly habit</h2>
      <ol>
        <li>Check <a href="index.php">Inquiries</a> and <a href="orders.php">Orders</a> every day. Call or WhatsApp quickly.</li>
        <li>Add at least one real project photo set when a job is finished.</li>
        <li>Add or update catalogues when a new company book arrives.</li>
        <li>Publish one short blog post when you can (even twice a month is useful).</li>
        <li>Once a month, open <a href="seo.php">SEO</a> and read Home, Wallpaper, Curtains, Carpets, About, Contact. Improve titles if they are empty or too long (the counter turns red).</li>
      </ol>
    </div>

    <div class="guide-card">
      <h2>9. Do not</h2>
      <ul>
        <li>Do not change files in File Manager / Hostinger unless the owner asks. This admin is enough for content.</li>
        <li>Do not delete catalogues, articles, or projects unless you are sure. Delete cannot be undone easily.</li>
        <li>Do not copy text from other companies’ websites.</li>
        <li>Do not unpublish the Home / Wallpaper / Curtains / Carpets pages.</li>
        <li>Do not put the admin password in the website text or in a blog post.</li>
      </ul>
    </div>
  </div>
</body>
</html>
