  </main>
  <footer class="site-footer">
    <div class="container footer-grid">
      <div>
        <p class="footer-brand"><?= e(SAB_NAME) ?></p>
        <p class="footer-ar"><?= SAB_NAME_AR ?></p>
        <p>Wallpaper, carpets, curtains, decoration and professional installation from our shop on Naif Road, Deira.</p>
      </div>
      <div>
        <h2>Pages</h2>
        <p><a href="<?= e(sabPageHref('wallpaper')) ?>">Wallpaper</a></p>
        <p><a href="<?= e(sabPageHref('curtains')) ?>">Curtains</a></p>
        <p><a href="<?= e(sabPageHref('carpets')) ?>">Carpets</a></p>
        <p><a href="<?= e(sabPageHref('catalog')) ?>">Catalogue</a></p>
        <p><a href="<?= e(sabPageHref('gallery')) ?>">Gallery / Our Projects</a></p>
        <p><a href="<?= e(sabPageHref('blog')) ?>">Blog</a></p>
        <p><a href="<?= e(sabPageHref('about')) ?>">About Us</a></p>
        <p><a href="<?= e(sabPageHref('contact')) ?>">Contact Us</a></p>
      </div>
      <div>
        <h2>Visit the shop</h2>
        <p><?= e(SAB_ADDRESS) ?></p>
        <p><?= e(SAB_HOURS) ?></p>
      </div>
      <div>
        <h2>Talk to us</h2>
        <p><a href="tel:<?= e(SAB_PHONE) ?>"><?= e(SAB_PHONE_DISPLAY) ?></a></p>
        <p><a href="mailto:<?= e(SAB_EMAIL) ?>"><?= e(SAB_EMAIL) ?></a></p>
        <p><a href="<?= e(SAB_MAPS) ?>" target="_blank" rel="noopener">Open in Google Maps</a></p>
      </div>
    </div>
    <p class="footer-copy">© <?= date('Y') ?> <?= e(SAB_NAME) ?>. All rights reserved.</p>
  </footer>
  <a class="wa-float" href="<?= e(sabWhatsApp()) ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true"><path fill="currentColor" d="M20.5 3.5A11 11 0 0 0 2.1 18.3L1 23l4.8-1.1A11 11 0 0 0 12 23a11 11 0 0 0 8.5-19.5zM12 21a9 9 0 0 1-4.6-1.3l-.3-.2-2.9.7.7-2.8-.2-.3A9 9 0 1 1 12 21zm5-6.7c-.3-.1-1.6-.8-1.9-.9s-.4-.1-.6.1-.7.9-.8 1-.3.2-.6.1a7.4 7.4 0 0 1-2.2-1.4 8.2 8.2 0 0 1-1.5-1.9c-.2-.3 0-.4.1-.6l.4-.5.1-.2a.5.5 0 0 0 0-.5c0-.1-.6-1.4-.8-1.9s-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-1 2.2 5.2 5.2 0 0 0 1.1 2.8 11.8 11.8 0 0 0 4.5 4 15 15 0 0 0 1.5.6 3.6 3.6 0 0 0 1.6.1 2.7 2.7 0 0 0 1.8-1.2 2.2 2.2 0 0 0 .2-1.2c-.1-.1-.3-.2-.6-.3z"/></svg>
  </a>
  <script src="<?= e(sabHref('assets/js/main.js?v=2')) ?>"></script>
</body>
</html>
