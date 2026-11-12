/* =============================================================================
   About Us — "Awards" 3D spotlight coverflow (markup in about.php, styles in
   pages.css). Light, scroll-scrubbed: trophy cards rotate through a central
   spotlight; the centred card is big & upright with its year, and the org +
   title read below (crossfading as the centre changes). Native scroll + heavy
   eased/trailing progress. Disabled at <=860px and under prefers-reduced-motion
   (static grid). CMS-driven: cards carry data-org / data-ttl.
   ========================================================================== */
(function () {
  var wrap = document.getElementById('awWrap');
  if (!wrap) return;
  var stage = document.getElementById('awStage');
  var info = document.getElementById('awInfo');
  if (!stage || !info) return;
  var cards = [].slice.call(stage.querySelectorAll('.aw-card'));
  if (!cards.length) return;
  var infoOrg = info.querySelector('.org'), infoTtl = info.querySelector('.ttl');

  var MQ = window.matchMedia('(max-width:860px), (prefers-reduced-motion:reduce)');
  var clamp = function (v, a, b) { return v < a ? a : (v > b ? b : v); };
  var p = 0, running = false, lastAct = -1, hoverIdx = -1;
  // hover a card -> it enlarges, straightens and comes forward so the award reads clearly
  cards.forEach(function (c, i) {
    c._h = 0;
    c.addEventListener('mouseenter', function () { hoverIdx = i; });
    c.addEventListener('mouseleave', function () { if (hoverIdx === i) hoverIdx = -1; });
  });

  function frame() {
    if (MQ.matches) { running = false; return; }
    var vh = innerHeight, dtop = wrap.offsetTop, H = wrap.offsetHeight;
    var rawP = clamp((scrollY - dtop) / Math.max(1, (H - vh * 2)), 0, 1);
    p += (rawP - p) * 0.05; if (Math.abs(rawP - p) < 0.0003) p = rawP;    // heavy
    var N = cards.length, pos = p * (N - 1);

    cards.forEach(function (c, i) {
      var d = i - pos, ad = Math.abs(d);
      var tx = d * 170, tz = -Math.min(ad, 2.4) * 170, ry = clamp(-d, -1.3, 1.3) * 42, sc = 1 - Math.min(ad, 1) * 0.18;
      var op = 1 - Math.min(ad, 2.4) * 0.34, zi = 100 - Math.round(ad * 10);
      var h = (c._h += (((i === hoverIdx) ? 1 : 0) - c._h) * 0.18);   // eased 0..1 hover amount
      if (h > 0.001) { sc *= (1 + 0.5 * h); ry *= (1 - 0.82 * h); tz += 150 * h; op = Math.max(op, h); if (h > 0.35) zi = 220; }
      c.style.transform = 'translateX(' + tx.toFixed(1) + 'px) translateZ(' + tz.toFixed(1) + 'px) rotateY(' + ry.toFixed(1) + 'deg) scale(' + sc.toFixed(3) + ')';
      c.style.opacity = op.toFixed(3);
      c.style.zIndex = String(zi);
    });

    var act = Math.round(pos), fract = Math.abs(pos - act);
    info.style.opacity = clamp(1 - fract * 0.9, 0.6, 1).toFixed(3);
    info.style.transform = 'translateY(' + (fract * 6).toFixed(1) + 'px)';
    if (act !== lastAct) {
      lastAct = act; var c = cards[act];
      if (c) { infoOrg.textContent = c.getAttribute('data-org') || ''; infoTtl.textContent = c.getAttribute('data-ttl') || ''; }
    }
    requestAnimationFrame(frame);
  }
  function start() { if (!running && !MQ.matches) { running = true; requestAnimationFrame(frame); } }
  (MQ.addEventListener ? MQ.addEventListener('change', start) : MQ.addListener(start));
  start();
})();
