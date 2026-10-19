/* =============================================================================
   Solutions — "Deck" (markup in solutions.php, styles in pages.css). Dark, pinned
   and scroll-scrubbed: vertical scroll walks through the solution panels (heavy
   lerp); each crossfades with a slight vertical travel, its partner-logo wall
   cascades in, and the left rail highlights the active one (clickable to jump).
   Height set inline (scales with panel count). Disabled at <=860px and under
   prefers-reduced-motion (static stack).
   ========================================================================== */
(function () {
  var wrap = document.getElementById('sdWrap');
  if (!wrap) return;
  var stage = document.getElementById('sdStage');
  var rail = document.getElementById('sdRail');
  if (!stage) return;
  var panels = [].slice.call(stage.querySelectorAll('.sd-panel'));
  var btns = rail ? [].slice.call(rail.querySelectorAll('button')) : [];
  if (!panels.length) return;

  var MQ = window.matchMedia('(max-width:860px), (prefers-reduced-motion:reduce)');
  var clamp = function (v, a, b) { return v < a ? a : (v > b ? b : v); };
  var N = panels.length, p = 0, cur = -1, running = false;

  function goTo(i) {
    var vh = innerHeight, H = wrap.offsetHeight, dtop = wrap.offsetTop;
    scrollTo({ top: dtop + (H - vh * 2) * (i / Math.max(1, N - 1)), behavior: 'smooth' });
  }
  btns.forEach(function (b) { b.addEventListener('click', function () { if (!MQ.matches) goTo(+b.getAttribute('data-i')); }); });

  function frame() {
    if (MQ.matches) { running = false; return; }
    var vh = innerHeight, dtop = wrap.offsetTop, H = wrap.offsetHeight;
    var rawP = clamp((scrollY - dtop) / Math.max(1, (H - vh * 2)), 0, 1);
    p += (rawP - p) * 0.05; if (Math.abs(rawP - p) < 0.0003) p = rawP;   // heavy
    var pos = p * (N - 1), act = Math.round(pos);
    panels.forEach(function (pn, i) {
      var d = i - pos, ad = Math.abs(d);
      pn.style.opacity = clamp(1 - ad * 2.1, 0, 1).toFixed(3);
      pn.style.transform = 'translateY(' + (d * 40).toFixed(1) + 'px) scale(' + (1 - Math.min(ad, 1) * 0.04).toFixed(3) + ')';
      pn.style.zIndex = String(10 - Math.round(ad * 5));
      pn.classList.toggle('on', i === act);
    });
    if (act !== cur) { cur = act; btns.forEach(function (b, i) { b.classList.toggle('on', i === act); b.classList.toggle('past', i < act); }); }
    requestAnimationFrame(frame);
  }
  function start() { if (!running && !MQ.matches) { running = true; requestAnimationFrame(frame); } }
  (MQ.addEventListener ? MQ.addEventListener('change', start) : MQ.addListener(start));
  start();

  // ambient dust
  var cv = document.getElementById('sdBg');
  if (cv && cv.getContext) {
    var ctx = cv.getContext('2d'), DPR = Math.min(devicePixelRatio || 1, 2), W, H2, ps = [];
    var rz = function () { W = cv.width = innerWidth * DPR; H2 = cv.height = innerHeight * DPR; };
    rz(); addEventListener('resize', rz);
    for (var i = 0; i < 54; i++) ps.push({ x: Math.random(), y: Math.random(), r: (Math.random() * 1.5 + 0.3) * DPR, s: Math.random() * 0.0003 + 0.00008, a: Math.random() * 0.5 + 0.15 });
    (function dust() {
      if (MQ.matches) { ctx.clearRect(0, 0, W, H2); return; }
      ctx.clearRect(0, 0, W, H2);
      ps.forEach(function (d) { d.y -= d.s; if (d.y < 0) d.y = 1; ctx.beginPath(); ctx.arc(d.x * W, d.y * H2, d.r, 0, 7); ctx.fillStyle = 'rgba(120,170,255,' + d.a + ')'; ctx.fill(); });
      requestAnimationFrame(dust);
    })();
  }
})();
