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
