/* =============================================================================
   What's New / Blog listing (markup in blog.php, styles in pages.css).
   - Scroll reveal (fade-up + image clip-wipe) via IntersectionObserver.
   - Subtle cover parallax.
   - Category tabs, live search and year filter run entirely CLIENT-SIDE:
     no reload, no jump to top. The URL is kept in sync via replaceState.
   Reduced-motion: no parallax; reveal elements are shown immediately.
   ========================================================================== */
(function () {
  var page = document.querySelector('.wn-page');
  if (!page) return;
  var reduce = matchMedia('(prefers-reduced-motion:reduce)').matches;

  /* ---- reveal ---- */
  var reveals = [].slice.call(document.querySelectorAll('.reveal'));
  if (reduce || !('IntersectionObserver' in window)) {
    reveals.forEach(function (e) { e.classList.add('in'); });
  } else {
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
    }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
    reveals.forEach(function (e) { io.observe(e); });
  }

  /* ---- cover parallax ---- */
  var cimg = document.querySelector('.wn-cover-img');
  if (cimg && !reduce) {
    addEventListener('scroll', function () {
      var y = scrollY;
      if (y < 900) cimg.style.transform = 'scale(1.06) translateY(' + (y * 0.1) + 'px)';
    }, { passive: true });
  }

  /* ---- client-side filtering ---- */
  var items = [].slice.call(document.querySelectorAll('.wn-item'));
  var tabs = [].slice.call(document.querySelectorAll('.wn-tab'));
  var search = document.getElementById('wnSearch');
  var yearSel = document.getElementById('wnYear');
  var empty = document.getElementById('wnEmpty');
  var state = {
    cat: page.getAttribute('data-ikat') || '',
    q: (page.getAttribute('data-iq') || '').toLowerCase(),
    year: page.getAttribute('data-iyear') || ''
  };

  function syncUrl() {
    if (!history.replaceState) return;
    var p = [];
    if (state.cat) p.push('kat=' + encodeURIComponent(state.cat));
    if (search && search.value.trim()) p.push('q=' + encodeURIComponent(search.value.trim()));
    if (state.year) p.push('year=' + encodeURIComponent(state.year));
    var url = location.pathname + (p.length ? '?' + p.join('&') : '');
    try { history.replaceState(null, '', url); } catch (e) {}
  }

  function apply(instant) {
    var shown = 0;
    items.forEach(function (it) {
      var okCat = !state.cat || it.getAttribute('data-cat') === state.cat;
      var okYr = !state.year || it.getAttribute('data-y') === state.year;
      var okQ = !state.q || (it.getAttribute('data-s') || '').indexOf(state.q) >= 0;
      var ok = okCat && okYr && okQ;
      it.style.display = ok ? '' : 'none';
      if (ok) { shown++; if (instant) it.classList.add('in'); }
    });
    if (empty) empty.hidden = shown > 0;
  }

  tabs.forEach(function (t) {
    t.addEventListener('click', function () {
      state.cat = t.getAttribute('data-cat') || '';
      tabs.forEach(function (x) { x.classList.toggle('on', x === t); });
      apply(true); syncUrl();
    });
  });
  if (yearSel) yearSel.addEventListener('change', function () { state.year = yearSel.value || ''; apply(true); syncUrl(); });
  if (search) {
    var td;
    search.addEventListener('input', function () {
      clearTimeout(td);
      td = setTimeout(function () { state.q = search.value.toLowerCase().trim(); apply(true); syncUrl(); }, 130);
    });
  }

  // apply any deep-linked filter on load (no scroll)
  if (state.cat || state.q || state.year) apply(true);
})();
