/* =============================================================================
   Services detail — "Capability Deck" (capability pages only). Markup in
   services-detail.php, styles in pages.css (.csd-*).
   1) Hero constellation canvas (#csdNet): drifting nodes + proximity links.
   2) Pinned area deck (#csdDeck): vertical scroll snaps each service area into
      focus; the progress rail lights and the capability list staggers in.
   Both element-guarded. The deck logic is disabled at <=860px / reduced-motion
   (CSS renders a static dark stack there).
   ========================================================================== */
(function () {
  // ---- hero constellation ----
  var cv = document.getElementById('csdNet');
  if (cv && cv.getContext) {
    var ctx = cv.getContext('2d'), W, H, DPR = Math.min(devicePixelRatio || 1, 2);
    var reduce = matchMedia('(prefers-reduced-motion:reduce)').matches;
    function rz() { var r = cv.parentElement.getBoundingClientRect(); W = cv.width = Math.max(1, r.width) * DPR; H = cv.height = Math.max(1, r.height) * DPR; }
    rz(); addEventListener('resize', rz);
    var pts = [];
    for (var i = 0; i < 70; i++) pts.push({ x: Math.random(), y: Math.random(), vx: (Math.random() - .5) * .0005, vy: (Math.random() - .5) * .0005, r: (Math.random() * 1.6 + .5) * DPR });
    function draw(move) {
      ctx.clearRect(0, 0, W, H);
      for (var i = 0; i < pts.length; i++) {
        var p = pts[i];
        if (move) { p.x += p.vx; p.y += p.vy; if (p.x < 0 || p.x > 1) p.vx *= -1; if (p.y < 0 || p.y > 1) p.vy *= -1; }
        ctx.beginPath(); ctx.arc(p.x * W, p.y * H, p.r, 0, 7); ctx.fillStyle = 'rgba(120,170,255,.6)'; ctx.fill();
        for (var j = i + 1; j < pts.length; j++) {
          var q = pts[j], dx = (p.x - q.x) * W, dy = (p.y - q.y) * H, d = Math.hypot(dx, dy);
          if (d < 120 * DPR) { ctx.beginPath(); ctx.moveTo(p.x * W, p.y * H); ctx.lineTo(q.x * W, q.y * H); ctx.strokeStyle = 'rgba(90,150,255,' + (.12 * (1 - d / (120 * DPR))) + ')'; ctx.lineWidth = DPR; ctx.stroke(); }
        }
      }
    }
    if (reduce) { draw(false); }
    else { (function loop() { draw(true); requestAnimationFrame(loop); })(); }
  }

  // ---- pinned area deck ----
  var deck = document.getElementById('csdDeck');
  if (deck) {
    var panels = [].slice.call(deck.querySelectorAll('.csd-panel'));
    var segs = [].slice.call(deck.querySelectorAll('.csd-seg'));
    var N = panels.length, cur = -1;
    var MQ = matchMedia('(max-width:860px), (prefers-reduced-motion:reduce)');
    function upd() {
      if (MQ.matches) return;                       // static stack handled by CSS
      var vh = innerHeight, top = deck.offsetTop, Hd = deck.offsetHeight;
      var p = Math.min(1, Math.max(0, (scrollY - top) / Math.max(1, (Hd - vh))));
      var act = Math.min(N - 1, Math.floor(p * N + 0.0001));
      if (act !== cur) {
        cur = act;
        for (var i = 0; i < panels.length; i++) panels[i].classList.toggle('on', i === act);
        for (var k = 0; k < segs.length; k++) segs[k].classList.toggle('on', k === act);
      }
    }
    addEventListener('scroll', upd, { passive: true });
    addEventListener('resize', upd);
    upd();
  }

  // ---- package pages: reveal tiers + matrix rows on scroll ----
  var reveals = [].slice.call(document.querySelectorAll('.pkg-reveal'));
  if (reveals.length) {
    if (!('IntersectionObserver' in window) || matchMedia('(prefers-reduced-motion:reduce)').matches) {
      reveals.forEach(function (e) { e.classList.add('in'); });
    } else {
      var rio = new IntersectionObserver(function (es) {
        es.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('in'); rio.unobserve(en.target); } });
      }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });
      reveals.forEach(function (e) { rio.observe(e); });
    }
  }
})();
