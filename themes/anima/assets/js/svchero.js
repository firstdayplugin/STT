/* =============================================================================
   Services — "Lifecycle Loop" hero (markup in services.php, styles in pages.css).
   The delivery lifecycle stages sit on a glowing closed loop around an END-TO-END
   hub; a light comet travels the loop continuously (the cycle never stops), faint
   spokes reach the hub, and the whole arena drifts with the cursor. Node count
   adapts automatically. Disabled at <=860px / reduced-motion (static list).
   ========================================================================== */
(function () {
  var sec = document.getElementById('svcHero');
  if (!sec) return;
  var stage = document.getElementById('svcStage');
  var hub = document.getElementById('svcHub');
  var copy = document.getElementById('svcCopy');
  var cv = document.getElementById('svcNet');
  if (!stage || !cv) return;
  var nodes = [].slice.call(stage.querySelectorAll('.svc-node'));
  if (!nodes.length) return;

  var CX = 50, CY = 60;   // loop centre (% of hero)
  function place() {
    var w = sec.offsetWidth, h = sec.offsetHeight, ccx = w * CX / 100, ccy = h * CY / 100, rad = Math.min(w, h) * 0.245;
    nodes.forEach(function (nd, i) {
      var ang = -Math.PI / 2 + i * (Math.PI * 2 / nodes.length);
      nd.style.left = (ccx + Math.cos(ang) * rad) + 'px';
      nd.style.top = (ccy + Math.sin(ang) * rad) + 'px';
    });
  }

  var MQ = window.matchMedia('(max-width:860px), (prefers-reduced-motion:reduce)');
  var ctx = cv.getContext('2d'), DPR = Math.min(devicePixelRatio || 1, 2), W, H;
  function rz() { W = cv.width = sec.offsetWidth * DPR; H = cv.height = sec.offsetHeight * DPR; place(); }
  rz(); addEventListener('resize', rz);

  var dust = [];
  for (var i = 0; i < 56; i++) dust.push({ x: Math.random(), y: Math.random(), r: (Math.random() * 1.5 + 0.3) * DPR, s: Math.random() * 0.0003 + 0.00008, a: Math.random() * 0.5 + 0.15 });
  var mx = 0, my = 0, tx = 0, ty = 0, t = 0, running = false;
  addEventListener('mousemove', function (e) { var r = sec.getBoundingClientRect(); tx = (e.clientX - r.left) / r.width - 0.5; ty = (e.clientY - r.top) / r.height - 0.5; });
  function cc(el) { var b = el.getBoundingClientRect(), s = sec.getBoundingClientRect(); return { x: (b.left - s.left + b.width / 2) * DPR, y: (b.top - s.top + b.height / 2) * DPR }; }
  function nodeIc(nd) { return cc(nd.querySelector('.ic')); }

  function frame() {
    if (MQ.matches) { running = false; ctx.clearRect(0, 0, W, H); return; }
    t += 0.016; mx += (tx - mx) * 0.05; my += (ty - my) * 0.05;
    stage.style.transform = 'translate(' + (mx * 22).toFixed(1) + 'px,' + (my * 16).toFixed(1) + 'px)';
    if (copy) copy.style.transform = 'translate(' + (mx * -8).toFixed(1) + 'px,' + (my * -6).toFixed(1) + 'px)';
    ctx.clearRect(0, 0, W, H);
    dust.forEach(function (d) { d.y -= d.s; if (d.y < 0) d.y = 1; ctx.beginPath(); ctx.arc(d.x * W, d.y * H, d.r, 0, 7); ctx.fillStyle = 'rgba(120,170,255,' + d.a + ')'; ctx.fill(); });
    var pts = nodes.map(nodeIc);
    var hx, hy;
    if (hub) { var hp = cc(hub); hx = hp.x; hy = hp.y; } else { hx = W * CX / 100; hy = H * CY / 100; }
    // spokes to hub (faint)
    pts.forEach(function (p) { ctx.beginPath(); ctx.moveTo(hx, hy); ctx.lineTo(p.x, p.y); ctx.strokeStyle = 'rgba(120,170,255,.1)'; ctx.lineWidth = 1 * DPR; ctx.stroke(); });
    // glowing closed loop
    ctx.beginPath();
    pts.forEach(function (p, i) { var n = pts[(i + 1) % pts.length]; if (i === 0) ctx.moveTo(p.x, p.y); ctx.lineTo(n.x, n.y); });
    ctx.closePath();
    ctx.strokeStyle = 'rgba(90,160,255,.4)'; ctx.lineWidth = 1.5 * DPR; ctx.shadowBlur = 14 * DPR; ctx.shadowColor = 'rgba(70,140,255,.6)'; ctx.stroke(); ctx.shadowBlur = 0;
    // travelling comet (continuous cycle)
    var N = pts.length, seg = (t * 0.18) % N, i0 = Math.floor(seg), f = seg - i0, a = pts[i0], b = pts[(i0 + 1) % N];
    var px = a.x + (b.x - a.x) * f, py = a.y + (b.y - a.y) * f;
    var g = ctx.createRadialGradient(px, py, 0, px, py, 16 * DPR); g.addColorStop(0, 'rgba(180,220,255,.95)'); g.addColorStop(1, 'rgba(90,160,255,0)');
    ctx.beginPath(); ctx.arc(px, py, 16 * DPR, 0, 7); ctx.fillStyle = g; ctx.fill();
    ctx.beginPath(); ctx.arc(px, py, 3 * DPR, 0, 7); ctx.fillStyle = '#eaf4ff'; ctx.fill();
    requestAnimationFrame(frame);
  }
  function start() { if (!running && !MQ.matches) { running = true; requestAnimationFrame(frame); } }
  (MQ.addEventListener ? MQ.addEventListener('change', start) : MQ.addListener(start));
  start();
})();
