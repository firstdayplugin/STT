/* =============================================================================
   Career detail / apply (markup in career-detail.php, styles in pages.css).
   Hero data-network, custom CV filename display, and a Share button. No motion
   under reduced-motion.
   ========================================================================== */
(function () {
  var reduce = matchMedia('(prefers-reduced-motion:reduce)').matches;

  /* hero network */
  var cv = document.getElementById('cdNet');
  if (cv && cv.getContext && !reduce) {
    var ctx = cv.getContext('2d'), DPR = Math.min(devicePixelRatio || 1, 2), W, H, pts = [];
    function rz() { W = cv.width = cv.offsetWidth * DPR; H = cv.height = cv.offsetHeight * DPR; } rz(); addEventListener('resize', rz);
    for (var i = 0; i < 44; i++) pts.push({ x: Math.random(), y: Math.random(), vx: (Math.random() - .5) * .0005, vy: (Math.random() - .5) * .0005 });
    (function loop() {
      ctx.clearRect(0, 0, W, H); var lim = 130 * DPR;
      for (var i = 0; i < pts.length; i++) { var p = pts[i]; p.x += p.vx; p.y += p.vy; if (p.x < 0 || p.x > 1) p.vx *= -1; if (p.y < 0 || p.y > 1) p.vy *= -1; }
      for (var i = 0; i < pts.length; i++) for (var j = i + 1; j < pts.length; j++) { var a = pts[i], b = pts[j], dx = (a.x - b.x) * W, dy = (a.y - b.y) * H, d = Math.hypot(dx, dy); if (d < lim) { ctx.globalAlpha = (1 - d / lim) * .45; ctx.strokeStyle = '#5a8cff'; ctx.lineWidth = DPR; ctx.beginPath(); ctx.moveTo(a.x * W, a.y * H); ctx.lineTo(b.x * W, b.y * H); ctx.stroke(); } }
      ctx.globalAlpha = 1; for (var i = 0; i < pts.length; i++) { var p = pts[i]; ctx.fillStyle = '#8fb6ff'; ctx.beginPath(); ctx.arc(p.x * W, p.y * H, 1.4 * DPR, 0, 7); ctx.fill(); }
      requestAnimationFrame(loop);
    })();
  }

  /* CV filename */
  var cvInput = document.getElementById('cdCv'), cvT = document.getElementById('cdCvT');
  if (cvInput && cvT) {
    var base = cvT.textContent;
    cvInput.addEventListener('change', function () {
      var f = cvInput.files && cvInput.files[0];
      cvT.textContent = f ? f.name : base;
      cvInput.closest('.cd-cv').classList.toggle('has', !!f);
    });
  }

  /* Share */
  var share = document.getElementById('cdShare');
  if (share) share.addEventListener('click', function () {
    var url = location.href, title = document.title;
    if (navigator.share) { navigator.share({ title: title, url: url }).catch(function () {}); }
    else if (navigator.clipboard) {
      navigator.clipboard.writeText(url).then(function () {
        var t = share.textContent; share.textContent = 'Link copied'; setTimeout(function () { share.textContent = t; }, 1600);
      }).catch(function () {});
    }
  });
})();
