/* =============================================================================
   Industries listing (/industri) — "Sectors in Motion". Markup in industri.php,
   styles in pages.css. A horizontal rail that scrubs through the sector cards
   while the section is pinned; progress bar + counter + clickable dots. On
   mobile (<=900px) or reduced-motion the rail falls back to a vertical stack
   (no pin, no transform) — handled in CSS; JS disables the scrub there.
   ========================================================================== */
(function () {
  var reduce = matchMedia('(prefers-reduced-motion:reduce)').matches;

  /* scroll reveal */
  var io = new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
  }, { threshold: .12 });
  document.querySelectorAll('.ind2-page .reveal').forEach(function (e) { io.observe(e); });

  /* ---- horizontal pinned rail ---- */
  var rail = document.getElementById('ind2Rail');
  var track = document.getElementById('ind2Track');
  var pin = document.getElementById('ind2Pin');
  if (rail && track && pin) {
    var bar = document.getElementById('ind2Bar');
    var num = document.getElementById('ind2Num');
    var name = document.getElementById('ind2Name');
    var dotsWrap = document.getElementById('ind2Dots');
    var cards = [].slice.call(track.querySelectorAll('.ind2-card'));
    var N = cards.length;
    var MQ = matchMedia('(max-width:900px), (prefers-reduced-motion:reduce)');

    if (name && cards[0]) name.textContent = cards[0].getAttribute('data-name') || '';

    // dots
    var dots = [];
    for (var i = 0; i < N; i++) {
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.setAttribute('aria-label', 'Go to sector ' + (i + 1));
      if (i === 0) btn.className = 'on';
      (function (idx) { btn.addEventListener('click', function () { jump(idx); }); })(i);
      dotsWrap.appendChild(btn);
      dots.push(btn);
    }

    function railHeight() { return (N + 0.4) * 0.82 * innerHeight; }
    function layout() {
      if (MQ.matches) { rail.style.height = ''; track.style.transform = ''; return; }
      rail.style.height = railHeight() + 'px';
      onScroll();
    }
    function jump(idx) {
      if (MQ.matches) { cards[idx] && cards[idx].scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }
      var p = N > 1 ? idx / (N - 1) : 0;
      scrollTo({ top: rail.offsetTop + p * (rail.offsetHeight - innerHeight), behavior: 'smooth' });
    }

    var cur = -1;
    function onScroll() {
      if (MQ.matches) return;
      var h = rail.offsetHeight - innerHeight;
      var p = h > 0 ? (scrollY - rail.offsetTop) / h : 0;
      p = Math.max(0, Math.min(1, p));
      var maxX = track.scrollWidth - innerWidth;
      if (maxX < 0) maxX = 0;
      track.style.transform = 'translateX(' + (-p * maxX) + 'px)';
      if (bar) bar.style.width = (8 + p * 92) + '%';
      var idx = Math.round(p * (N - 1));
      if (idx !== cur) {
        cur = idx;
        if (num) num.textContent = String(idx + 1).padStart(2, '0');
        if (name && cards[idx]) name.textContent = cards[idx].getAttribute('data-name') || '';
        var ac = cards[idx] && cards[idx].style.getPropertyValue('--ac');
        if (ac) pin.style.background = 'radial-gradient(120% 100% at 15% 0%,' + hexa(ac.trim(), .14) + ',transparent 60%),#070c17';
        for (var k = 0; k < dots.length; k++) dots[k].className = (k === idx ? 'on' : '');
      }
    }
    function hexa(c, a) {
      if (c[0] !== '#') return c;
      var n = parseInt(c.slice(1), 16);
      return 'rgba(' + (n >> 16 & 255) + ',' + (n >> 8 & 255) + ',' + (n & 255) + ',' + a + ')';
    }

    layout();
    addEventListener('resize', layout);
    addEventListener('scroll', onScroll, { passive: true });
  }

  /* ---- ambient network canvas (hero + rail) ---- */
  function net(cv, count, color) {
    if (!cv || !cv.getContext || reduce) return;
    var ctx = cv.getContext('2d'), DPR = Math.min(devicePixelRatio || 1, 2), W, H, pts = [], mx = .5, my = .5, tx = .5, ty = .5;
    function rz() { W = cv.width = cv.offsetWidth * DPR; H = cv.height = cv.offsetHeight * DPR; } rz();
    addEventListener('resize', rz);
    for (var i = 0; i < count; i++) pts.push({ x: Math.random(), y: Math.random(), vx: (Math.random() - .5) * .0004, vy: (Math.random() - .5) * .0004 });
    addEventListener('mousemove', function (e) { tx = e.clientX / innerWidth; ty = e.clientY / innerHeight; });
    (function loop() {
      mx += (tx - mx) * .04; my += (ty - my) * .04;
      ctx.clearRect(0, 0, W, H);
      var ox = (mx - .5) * 30 * DPR, oy = (my - .5) * 30 * DPR, lim = 150 * DPR;
      for (var i = 0; i < pts.length; i++) { var p = pts[i]; p.x += p.vx; p.y += p.vy; if (p.x < 0 || p.x > 1) p.vx *= -1; if (p.y < 0 || p.y > 1) p.vy *= -1; }
      for (var i = 0; i < pts.length; i++) for (var j = i + 1; j < pts.length; j++) {
        var a = pts[i], b = pts[j], dx = (a.x - b.x) * W, dy = (a.y - b.y) * H, d = Math.hypot(dx, dy);
        if (d < lim) { ctx.globalAlpha = (1 - d / lim) * .45; ctx.strokeStyle = color; ctx.lineWidth = DPR; ctx.beginPath(); ctx.moveTo(a.x * W + ox, a.y * H + oy); ctx.lineTo(b.x * W + ox, b.y * H + oy); ctx.stroke(); }
      }
      ctx.globalAlpha = 1;
      for (var i = 0; i < pts.length; i++) { var p = pts[i]; ctx.fillStyle = color; ctx.beginPath(); ctx.arc(p.x * W + ox, p.y * H + oy, 1.4 * DPR, 0, 7); ctx.fill(); }
      requestAnimationFrame(loop);
    })();
  }
  net(document.getElementById('ind2Net'), 52, '#6aa0ff');
  net(document.getElementById('ind2RailNet'), 40, '#5a8cff');
})();
