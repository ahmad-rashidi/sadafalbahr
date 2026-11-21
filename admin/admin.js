(function () {
  function attachCounter(el, idealMin, idealMax) {
    if (el.dataset.seoBound) {
      return;
    }
    el.dataset.seoBound = '1';
    const hint = document.createElement('span');
    hint.className = 'seo-count';
    el.insertAdjacentElement('afterend', hint);

    function update() {
      const n = (el.value || '').length;
      hint.textContent = n + ' characters (aim ' + idealMin + '–' + idealMax + ')';
      hint.classList.toggle('seo-count-ok', n >= idealMin && n <= idealMax);
      hint.classList.toggle('seo-count-warn', n > 0 && (n < idealMin || n > idealMax));
    }

    el.addEventListener('input', update);
    update();
  }

  document.querySelectorAll('input, textarea').forEach(function (el) {
    const name = el.getAttribute('name') || '';
    if (/^meta_title(\[|$)/.test(name) || name === 'meta_title') {
      attachCounter(el, 50, 60);
    }
    if (/^meta_description(\[|$)/.test(name) || name === 'meta_description') {
      attachCounter(el, 140, 160);
    }
  });
})();
