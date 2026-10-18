/* =============================================================================
   About Us — "List of Certification" brand credential wall (markup in about.php,
   styles in pages.css). Light, pinned & scroll-scrubbed: vertical scroll walks
   through vendor categories; each category's badges light up one-by-one into a
   wall, the active category + nav highlight, and the tabs are clickable (smooth
   jump). Native scroll + eased/trailing progress. Disabled at <=860px and under
   prefers-reduced-motion (static per-category grid). CMS: about_items 'cert'.
   ========================================================================== */
(function () {
  var wrap = document.getElementById('cwWrap');
  if (!wrap) return;
  var wall = document.getElementById('cwWall');
  var nav = document.getElementById('cwNav');
  if (!wall) return;
  var cats = [].slice.call(wrap.querySelectorAll('.cw-cat'));
  var navs = nav ? [].slice.call(nav.querySelectorAll('li')) : [];
  var layers = [].slice.call(wall.querySelectorAll('.cw-layer'));
  if (!layers.length) return;
  var badgesByLayer = layers.map(function (l) { return [].slice.call(l.querySelectorAll(':scope > .cw-badge')); });

  var MQ = window.matchMedia('(max-width:860px), (prefers-reduced-motion:reduce)');
  var clamp = function (v, a, b) { return v < a ? a : (v > b ? b : v); };
  var N = layers.length, p = 0, cur = -1, running = false;

  function setCat(ci) {
    if (ci === cur) return; cur = ci;
    cats.forEach(function (c, i) { c.classList.toggle('on', i === ci); });
    navs.forEach(function (l, i) { l.classList.toggle('on', i === ci); l.classList.toggle('past', i < ci); });
    layers.forEach(function (l, i) { l.classList.toggle('on', i === ci); });
  }
  function goTo(ci) {
    var vh = innerHeight, H = wrap.offsetHeight, dtop = wrap.offsetTop;
    var target = dtop + (H - vh * 2) * ((ci + 0.5) / N);
    scrollTo({ top: target, behavior: 'smooth' });
  }
  navs.forEach(function (l, i) { l.addEventListener('click', function () { if (!MQ.matches) goTo(i); }); });

  function frame() {
    if (MQ.matches) { running = false; return; }
    var vh = innerHeight, dtop = wrap.offsetTop, H = wrap.offsetHeight;
    var rawP = clamp((scrollY - dtop) / Math.max(1, (H - vh * 2)), 0, 1);
    p += (rawP - p) * 0.07; if (Math.abs(rawP - p) < 0.0003) p = rawP;    // eased
    var ci = clamp(Math.floor(p * N), 0, N - 1);
    setCat(ci);
    var localP = clamp(p * N - ci, 0, 1);
    var bs = badgesByLayer[ci], n = bs.length || 1;
    bs.forEach(function (b, i) {
      var start = (i / n) * 0.36, r = clamp((localP - start) / 0.14, 0, 1);
      b.style.opacity = r.toFixed(3);
      b.style.transform = 'translateY(' + ((1 - r) * 30).toFixed(1) + 'px) scale(' + (0.92 + r * 0.08).toFixed(3) + ')';
    });
    requestAnimationFrame(frame);
  }
  function start() { if (!running && !MQ.matches) { running = true; requestAnimationFrame(frame); } }
  (MQ.addEventListener ? MQ.addEventListener('change', start) : MQ.addListener(start));
  // first layer visible before any scroll
  if (badgesByLayer[0]) badgesByLayer[0].forEach(function (b) { b.style.opacity = 1; b.style.transform = 'none'; });
  setCat(0);
  start();
})();
