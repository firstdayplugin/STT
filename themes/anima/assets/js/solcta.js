/* =============================================================================
   Solutions — pre-footer CTA band ambient dust (markup in solutions.php, styles
   in pages.css). Drifting particles behind the "Let's talk" copy. Stops under
   prefers-reduced-motion.
   ========================================================================== */
(function () {
  var cv = document.getElementById('solCtaBg');
  if (!cv || !cv.getContext) return;
  var sec = cv.parentElement;
  var ctx = cv.getContext('2d'), DPR = Math.min(devicePixelRatio || 1, 2), W, H, ps = [];
  function rz() { W = cv.width = sec.offsetWidth * DPR; H = cv.height = sec.offsetHeight * DPR; }
  rz(); addEventListener('resize', rz);
  for (var i = 0; i < 58; i++) ps.push({ x: Math.random(), y: Math.random(), r: (Math.random() * 1.6 + 0.3) * DPR, s: Math.random() * 0.0004 + 0.0001, a: Math.random() * 0.5 + 0.15 });
  var MQ = window.matchMedia('(prefers-reduced-motion:reduce)');
  (function dust() {
    if (MQ.matches) { ctx.clearRect(0, 0, W, H); return; }
    ctx.clearRect(0, 0, W, H);
    ps.forEach(function (p) { p.y -= p.s; if (p.y < 0) p.y = 1; ctx.beginPath(); ctx.arc(p.x * W, p.y * H, p.r, 0, 7); ctx.fillStyle = 'rgba(120,170,255,' + p.a + ')'; ctx.fill(); });
    requestAnimationFrame(dust);
  })();
})();
