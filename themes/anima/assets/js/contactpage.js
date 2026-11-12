/* =============================================================================
   Contact page (/contact-us) — markup in contact.php, styles in pages.css.
   Scroll-reveal for the light body, plus an animated data-network in the hero
   with subtle cursor parallax. All motion is disabled under reduced-motion.
   ========================================================================== */
(function () {
  var reduce = matchMedia('(prefers-reduced-motion:reduce)').matches;

  /* scroll reveal */
  var io = new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
  }, { threshold: .12 });
  document.querySelectorAll('.cu-page .reveal').forEach(function (e) { io.observe(e); });

  /* hero network */
  var cv = document.getElementById('cuNet');
  if (cv && cv.getContext && !reduce) {
    var ctx = cv.getContext('2d'), DPR = Math.min(devicePixelRatio || 1, 2),
        W, H, pts = [], mx = .5, my = .5, tx = .5, ty = .5;
    function rz() { W = cv.width = cv.offsetWidth * DPR; H = cv.height = cv.offsetHeight * DPR; } rz();
    addEventListener('resize', rz);
    for (var i = 0; i < 56; i++) pts.push({ x: Math.random(), y: Math.random(), vx: (Math.random() - .5) * .0005, vy: (Math.random() - .5) * .0005 });
    addEventListener('mousemove', function (e) { tx = e.clientX / innerWidth; ty = e.clientY / innerHeight; });
    (function loop() {
      mx += (tx - mx) * .05; my += (ty - my) * .05;
      ctx.clearRect(0, 0, W, H);
      var ox = (mx - .5) * 34 * DPR, oy = (my - .5) * 34 * DPR, lim = 140 * DPR;
      for (var i = 0; i < pts.length; i++) { var p = pts[i]; p.x += p.vx; p.y += p.vy; if (p.x < 0 || p.x > 1) p.vx *= -1; if (p.y < 0 || p.y > 1) p.vy *= -1; }
      for (var i = 0; i < pts.length; i++) for (var j = i + 1; j < pts.length; j++) {
        var a = pts[i], b = pts[j], dx = (a.x - b.x) * W, dy = (a.y - b.y) * H, d = Math.hypot(dx, dy);
        if (d < lim) { ctx.globalAlpha = (1 - d / lim) * .5; ctx.strokeStyle = '#5a8cff'; ctx.lineWidth = DPR; ctx.beginPath(); ctx.moveTo(a.x * W + ox, a.y * H + oy); ctx.lineTo(b.x * W + ox, b.y * H + oy); ctx.stroke(); }
      }
      ctx.globalAlpha = 1;
      for (var i = 0; i < pts.length; i++) { var p = pts[i]; ctx.fillStyle = '#8fb6ff'; ctx.beginPath(); ctx.arc(p.x * W + ox, p.y * H + oy, 1.5 * DPR, 0, 7); ctx.fill(); }
      requestAnimationFrame(loop);
    })();
  }
})();
