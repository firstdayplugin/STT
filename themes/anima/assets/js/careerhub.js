/* =============================================================================
   Career "Careers Hub" (markup in career.php, styles in pages.css).
   - Hero data-network + cursor parallax; aurora via CSS.
   - Scroll reveal + stat count-up.
   - Search + Role + Location filter CLIENT-SIDE (no reload, no jump); URL synced.
   Reduced-motion: no network/parallax; reveals shown; counters jump to final.
   ========================================================================== */
(function () {
  var page = document.querySelector('.cx-page');
  if (!page) return;
  var reduce = matchMedia('(prefers-reduced-motion:reduce)').matches;

  /* reveal */
  var reveals = [].slice.call(document.querySelectorAll('.reveal'));
  if (reduce || !('IntersectionObserver' in window)) { reveals.forEach(function (e) { e.classList.add('in'); }); }
  else {
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
    }, { threshold: 0.14, rootMargin: '0px 0px -6% 0px' });
    reveals.forEach(function (e) { io.observe(e); });
  }

  /* count-up */
  function cnt(el) {
    var to = +el.getAttribute('data-c') || 0, suf = el.getAttribute('data-suf') || '';
    if (reduce) { el.textContent = to + suf; return; }
    var t0 = null;
    function step(ts) { t0 = t0 || ts; var p = Math.min((ts - t0) / 900, 1); el.textContent = Math.round(to * (1 - Math.pow(1 - p, 3))) + suf; if (p < 1) requestAnimationFrame(step); }
    requestAnimationFrame(step);
  }
  [].slice.call(document.querySelectorAll('.cx-stat b[data-c]')).forEach(cnt);

  /* hero network + parallax */
  (function () {
    var cv = document.getElementById('cxNet'); if (!cv || reduce) return;
    var ctx = cv.getContext('2d'), DPR = Math.min(devicePixelRatio || 1, 2), W, H, pts = [], mx = .5, my = .5, tx = .5, ty = .5;
    function rz() { W = cv.width = cv.offsetWidth * DPR; H = cv.height = cv.offsetHeight * DPR; } rz(); addEventListener('resize', rz);
    for (var i = 0; i < 58; i++) pts.push({ x: Math.random(), y: Math.random(), vx: (Math.random() - .5) * .0005, vy: (Math.random() - .5) * .0005 });
    addEventListener('mousemove', function (e) { tx = e.clientX / innerWidth; ty = e.clientY / innerHeight; });
    (function loop() {
      mx += (tx - mx) * .05; my += (ty - my) * .05; ctx.clearRect(0, 0, W, H);
      var ox = (mx - .5) * 36 * DPR, oy = (my - .5) * 36 * DPR, lim = 140 * DPR;
      for (var i = 0; i < pts.length; i++) { var p = pts[i]; p.x += p.vx; p.y += p.vy; if (p.x < 0 || p.x > 1) p.vx *= -1; if (p.y < 0 || p.y > 1) p.vy *= -1; }
      for (var i = 0; i < pts.length; i++) for (var j = i + 1; j < pts.length; j++) {
        var a = pts[i], b = pts[j], dx = (a.x - b.x) * W, dy = (a.y - b.y) * H, d = Math.hypot(dx, dy);
        if (d < lim) { ctx.globalAlpha = (1 - d / lim) * .5; ctx.strokeStyle = '#5a8cff'; ctx.lineWidth = DPR; ctx.beginPath(); ctx.moveTo(a.x * W + ox, a.y * H + oy); ctx.lineTo(b.x * W + ox, b.y * H + oy); ctx.stroke(); }
      }
      ctx.globalAlpha = 1; for (var i = 0; i < pts.length; i++) { var p = pts[i]; ctx.fillStyle = '#8fb6ff'; ctx.beginPath(); ctx.arc(p.x * W + ox, p.y * H + oy, 1.5 * DPR, 0, 7); ctx.fill(); }
      requestAnimationFrame(loop);
    })();
  })();

  /* client-side job filter */
  var jobs = [].slice.call(document.querySelectorAll('.cx-job'));
  var search = document.getElementById('cxSearch'), roleSel = document.getElementById('cxRole'), locSel = document.getElementById('cxLoc'), empty = document.getElementById('cxEmpty');
  var state = { role: page.getAttribute('data-irole') || '', loc: page.getAttribute('data-iloc') || '', q: (page.getAttribute('data-iq') || '').toLowerCase() };

  function syncUrl() {
    if (!history.replaceState) return;
    var p = [];
    if (state.role) p.push('role=' + encodeURIComponent(state.role));
    if (state.loc) p.push('lokasi=' + encodeURIComponent(state.loc));
    if (search && search.value.trim()) p.push('q=' + encodeURIComponent(search.value.trim()));
    try { history.replaceState(null, '', location.pathname + (p.length ? '?' + p.join('&') : '')); } catch (e) {}
  }
  function apply() {
    var shown = 0;
    jobs.forEach(function (it) {
      var ok = (!state.role || it.getAttribute('data-role') === state.role)
        && (!state.loc || it.getAttribute('data-loc') === state.loc)
        && (!state.q || (it.getAttribute('data-s') || '').indexOf(state.q) >= 0);
      it.style.display = ok ? '' : 'none';
      if (ok) { shown++; it.classList.add('in'); }
    });
    if (empty) empty.hidden = shown > 0;
  }
  if (roleSel) roleSel.addEventListener('change', function () { state.role = roleSel.value; apply(); syncUrl(); });
  if (locSel) locSel.addEventListener('change', function () { state.loc = locSel.value; apply(); syncUrl(); });
  if (search) { var td; search.addEventListener('input', function () { clearTimeout(td); td = setTimeout(function () { state.q = search.value.toLowerCase().trim(); apply(); syncUrl(); }, 130); }); }
  if (state.role || state.loc || state.q) apply();
})();
