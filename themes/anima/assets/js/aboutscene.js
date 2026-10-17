/* =============================================================================
   About Us — dark "cover" scene (markup in about.php, styles in pages.css).
   Pinned, scroll-driven scene that rises over the hero: OUR VISION with a video
   card, then the card shrinks + lifts and OUR MISSION appears as glass cards that
   "wire in" one-by-one, each connected to the card by a glowing cable with
   traveling light pulses (same constellation language as the hero).

   Motion is driven by NATIVE scroll (no hijack); applied progress is eased toward
   the target each frame for a heavy, premium feel. Disabled at <=860px and under
   prefers-reduced-motion, where the section is a static stack.
   ========================================================================== */
(function () {
  var wrap = document.getElementById('abdWrap');
  if (!wrap) return;
  var MQ = window.matchMedia('(max-width:860px), (prefers-reduced-motion:reduce)');

  var clamp = function (v, a, b) { return v < a ? a : (v > b ? b : v); };
  var ss = function (a, b, x) { var t = clamp((x - a) / (b - a), 0, 1); return t * t * (3 - 2 * t); };
  var lerp = function (a, b, t) { return a + (b - a) * t; };

  var dv   = document.getElementById('abdV');
  var card = document.getElementById('abdCard');
  var miss = document.getElementById('abdMiss');
  var netC = document.getElementById('abdNet'), nx = netC ? netC.getContext('2d') : null;
  var artC = document.getElementById('abdCardArt'), ax = artC ? artC.getContext('2d') : null;
  var mcards = [].slice.call(wrap.querySelectorAll('.abd-mcard'));
  var DPR = Math.min(2, window.devicePixelRatio || 1);
  var rev = []; var hovered = -1;
  mcards.forEach(function (c, i) {
    rev[i] = 0;
    c.addEventListener('mouseenter', function () { hovered = i; });
    c.addEventListener('mouseleave', function () { if (hovered === i) hovered = -1; });
  });

  /* ambient node dust */
  var dust = [];
  for (var i = 0; i < 46; i++) dust.push({ x: Math.random(), y: Math.random(), ph: Math.random() * 6.28, sp: 0.3 + Math.random() * 0.6, z: 0.6 + Math.random() * 0.6 });
  /* card contour line-art */
  var ticks = [];
  for (var i = 0; i < 14; i++) ticks.push({ x: Math.random(), y: Math.random(), ph: Math.random() * 6.28, sp: 0.3 + Math.random() * 0.5 });

  function sizeAll() {
    if (!netC) return;
    netC.width = netC.clientWidth * DPR; netC.height = netC.clientHeight * DPR;
    if (artC && card) { artC.width = card.clientWidth * DPR; artC.height = card.clientHeight * DPR; }
  }
  function quad(p0, q, p1, t) { var it = 1 - t; return { x: it * it * p0.x + 2 * it * t * q.x + t * t * p1.x, y: it * it * p0.y + 2 * it * t * q.y + t * t * p1.y }; }

  function drawDust(t, alpha) {
    var W = netC.width, H = netC.height;
    nx.save(); nx.globalAlpha = alpha;
    for (var i = 0; i < dust.length; i++) { var d = dust[i];
      var x = d.x * W + Math.sin(t * d.sp + d.ph) * 10 * DPR * d.z, y = d.y * H + Math.cos(t * d.sp * 0.9 + d.ph) * 9 * DPR * d.z;
      nx.beginPath(); nx.arc(x, y, 1.3 * DPR * d.z, 0, 6.28); nx.fillStyle = 'rgba(150,190,240,.5)'; nx.fill(); }
    nx.restore();
  }

  // Glowing cables + traveling light pulses from the video card to each mission card.
  function drawCables(t) {
    if (!mcards.length) return;
    var h = netC.getBoundingClientRect(), cr = card.getBoundingClientRect();
    var C = { x: (cr.left + cr.width / 2 - h.left) * DPR, y: (cr.top + cr.height / 2 - h.top) * DPR };
    var anyRev = 0;
    nx.save();
    for (var i = 0; i < mcards.length; i++) {
      var a = rev[i]; if (a > anyRev) anyRev = a; if (a <= 0.02) continue;
      var r = mcards[i].getBoundingClientRect();
      var P = { x: (r.left + r.width / 2 - h.left) * DPR, y: (r.top + 8 - h.top) * DPR };
      var mx = (C.x + P.x) / 2, my = (C.y + P.y) / 2, dx = P.x - C.x, dy = P.y - C.y, len = Math.hypot(dx, dy) || 1;
      var off = Math.min(84 * DPR, len * 0.17) * ((i % 2) ? 1 : -1);
      var Q = { x: mx + (-dy / len) * off, y: my + (dx / len) * off };
      var hot = (hovered === i);
      // glowing cable
      nx.globalAlpha = a * (hot ? 0.95 : 0.52);
      nx.strokeStyle = hot ? 'rgba(140,205,255,0.95)' : 'rgba(120,180,240,0.55)';
      nx.lineWidth = (hot ? 1.9 : 1.2) * DPR;
      nx.shadowColor = 'rgba(90,160,255,0.85)'; nx.shadowBlur = (hot ? 16 : 7) * DPR;
      nx.beginPath(); nx.moveTo(C.x, C.y); nx.quadraticCurveTo(Q.x, Q.y, P.x, P.y); nx.stroke();
      nx.shadowBlur = 0;
      // traveling light pulses
      var pn = hot ? 3 : 2;
      for (var k = 0; k < pn; k++) {
        var tt = (t * (hot ? 0.5 : 0.32) + i * 0.13 + k / pn) % 1;
        var pp = quad(C, Q, P, tt);
        nx.globalAlpha = a * (1 - tt) * 0.95;
        nx.beginPath(); nx.arc(pp.x, pp.y, (hot ? 3.2 : 2.3) * DPR, 0, 6.28);
        nx.fillStyle = 'rgba(155,240,218,1)'; nx.shadowColor = 'rgba(120,230,200,0.95)'; nx.shadowBlur = 11 * DPR; nx.fill(); nx.shadowBlur = 0;
      }
      // pulsing endpoint
      nx.globalAlpha = a; var pr = (2.6 + Math.sin(t * 2.2 + i) * 0.8) * DPR;
      nx.beginPath(); nx.arc(P.x, P.y, pr, 0, 6.28); nx.fillStyle = 'rgba(155,220,255,0.95)';
      nx.shadowColor = 'rgba(120,200,255,0.9)'; nx.shadowBlur = 10 * DPR; nx.fill(); nx.shadowBlur = 0;
    }
    // glowing core at the video card (energy source)
    if (anyRev > 0.02) {
      var crr = (10 + Math.sin(t * 2) * 3) * DPR;
      var g = nx.createRadialGradient(C.x, C.y, 0, C.x, C.y, crr * 2.4);
      g.addColorStop(0, 'rgba(120,200,255,' + (0.5 * anyRev).toFixed(3) + ')'); g.addColorStop(1, 'rgba(120,200,255,0)');
      nx.globalAlpha = 1; nx.beginPath(); nx.arc(C.x, C.y, crr * 2.4, 0, 6.28); nx.fillStyle = g; nx.fill();
    }
    nx.restore();
  }

  function drawArt(t, alpha) {
    if (!ax) return; var W = artC.width, H = artC.height; ax.clearRect(0, 0, W, H); if (alpha <= 0.01) return;
    ax.save(); ax.globalAlpha = alpha; ax.strokeStyle = 'rgba(255,255,255,0.5)'; ax.lineWidth = 1.3 * DPR;
    for (var c = 0; c < 2; c++) { ax.beginPath();
      for (var sx = 0; sx <= W; sx += 8 * DPR) { var yy = H * (0.4 + c * 0.16) + Math.sin(sx * 0.012 / DPR + t * 0.8 + c * 2) * 16 * DPR + Math.sin(sx * 0.03 / DPR - t * 0.6) * 6 * DPR;
        if (sx === 0) ax.moveTo(sx, yy); else ax.lineTo(sx, yy); } ax.stroke(); }
    ax.beginPath();
    for (var a2 = 0; a2 <= 6.2832; a2 += 0.26) { var rr = Math.min(W, H) * 0.25 * (1 + 0.18 * Math.sin(3 * a2 + t) + 0.1 * Math.sin(5 * a2 - t * 1.2));
      var bx = W * 0.6 + Math.cos(a2) * rr, by = H * 0.5 + Math.sin(a2) * rr * 0.8; if (a2 === 0) ax.moveTo(bx, by); else ax.lineTo(bx, by); }
    ax.closePath(); ax.strokeStyle = 'rgba(255,255,255,0.45)'; ax.stroke();
    ax.strokeStyle = 'rgba(255,255,255,0.7)'; ax.lineWidth = 1.4 * DPR;
    for (var i2 = 0; i2 < ticks.length; i2++) { var tk = ticks[i2]; var x = tk.x * W + Math.sin(t * tk.sp + tk.ph) * 6 * DPR, y = tk.y * H + Math.cos(t * tk.sp + tk.ph) * 6 * DPR, s = 5 * DPR;
      ax.beginPath(); ax.moveTo(x - s, y); ax.lineTo(x - s, y + s); ax.lineTo(x, y + s); ax.stroke(); }
    ax.restore();
  }

  var p = 0, lastW = 0, lastH = 0, running = false;
  function frame(ts) {
    if (MQ.matches) { running = false; return; }
    var t = ts / 1000;
    if (netC && (netC.clientWidth !== lastW || netC.clientHeight !== lastH)) { sizeAll(); lastW = netC.clientWidth; lastH = netC.clientHeight; }

    var vh = innerHeight, dtop = wrap.offsetTop, H = wrap.offsetHeight;
    var rawP = clamp((window.scrollY - dtop) / Math.max(1, (H - vh * 2)), 0, 1);
    p += (rawP - p) * 0.08; if (Math.abs(rawP - p) < 0.0004) p = rawP;

    // OUR VISION out
    dv.style.opacity = 1 - ss(0.06, 0.3, p);
    dv.style.transform = 'translateX(-50%) translateY(' + (-40 * ss(0.06, 0.34, p)).toFixed(1) + 'px)';

    // card: big+low -> small+high (kept clear of the sticky header), never disappears
    var e = ss(0.12, 0.62, p);
    var oy = lerp(0.14 * vh, -0.28 * vh, e), sc = lerp(1, 0.30, e);
    card.style.transform = 'translate(-50%,-50%) translateY(' + oy.toFixed(1) + 'px) scale(' + sc.toFixed(3) + ')';

    // OUR MISSION block + per-card staggered "wire-in"
    var m = ss(0.42, 0.72, p);
    miss.style.opacity = ss(0.42, 0.54, p);
    miss.style.top = lerp(36, 30, m).toFixed(1) + '%';
    miss.style.transform = 'translateX(-50%) translateY(' + (26 * (1 - m)).toFixed(1) + 'px)';
    miss.style.pointerEvents = p > 0.56 ? 'auto' : 'none';
    for (var ci = 0; ci < mcards.length; ci++) {
      var ct = ss(0.5 + ci * 0.035, 0.74 + ci * 0.035, p); rev[ci] = ct;
      if (ct >= 0.999) { mcards[ci].style.transform = ''; mcards[ci].style.opacity = ''; }  // clear so CSS :hover works
      else { mcards[ci].style.opacity = ct.toFixed(3); mcards[ci].style.transform = 'translateY(' + (24 * (1 - ct)).toFixed(1) + 'px)'; }
    }

    if (nx) {
      nx.clearRect(0, 0, netC.width, netC.height);
      drawDust(t, 0.22 + 0.22 * m);
      drawCables(t);
    }
    drawArt(t, 1 - ss(0.14, 0.44, p));

    requestAnimationFrame(frame);
  }
  function start() { if (!running && !MQ.matches) { running = true; sizeAll(); requestAnimationFrame(frame); } }
  addEventListener('resize', sizeAll);
  (MQ.addEventListener ? MQ.addEventListener('change', start) : MQ.addListener(start));
  start();
})();
