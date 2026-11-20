(() => {
  const header = document.querySelector('[data-header]');
  const toggle = document.querySelector('[data-nav-toggle]');
  const nav = document.querySelector('[data-nav]');

  const onScroll = () => {
    header?.classList.toggle('is-scrolled', window.scrollY > 12);
  };
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  toggle?.addEventListener('click', () => {
    const open = nav?.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  });

  nav?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      nav.classList.remove('is-open');
      toggle?.setAttribute('aria-expanded', 'false');
    });
  });

  const viewer = document.querySelector('[data-design-viewer]');
  if (viewer) {
    const slides = [...viewer.querySelectorAll('[data-slide]')];
    const thumbs = [...viewer.querySelectorAll('[data-thumb]')];
    const nameEl = viewer.querySelector('[data-design-name]');
    const countEl = viewer.querySelector('[data-counter]');
    const orderBtn = viewer.querySelector('[data-order-btn]');
    let index = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));
    if (index < 0) index = 0;

    const show = (next) => {
      if (!slides.length) return;
      index = (next + slides.length) % slides.length;
      slides.forEach((slide, i) => {
        const on = i === index;
        slide.classList.toggle('is-active', on);
        slide.hidden = !on;
      });
      thumbs.forEach((thumb, i) => thumb.classList.toggle('is-active', i === index));
      const current = slides[index];
      if (nameEl) nameEl.textContent = current.getAttribute('data-name') || '';
      if (countEl) countEl.textContent = String(index + 1);
      if (orderBtn) orderBtn.setAttribute('href', current.getAttribute('data-order') || '#');
      thumbs[index]?.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    };

    viewer.querySelector('[data-prev]')?.addEventListener('click', () => show(index - 1));
    viewer.querySelector('[data-next]')?.addEventListener('click', () => show(index + 1));
    thumbs.forEach((thumb, i) => thumb.addEventListener('click', () => show(i)));

    document.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowLeft') show(index - 1);
      if (event.key === 'ArrowRight') show(index + 1);
    });

    let startX = 0;
    const stage = viewer.querySelector('.design-frame');
    stage?.addEventListener('touchstart', (event) => {
      startX = event.changedTouches[0]?.clientX || 0;
    }, { passive: true });
    stage?.addEventListener('touchend', (event) => {
      const endX = event.changedTouches[0]?.clientX || 0;
      if (endX - startX > 40) show(index - 1);
      if (startX - endX > 40) show(index + 1);
    }, { passive: true });
  }

  const filters = document.querySelector('[data-filters]');
  if (filters) {
    const figures = document.querySelectorAll('.gallery-grid figure, .gallery-grid .project-tile');
    filters.querySelectorAll('button').forEach((button) => {
      button.addEventListener('click', () => {
        filters.querySelectorAll('button').forEach((item) => item.classList.remove('is-active'));
        button.classList.add('is-active');
        const value = button.getAttribute('data-filter');
        figures.forEach((figure) => {
          const show = value === 'all' || figure.getAttribute('data-category') === value;
          figure.hidden = !show;
        });
      });
    });
  }
})();
