/* =============================================================================
   Industry detail — "Command Deck" (markup in industri-detail.php, styles in
   pages.css). A cinematic hero (data-network + cursor parallax + scan sweep)
   flows into a pinned, scroll-scrubbed deck of the solution pillars. Each pillar
   dwells sharp for most of its scroll band (heavy feel) and only crossfades at
   the edges; its capabilities assemble into a glowing "constellation" network —
   nodes laid out on a golden spiral with connecting lines that draw in.
   Disabled at <=860px / reduced-motion (CSS renders a static stacked layout).
   ========================================================================== */
(function () {
  var deck = document.getElementById('idcDeck');
  var MQ = window.matchMedia('(max-width:860px), (prefers-reduced-motion:reduce)');

  /* ---- hero data-network + parallax (skipped on mobile/reduced-motion) ---- */
  (function () {
    var cv = document.getElementById('idcNet');
    if (!cv || MQ.matches) return;
    var hero = cv.parentElement, ctx = cv.getContext('2d'), DPR = Math.min(devicePixelRatio || 1, 2), W, H, pts = [], mx = .5, my = .5, tmx = .5, tmy = .5;
    function rz() { W = cv.width = hero.offsetWidth * DPR; H = cv.height = hero.offsetHeight * DPR; }
    rz(); addEventListener('resize', rz);
    for (var i = 0; i < 64; i++) pts.push({ x: Math.random(), y: Math.random(), vx: (Math.random() - .5) * .0006, vy: (Math.random() - .5) * .0006 });
    addEventListener('mousemove', function (e) { tmx = e.clientX / innerWidth; tmy = e.clientY / innerHeight; });
    (function loop() {
      mx += (tmx - mx) * .05; my += (tmy - my) * .05; ctx.clearRect(0, 0, W, H);
      var ox = (mx - .5) * 40 * DPR, oy = (my - .5) * 40 * DPR, lim = 140 * DPR;
      for (var i = 0; i < pts.length; i++) { var p = pts[i]; p.x += p.vx; p.y += p.vy; if (p.x < 0 || p.x > 1) p.vx *= -1; if (p.y < 0 || p.y > 1) p.vy *= -1; }
      for (var i = 0; i < pts.length; i++) for (var j = i + 1; j < pts.length; j++) {
        var a = pts[i], b = pts[j], dx = (a.x - b.x) * W, dy = (a.y - b.y) * H, d = Math.hypot(dx, dy);
        if (d < lim) { ctx.globalAlpha = (1 - d / lim) * .5; ctx.strokeStyle = '#5a8cff'; ctx.lineWidth = DPR; ctx.beginPath(); ctx.moveTo(a.x * W + ox, a.y * H + oy); ctx.lineTo(b.x * W + ox, b.y * H + oy); ctx.stroke(); }
      }
      ctx.globalAlpha = 1; for (var i = 0; i < pts.length; i++) { var p = pts[i]; ctx.fillStyle = '#8fb6ff'; ctx.beginPath(); ctx.arc(p.x * W + ox, p.y * H + oy, 1.6 * DPR, 0, 7); ctx.fill(); }
      requestAnimationFrame(loop);
    })();
  })();

  /* ---- capability detail popover (works for desktop nodes AND mobile chips) ---- */
  (function () {
    if (!deck) return;
    var modal = document.getElementById('idcModal'); if (!modal) return;
    var icEl = document.getElementById('idcModalIc'), tEl = document.getElementById('idcModalT'), bEl = document.getElementById('idcModalBody');
    var last = null;
    function open(node) {
      var dot = node.querySelector('.idc-dot'), lb = node.querySelector('.idc-lb'), nd = node.querySelector('.idc-nd');
      if (icEl) icEl.innerHTML = dot ? dot.innerHTML : '';
      if (tEl) tEl.textContent = lb ? lb.textContent.trim() : '';
      if (bEl) bEl.innerHTML = nd ? nd.innerHTML : '';
      modal.hidden = false; document.body.style.overflow = 'hidden';
      requestAnimationFrame(function () { modal.classList.add('open'); });
      last = node;
    }
    function close() {
      modal.classList.remove('open'); document.body.style.overflow = '';
      setTimeout(function () { modal.hidden = true; }, 260);
      if (last) { try { last.focus(); } catch (e) {} last = null; }
    }
    deck.addEventListener('click', function (e) {
      var n = e.target.closest ? e.target.closest('.idc-node.has-detail') : null;
      if (n) { e.preventDefault(); open(n); }
    });
    modal.addEventListener('click', function (e) { if (e.target.hasAttribute('data-idc-close')) close(); });
    addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modal.hidden) close(); });
  })();

  if (!deck || MQ.matches) return;   // the deck itself only animates on desktop

  /* ---- ambient dust inside the pinned stage ---- */
  (function () {
    var cv = document.getElementById('idcDust'); if (!cv) return;
    var ctx = cv.getContext('2d'), DPR = Math.min(devicePixelRatio || 1, 2), W, H, ps = [];
    function rz() { W = cv.width = cv.offsetWidth * DPR; H = cv.height = cv.offsetHeight * DPR; } rz(); addEventListener('resize', rz);
    for (var i = 0; i < 60; i++) ps.push({ x: Math.random(), y: Math.random(), r: (Math.random() * 1.4 + .3) * DPR, s: Math.random() * .0003 + .00006, a: Math.random() * .4 + .1 });
    (function loop() { ctx.clearRect(0, 0, W, H); ps.forEach(function (p) { p.y -= p.s; if (p.y < 0) p.y = 1; ctx.beginPath(); ctx.arc(p.x * W, p.y * H, p.r, 0, 7); ctx.fillStyle = 'rgba(130,170,255,' + p.a + ')'; ctx.fill(); }); requestAnimationFrame(loop); })();
  })();

  var panels = [].slice.call(deck.querySelectorAll('.idc-panel'));
  var railBtns = [].slice.call(deck.querySelectorAll('.idc-rail button'));
  var cue = document.getElementById('idcScrollCue');
  var N = panels.length;
  if (!N) return;

  /* ---- per-panel constellation layout (golden spiral + nearest-neighbour edges) ---- */
  function layout(panel) {
    var nodes = [].slice.call(panel.querySelectorAll('.idc-node')), n = nodes.length, GA = 2.399963;
    nodes.forEach(function (nd, i) {
      var r = Math.sqrt((i + 0.6) / n) * 39, a = i * GA;
      var x = 50 + Math.cos(a) * r * 1.18, y = 50 + Math.sin(a) * r;
      nd._x = Math.max(11, Math.min(89, x)); nd._y = Math.max(13, Math.min(87, y));
      nd._appear = 0.18 + (i / n) * 0.5;
      nd._jx = Math.random() * 6.28; nd._jy = Math.random() * 6.28;
      nd.style.left = nd._x + '%'; nd.style.top = nd._y + '%';
    });
    var edges = [];
    for (var i = 0; i < n; i++) { var dd = []; for (var j = 0; j < n; j++) { if (i === j) continue; var dx = nodes[i]._x - nodes[j]._x, dy = nodes[i]._y - nodes[j]._y; dd.push([dx * dx + dy * dy, j]); } dd.sort(function (a, b) { return a[0] - b[0]; }); edges.push([i, dd[0][1]]); if (dd[1]) edges.push([i, dd[1][1]]); }
    panel._nodes = nodes; panel._edges = edges; panel._cv = panel.querySelector('.idc-con canvas');
  }
  panels.forEach(layout);

  railBtns.forEach(function (b, i) {
    b.addEventListener('click', function () { var h = deck.offsetHeight - innerHeight; scrollTo({ top: deck.offsetTop + h * ((i + 0.5) / N), behavior: 'smooth' }); });
  });

  /* ---- scrub engine (heavy lerp + dwell) ---- */
  var p = 0, DPR = Math.min(devicePixelRatio || 1, 2);
  var FULL = 0.34, FADE = 0.40;   // dwell half-width + crossfade width (in segment units)
  function draw() {
    var travel = deck.offsetHeight - innerHeight;
    var raw = Math.max(0, Math.min(1, (scrollY - deck.offsetTop) / travel));
    p += (raw - p) * 0.06;                                   // heavy
    var pos = p * N, active = Math.max(0, Math.min(N - 1, Math.round(pos - 0.5)));
    var t = performance.now() / 1000;
    panels.forEach(function (panel, i) {
      var center = i + 0.5, d = Math.abs(pos - center);
      var a = Math.max(0, Math.min(1, 1 - (d - FULL) / FADE));  // 1 across the dwell, fades at edges
      panel.style.opacity = a;
      panel.style.visibility = a > 0.015 ? 'visible' : 'hidden';
      panel.style.transform = 'translateY(' + ((pos - center) * 26) + 'px)';
      if (a > 0.55) panel.classList.add('show'); else panel.classList.remove('show');
      var cv = panel._cv; if (!cv) return;
      var ctx = cv.getContext('2d');
      if (a <= 0.015) { ctx.clearRect(0, 0, cv.width, cv.height); return; }
      if (cv.width !== cv.offsetWidth * DPR) { cv.width = cv.offsetWidth * DPR; cv.height = cv.offsetHeight * DPR; }
      var W = cv.width, H = cv.height; ctx.clearRect(0, 0, W, H);
      var nodes = panel._nodes, drift = a > 0.5 ? 1 : 0;
      nodes.forEach(function (nd) {
        var prog = Math.max(0, Math.min(1, (a - nd._appear) / 0.22));
        var dx = Math.sin(t * 0.8 + nd._jx) * 1.0 * drift, dy = Math.cos(t * 0.7 + nd._jy) * 1.0 * drift;
        nd._px = (nd._x + dx) / 100 * W; nd._py = (nd._y + dy) / 100 * H;
        if (prog > 0.02) nd.classList.add('in'); else nd.classList.remove('in');
        nd.style.transform = 'translate(-50%,-50%) scale(' + (0.4 + 0.6 * prog) + ')';
        nd.style.left = (nd._x + dx) + '%'; nd.style.top = (nd._y + dy) + '%';
      });
      ctx.lineWidth = DPR;
      panel._edges.forEach(function (e) {
        var A = nodes[e[0]], B = nodes[e[1]];
        var pa = Math.max(0, Math.min(1, (a - A._appear) / 0.22)), pb = Math.max(0, Math.min(1, (a - B._appear) / 0.22)), f = Math.min(pa, pb);
        if (f <= 0.02) return;
        var g = ctx.createLinearGradient(A._px, A._py, B._px, B._py);
        g.addColorStop(0, 'rgba(90,150,255,' + (0.5 * f) + ')'); g.addColorStop(1, 'rgba(73,214,199,' + (0.4 * f) + ')');
        ctx.strokeStyle = g; ctx.beginPath(); ctx.moveTo(A._px, A._py); ctx.lineTo(A._px + (B._px - A._px) * f, A._py + (B._py - A._py) * f); ctx.stroke();
      });
    });
    railBtns.forEach(function (b, i) { b.classList.toggle('on', i === active); });
    if (cue) cue.style.opacity = Math.max(0, 1 - pos / 0.45);   // fades as the first panel settles
    requestAnimationFrame(draw);
  }
  requestAnimationFrame(draw);
})();
