/* =============================================================================
   Solutions — "Featured Promo Spotlight" (markup in solutions.php, styles in
   pages.css). Multi-banner carousel: one large featured slide (Ken Burns + brand
   duotone + aurora + sheen, text staggers in) and a clickable thumbnail queue
   with an auto-advance progress bar. Hover pauses; prev/next arrows. Rows come
   from the solutions_promo table. Reduced-motion: shows the first slide, no auto.
   ========================================================================== */
(function () {
  var feat = document.getElementById('pmFeat');
  var queue = document.getElementById('pmQueue');
  if (!feat || !queue) return;
  var slides = [].slice.call(feat.querySelectorAll('.pm-slide'));
  var thumbs = [].slice.call(queue.querySelectorAll('.pm-thumb'));
  if (!slides.length) return;

  var DUR = 5500;
  thumbs.forEach(function (t) { t.style.setProperty('--dur', (DUR / 1000) + 's'); });
  var cur = 0, timer = null, paused = false;
  var reduce = window.matchMedia('(prefers-reduced-motion:reduce)').matches;

  function show(i, user) {
    i = (i + slides.length) % slides.length; cur = i;
    slides.forEach(function (s, k) { s.classList.toggle('on', k === i); });
    thumbs.forEach(function (t, k) { t.classList.remove('on'); if (k === i) { void t.offsetWidth; t.classList.add('on'); } });
    if (user) restart();
  }
  function restart() { clearTimeout(timer); if (!paused && !reduce) timer = setTimeout(function () { show(cur + 1); }, DUR); }

  thumbs.forEach(function (t) { t.addEventListener('click', function () { show(+t.getAttribute('data-i'), true); }); });
  var pv = document.getElementById('pmPrev'), nx = document.getElementById('pmNext');
  if (nx) nx.addEventListener('click', function () { show(cur + 1, true); });
  if (pv) pv.addEventListener('click', function () { show(cur - 1, true); });
  [feat, queue].forEach(function (el) {
    el.addEventListener('mouseenter', function () { paused = true; clearTimeout(timer); });
    el.addEventListener('mouseleave', function () { paused = false; restart(); });
  });

  show(0);
  if (!reduce) restart();
})();
