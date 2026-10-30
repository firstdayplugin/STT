/* =============================================================================
   About Us — "Our Quality Standards" trust seals (markup in about.php, styles in
   pages.css). Dark band: international standard marks are framed plaques that
   reveal (rise + fade + light sweep) one-by-one as the band crosses the viewport,
   tied by a glowing trust rail; ambient dust drifts behind. Scroll-driven reveal
   (not pinned). Disabled at <=860px and under prefers-reduced-motion (static).
   ========================================================================== */
(function () {
  var sec = document.getElementById('qsSec');
  if (!sec) return;
  var grid = document.getElementById('qsGrid');
  var fill = document.getElementById('qsFill');
  if (!grid) return;
  var cards = [].slice.call(grid.querySelectorAll('.qs-card'));
  var nodes = cards.map(function (c) { return c.querySelector('.qs-node'); });
  if (!cards.length) return;

  var MQ = window.matchMedia('(max-width:860px), (prefers-reduced-motion:reduce)');
  var clamp = function (v, a, b) { return v < a ? a : (v > b ? b : v); };
  var revealed = [];

  function tick() {
    if (MQ.matches) { return; }
    var r = sec.getBoundingClientRect(), vh = innerHeight;
    var prog = clamp((vh * 0.85 - r.top) / (vh * 0.5), 0, 1);
    if (fill) fill.style.width = (prog * 100) + '%';
    cards.forEach(function (c, i) {
      var local = clamp((prog - i * 0.16) / 0.4, 0, 1);
      c.style.opacity = local.toFixed(3);
      c.style.transform = 'translateY(' + ((1 - local) * 40).toFixed(1) + 'px)';
      if (local > 0.02 && nodes[i]) nodes[i].style.opacity = '1';
      if (local >= 0.6 && !revealed[i]) { revealed[i] = true; c.classList.add('shine'); }
    });
    requestAnimationFrame(tick);
  }
  requestAnimationFrame(tick);

  // ambient dust behind the seals
  var cv = document.getElementById('qsBg');
  if (cv && cv.getContext) {
    var ctx = cv.getContext('2d'), DPR = Math.min(devicePixelRatio || 1, 2), W, H, ps = [];
    var rz = function () { W = cv.width = sec.offsetWidth * DPR; H = cv.height = sec.offsetHeight * DPR; };
    rz(); addEventListener('resize', rz);
    for (var i = 0; i < 46; i++) ps.push({ x: Math.random(), y: Math.random(), r: (Math.random() * 1.6 + 0.4) * DPR, s: Math.random() * 0.0004 + 0.0001, a: Math.random() * 0.5 + 0.15 });
    (function dust() {
      if (MQ.matches) { ctx.clearRect(0, 0, W, H); return; }
      ctx.clearRect(0, 0, W, H);
      ps.forEach(function (pt) {
        pt.y -= pt.s; if (pt.y < 0) pt.y = 1;
        ctx.beginPath(); ctx.arc(pt.x * W, pt.y * H, pt.r, 0, 7);
        ctx.fillStyle = 'rgba(120,170,255,' + pt.a + ')'; ctx.fill();
      });
      requestAnimationFrame(dust);
    })();
  }
})();
