/* =============================================================================
   About Us — dark "cover" scene (markup in about.php, styles in pages.css).
   A pinned, scroll-driven scene that rises over the hero, shows OUR VISION with a
   video card, then the card shrinks + lifts and OUR MISSION appears as glass cards
   wired to the card by flowing cables (same constellation language as the hero).

   Motion is driven by NATIVE scroll (no scroll hijacking) but the applied progress
   is eased toward the target each frame, so everything trails the scroll with a
   heavy, premium feel. Disabled at <=860px, where the section is a static stack.
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

  /* ambient node dust */
  var dust = [];
  (function () { for (var i = 0; i < 46; i++) dust.push({ x: Math.random(), y: Math.random(), ph: Math.random() * 6.28, sp: 0.3 + Math.random() * 0.6, z: 0.6 + Math.random() * 0.6 }); })();

  /* card contour line-art */
  var ticks = [];
  (function () { for (var i = 0; i < 14; i++) ticks.push({ x: Math.random(), y: Math.random(), ph: Math.random() * 6.28, sp: 0.3 + Math.random() * 0.5 }); })();

  function sizeAll() {
    if (!netC) return;
    netC.width = netC.clientWidth * DPR; netC.height = netC.clientHeight * DPR;
    if (artC && card) { artC.width = card.clientWidth * DPR; artC.height = card.clientHeight * DPR; }
  }

  function drawDust(t, alpha) {
    var W = netC.width, H = netC.height;
    nx.save(); nx.globalAlpha = alpha;
    for (var i = 0; i < dust.length; i++) { var d = dust[i];
      var x = (d.x * W) + Math.sin(t * d.sp + d.ph) * 10 * DPR * d.z, y = (d.y * H) + Math.cos(t * d.sp * 0.9 + d.ph) * 9 * DPR * d.z;
      nx.beginPath(); nx.arc(x, y, 1.3 * DPR * d.z, 0, 6.28); nx.fillStyle = 'rgba(150,190,240,.5)'; nx.fill(); }
    nx.restore();
  }
  function drawCables(t, alpha) {
    if (alpha <= 0.01 || !mcards.length) return;
    var h = netC.getBoundingClientRect(), cr = card.getBoundingClientRect();
    var cx = (cr.left + cr.width / 2 - h.left) * DPR, cy = (cr.top + cr.height / 2 - h.top) * DPR;
    nx.save(); nx.globalAlpha = alpha; nx.lineWidth = 1.1 * DPR;
    for (var i = 0; i < mcards.length; i++) {
      var r = mcards[i].getBoundingClientRect();
      var tx = (r.left + r.width / 2 - h.left) * DPR, ty = (r.top + 8 - h.top) * DPR;
      var mx = (cx + tx) / 2, my = (cy + ty) / 2, dx = tx - cx, dy = ty - cy, len = Math.hypot(dx, dy) || 1;
      var off = Math.min(70 * DPR, len * 0.16) * ((i % 2) ? 1 : -1);
      var qx = mx + (-dy / len) * off, qy = my + (dx / len) * off;
      nx.strokeStyle = 'rgba(150,190,240,0.28)';
      nx.setLineDash([5 * DPR, 9 * DPR]); nx.lineDashOffset = -(t * 24 * DPR) % (14 * DPR);
      nx.beginPath(); nx.moveTo(cx, cy); nx.quadraticCurveTo(qx, qy, tx, ty); nx.stroke();
      nx.setLineDash([]);
      nx.beginPath(); nx.arc(tx, ty, 2.2 * DPR, 0, 6.28); nx.fillStyle = 'rgba(120,230,200,.9)'; nx.fill();
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
    for (var a = 0; a <= 6.2832; a += 0.26) { var rr = Math.min(W, H) * 0.25 * (1 + 0.18 * Math.sin(3 * a + t) + 0.1 * Math.sin(5 * a - t * 1.2));
      var bx = W * 0.6 + Math.cos(a) * rr, by = H * 0.5 + Math.sin(a) * rr * 0.8; if (a === 0) ax.moveTo(bx, by); else ax.lineTo(bx, by); }
    ax.closePath(); ax.strokeStyle = 'rgba(255,255,255,0.45)'; ax.stroke();
    ax.strokeStyle = 'rgba(255,255,255,0.7)'; ax.lineWidth = 1.4 * DPR;
    for (var i = 0; i < ticks.length; i++) { var tk = ticks[i]; var x = tk.x * W + Math.sin(t * tk.sp + tk.ph) * 6 * DPR, y = tk.y * H + Math.cos(t * tk.sp + tk.ph) * 6 * DPR, s = 5 * DPR;
      ax.beginPath(); ax.moveTo(x - s, y); ax.lineTo(x - s, y + s); ax.lineTo(x, y + s); ax.stroke(); }
    ax.restore();
  }

  var p = 0, lastW = 0, lastH = 0, running = false;
  function frame(ts) {
    if (MQ.matches) { running = false; return; }   // mobile: static stack
    var t = ts / 1000;
    if (netC && (netC.clientWidth !== lastW || netC.clientHeight !== lastH)) { sizeAll(); lastW = netC.clientWidth; lastH = netC.clientHeight; }

    var vh = innerHeight, dtop = wrap.offsetTop, H = wrap.offsetHeight;
    var rawP = clamp((window.scrollY - dtop) / Math.max(1, (H - vh * 2)), 0, 1);
    p += (rawP - p) * 0.08;                          // eased trailing => heavy/premium
    if (Math.abs(rawP - p) < 0.0004) p = rawP;

    var v1 = 1 - ss(0.06, 0.3, p);
    dv.style.opacity = v1; dv.style.transform = 'translateX(-50%) translateY(' + (-40 * ss(0.06, 0.34, p)).toFixed(1) + 'px)';

    var e = ss(0.12, 0.62, p);
    var oy = lerp(0.14 * vh, -0.36 * vh, e), sc = lerp(1, 0.34, e);
    card.style.transform = 'translate(-50%,-50%) translateY(' + oy.toFixed(1) + 'px) scale(' + sc.toFixed(3) + ')';

    var m = ss(0.42, 0.72, p);
    miss.style.opacity = m;
    miss.style.top = lerp(30, 22, m).toFixed(1) + '%';
    miss.style.transform = 'translateX(-50%) translateY(' + (30 * (1 - m)).toFixed(1) + 'px)';
    miss.style.pointerEvents = m > 0.5 ? 'auto' : 'none';

    if (nx) {
      nx.clearRect(0, 0, netC.width, netC.height);
      drawDust(t, 0.22 + 0.22 * m);
      drawCables(t, m);
    }
    drawArt(t, 1 - ss(0.14, 0.44, p));

    requestAnimationFrame(frame);
  }
  function start() { if (!running && !MQ.matches) { running = true; sizeAll(); requestAnimationFrame(frame); } }
  addEventListener('resize', sizeAll);
  (MQ.addEventListener ? MQ.addEventListener('change', start) : MQ.addListener(start));
  start();
})();
