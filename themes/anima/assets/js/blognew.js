/* =============================================================================
   What's New / Blog listing (markup in blog.php, styles in pages.css).
   - Scroll reveal (fade-up) + subtle cover parallax.
   - Category tabs, live search and year filter run entirely CLIENT-SIDE.
   - CLIENT-SIDE PAGINATION layered on top of the filters: the filtered set is
     paged (PER per page); the pager rebuilds as filters change (always resets to
     page 1), and each page change staggers the cards in and scrolls to the list.
   Reduced-motion: no parallax / instant reveal, no stagger.
   ========================================================================== */
(function () {
  var page = document.querySelector('.wn-page');
  if (!page) return;
  var reduce = matchMedia('(prefers-reduced-motion:reduce)').matches;
  var PER = 12;

  /* ---- cover parallax ---- */
  var cimg = document.querySelector('.wn-cover-img');
  if (cimg && !reduce) {
    addEventListener('scroll', function () {
      var y = scrollY;
      if (y < 900) cimg.style.transform = 'scale(1.06) translateY(' + (y * 0.1) + 'px)';
    }, { passive: true });
  }

  var items = [].slice.call(document.querySelectorAll('.wn-item'));
  var tabs = [].slice.call(document.querySelectorAll('.wn-tab'));
  var search = document.getElementById('wnSearch');
  var yearSel = document.getElementById('wnYear');
  var empty = document.getElementById('wnEmpty');
  var pager = document.getElementById('wnPager');
  var state = {
    cat: page.getAttribute('data-ikat') || '',
    q: (page.getAttribute('data-iq') || '').toLowerCase(),
    year: page.getAttribute('data-iyear') || '',
    page: 1
  };

  var CHEVL = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>';
  var CHEVR = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>';

  function isMatch(it) {
    return (!state.cat || it.getAttribute('data-cat') === state.cat)
        && (!state.year || it.getAttribute('data-y') === state.year)
        && (!state.q || (it.getAttribute('data-s') || '').indexOf(state.q) >= 0);
  }

  function syncUrl() {
    if (!history.replaceState) return;
    var p = [];
    if (state.cat) p.push('kat=' + encodeURIComponent(state.cat));
    if (search && search.value.trim()) p.push('q=' + encodeURIComponent(search.value.trim()));
    if (state.year) p.push('year=' + encodeURIComponent(state.year));
    try { history.replaceState(null, '', location.pathname + (p.length ? '?' + p.join('&') : '')); } catch (e) {}
  }

  function pageList(c, total) {
    var out = [], lo = Math.max(2, c - 1), hi = Math.min(total - 1, c + 1);
    out.push(1);
    if (lo > 2) out.push('…');
    for (var i = lo; i <= hi; i++) out.push(i);
    if (hi < total - 1) out.push('…');
    if (total > 1) out.push(total);
    return out;
  }

  function btn(cls, target, disabled, label, on) {
    return '<button type="button" class="wn-pg ' + cls + (on ? ' on' : '') + '" data-pg="' + target + '"'
         + (disabled ? ' disabled aria-disabled="true"' : '') + (on ? ' aria-current="page"' : '') + '>' + label + '</button>';
  }

  function renderPager(total, cur) {
    if (!pager) return;
    if (total <= 1) { pager.hidden = true; pager.innerHTML = ''; return; }
    pager.hidden = false;
    var h = btn('nav', cur > 1 ? cur - 1 : 0, cur <= 1, CHEVL + '<span class="lbl">Prev</span>');
    pageList(cur, total).forEach(function (x) {
      h += (x === '…') ? '<span class="wn-pg-gap">…</span>' : btn('num', x, false, x, x === cur);
    });
    h += btn('nav', cur < total ? cur + 1 : 0, cur >= total, '<span class="lbl">Next</span>' + CHEVR);
    pager.innerHTML = '<div class="wn-pg-row">' + h + '</div><div class="wn-pager-info">Page ' + cur + ' of ' + total + '</div>';
  }

  function animateIn(vis) {
    if (reduce) { vis.forEach(function (it) { it.classList.add('in'); it.style.transitionDelay = ''; }); return; }
    vis.forEach(function (it) { it.classList.remove('in'); it.style.transitionDelay = ''; });
    void page.offsetWidth; // reflow so the removal takes effect before re-adding
    vis.forEach(function (it, i) { it.style.transitionDelay = (Math.min(i, 12) * 0.04) + 's'; it.classList.add('in'); });
    setTimeout(function () { vis.forEach(function (it) { it.style.transitionDelay = ''; }); }, 1100);
  }

  function scrollToList() {
    var t = document.querySelector('.wn-list'); if (!t) return;
    var y = t.getBoundingClientRect().top + scrollY - 76;
    try { scrollTo({ top: y, behavior: reduce ? 'auto' : 'smooth' }); } catch (e) { scrollTo(0, y); }
  }

  function render(scroll) {
    var matching = items.filter(isMatch);
    var total = Math.max(1, Math.ceil(matching.length / PER));
    if (state.page > total) state.page = total;
    if (state.page < 1) state.page = 1;
    var start = (state.page - 1) * PER, end = start + PER, vis = [];
    items.forEach(function (it) { it.style.display = 'none'; });
    matching.forEach(function (it, i) { if (i >= start && i < end) { it.style.display = ''; vis.push(it); } });
    if (empty) empty.hidden = matching.length > 0;
    renderPager(total, state.page);
    animateIn(vis);
    if (scroll) scrollToList();
  }

  function setFilter(fn) { fn(); state.page = 1; render(false); syncUrl(); }

  tabs.forEach(function (t) {
    t.addEventListener('click', function () {
      setFilter(function () { state.cat = t.getAttribute('data-cat') || ''; tabs.forEach(function (x) { x.classList.toggle('on', x === t); }); });
    });
  });
  if (yearSel) yearSel.addEventListener('change', function () { setFilter(function () { state.year = yearSel.value || ''; }); });
  if (search) {
    var td;
    search.addEventListener('input', function () {
      clearTimeout(td);
      td = setTimeout(function () { setFilter(function () { state.q = search.value.toLowerCase().trim(); }); }, 130);
    });
  }
  if (pager) pager.addEventListener('click', function (e) {
    var b = e.target.closest('.wn-pg'); if (!b || b.disabled) return;
    var n = parseInt(b.getAttribute('data-pg'), 10);
    if (n && n !== state.page) { state.page = n; render(true); }
  });

  render(false); // initial page 1 (respects any deep-linked filter)
})();
