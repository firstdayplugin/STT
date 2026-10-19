/* =============================================================================
   Solutions — "Constellation" hero (markup in solutions.php, styles in pages.css).
   5+ solution nodes fan out around a glowing STT core, wired by animated data
   lines with travelling pulses. Cursor parallax on the whole arena; nodes link
   to their section. On scroll the arena fades/recedes as the promo rises over it.
   Node positions are computed here from the node count, so it adapts to any
   number of sections. Disabled at <=860px / reduced-motion (static list).
   ========================================================================== */
(function () {
  var sec = document.getElementById('solHero');
  if (!sec) return;
  var arena = document.getElementById('solArena');
  var core = document.getElementById('solCore');
  var copy = document.getElementById('solCopy');
  var cv = document.getElementById('solNet');
  if (!arena || !core || !cv) return;
  var nodes = [].slice.call(arena.querySelectorAll('.scn-node'));
  if (!nodes.length) return;

  // Fan-arc layout: x spread 14%→86%, y dips up in the middle (edges lower).
  var N = nodes.length;
  nodes.forEach(function (n, i) {
    var fx = N > 1 ? i / (N - 1) : 0.5;
    var x = 14 + fx * 72;
    var y = 60 - Math.sin(fx * Math.PI) * 21;
    n.style.left = x.toFixed(2) + '%';
    n.style.top = y.toFixed(2) + '%';
  });

  var MQ = window.matchMedia('(max-width:860px), (prefers-reduced-motion:reduce)');
  var ctx = cv.getContext('2d'), DPR = Math.min(devicePixelRatio || 1, 2), W, H;
  function rz() { W = cv.width = sec.offsetWidth * DPR; H = cv.height = sec.offsetHeight * DPR; }
  rz(); addEventListener('resize', rz);

  var dust = [];
  for (var i = 0; i < 56; i++) dust.push({ x: Math.random(), y: Math.random(), r: (Math.random() * 1.5 + 0.3) * DPR, s: Math.random() * 0.0003 + 0.00008, a: Math.random() * 0.5 + 0.15 });

  var mx = 0, my = 0, tx = 0, ty = 0, t = 0, running = false;
  addEventListener('mousemove', function (e) {
    var r = sec.getBoundingClientRect();
    tx = (e.clientX - r.left) / r.width - 0.5;
    ty = (e.clientY - r.top) / r.height - 0.5;
  });
  function cc(el) { var r = el.getBoundingClientRect(), s = sec.getBoundingClientRect(); return { x: (r.left - s.left + r.width / 2) * DPR, y: (r.top - s.top + r.height / 2) * DPR }; }

  var sp = 0;   // lerped scroll progress → heavy, weighty recede as the promo covers
  function frame() {
    if (MQ.matches) { running = false; ctx.clearRect(0, 0, W, H); return; }
    t += 0.016; mx += (tx - mx) * 0.05; my += (ty - my) * 0.05;
    var spT = Math.min(Math.max(scrollY / (innerHeight * 1.15), 0), 1);
    sp += (spT - sp) * 0.055;                    // heavy trailing (About Us feel)
    var lift = sp * 70, sc = 1 - sp * 0.07;
    arena.style.opacity = (1 - sp).toFixed(3);
    arena.style.transform = 'translate(' + (mx * 26).toFixed(1) + 'px,' + (my * 20 - lift).toFixed(1) + 'px) scale(' + sc.toFixed(3) + ')';
    if (copy) { copy.style.opacity = Math.max(0, 1 - sp * 1.2).toFixed(3); copy.style.transform = 'translate(' + (mx * -10).toFixed(1) + 'px,' + (my * -8 - lift * 0.6).toFixed(1) + 'px)'; }
    var sd = sec.querySelector('.scn-scroll'); if (sd) sd.style.opacity = Math.max(0, 1 - sp * 3).toFixed(3);
    ctx.clearRect(0, 0, W, H);
    dust.forEach(function (d) { d.y -= d.s; if (d.y < 0) d.y = 1; ctx.beginPath(); ctx.arc(d.x * W, d.y * H, d.r, 0, 7); ctx.fillStyle = 'rgba(120,170,255,' + d.a + ')'; ctx.fill(); });
    var c = cc(core);
    nodes.forEach(function (n, i) {
      var p = cc(n);
      var grad = ctx.createLinearGradient(c.x, c.y, p.x, p.y);
      grad.addColorStop(0, 'rgba(90,160,255,.5)'); grad.addColorStop(1, 'rgba(73,214,199,.15)');
      ctx.beginPath(); ctx.moveTo(c.x, c.y); ctx.lineTo(p.x, p.y); ctx.strokeStyle = grad; ctx.lineWidth = 1.2 * DPR; ctx.stroke();
      var tp = (t * 0.5 + i * 0.2) % 1, px = c.x + (p.x - c.x) * tp, py = c.y + (p.y - c.y) * tp;
      ctx.beginPath(); ctx.arc(px, py, 2.6 * DPR, 0, 7); ctx.fillStyle = 'rgba(150,210,255,.95)';
      ctx.shadowBlur = 12 * DPR; ctx.shadowColor = 'rgba(90,160,255,.9)'; ctx.fill(); ctx.shadowBlur = 0;
    });
    ctx.beginPath();
    nodes.forEach(function (n, i) { var p = cc(n); if (i === 0) ctx.moveTo(p.x, p.y); else ctx.lineTo(p.x, p.y); });
    ctx.strokeStyle = 'rgba(120,170,255,.12)'; ctx.lineWidth = 1 * DPR; ctx.stroke();
    requestAnimationFrame(frame);
  }
  function start() { if (!running && !MQ.matches) { running = true; requestAnimationFrame(frame); } }
  (MQ.addEventListener ? MQ.addEventListener('change', start) : MQ.addListener(start));
  start();

  // hover dims the other nodes
  nodes.forEach(function (n) {
    n.addEventListener('mouseenter', function () { nodes.forEach(function (o) { if (o !== n) o.classList.add('dim'); }); });
    n.addEventListener('mouseleave', function () { nodes.forEach(function (o) { o.classList.remove('dim'); }); });
  });
})();
