/* =============================================================================
   About Us — "Our Value" ICARE constellation (markup in about.php, styles in
   pages.css). Dark, scroll-scrubbed: the ICARE letters (wireframe + corner ticks)
   are readable immediately; as you scroll, each value card "wires in" one-by-one
   through a drawing cable (glowing head) with the active letter lit, then flow
   pulses. Native scroll + eased/trailing progress for a heavy, premium feel.
   Disabled at <=860px and under prefers-reduced-motion (static stack).
   ========================================================================== */
(function () {
  var wrap = document.getElementById('icvWrap');
  if (!wrap) return;
  var cv = document.getElementById('icvNet'); if (!cv) return;
  var x = cv.getContext('2d');
  var inner = wrap.querySelector('.icv-inner');
  var letters = [].slice.call(wrap.querySelectorAll('.icv-ltr'));
  var cards = [].slice.call(wrap.querySelectorAll('.icv-card'));
  if (!letters.length) return;
  var n = Math.min(letters.length, cards.length);
  var DPR = Math.min(2, window.devicePixelRatio || 1);
  var MQ = window.matchMedia('(max-width:860px), (prefers-reduced-motion:reduce)');
  var clamp = function (v, a, b) { return v < a ? a : (v > b ? b : v); };
  var ss = function (a, b, v) { var t = clamp((v - a) / (b - a), 0, 1); return t * t * (3 - 2 * t); };
  var drift = letters.map(function () { return { ph: Math.random() * 6.28, sp: 0.4 + Math.random() * 0.5, ax: 5 + Math.random() * 4, ay: 4 + Math.random() * 3, px: 0, py: 0 }; });

  var active = -1;
  function bind(el, i) { el.addEventListener('mouseenter', function () { active = i; }); el.addEventListener('mouseleave', function () { if (active === i) active = -1; }); }
  letters.forEach(bind); cards.forEach(bind);
  var mnx = 0, mny = 0;
  inner.addEventListener('mousemove', function (e) { var r = inner.getBoundingClientRect(); mnx = ((e.clientX - r.left) / r.width - .5) * 2; mny = ((e.clientY - r.top) / r.height - .5) * 2; });
  inner.addEventListener('mouseleave', function () { mnx = 0; mny = 0; });

  function size() { cv.width = cv.clientWidth * DPR; cv.height = cv.clientHeight * DPR; }
  function cen(el) { var b = el.getBoundingClientRect(), r = inner.getBoundingClientRect();
    return { x: (b.left - r.left + b.width / 2) * DPR, y: (b.top - r.top + b.height / 2) * DPR, top: (b.top - r.top) * DPR, bot: (b.top - r.top + b.height) * DPR }; }
  function quad(a, q, b, t) { var it = 1 - t; return { x: it * it * a.x + 2 * it * t * q.x + t * t * b.x, y: it * it * a.y + 2 * it * t * q.y + t * t * b.y }; }

  var p = 0, lastW = 0, lastH = 0, running = false;
  function frame(ts) {
    if (MQ.matches) { running = false; return; }
    var t = ts / 1000;
    if (cv.clientWidth !== lastW || cv.clientHeight !== lastH) { size(); lastW = cv.clientWidth; lastH = cv.clientHeight; }

    var vh = innerHeight, dtop = wrap.offsetTop, H = wrap.offsetHeight;
    var rawP = clamp((scrollY - dtop) / Math.max(1, (H - vh * 2)), 0, 1);
    p += (rawP - p) * 0.08; if (Math.abs(rawP - p) < 0.0004) p = rawP;

    var rv = []; for (var i = 0; i < n; i++) rv[i] = ss(0.08 + i * 0.15, 0.34 + i * 0.15, p);
    var act = -1; for (var i = 0; i < n; i++) { if (rv[i] > 0.03 && rv[i] < 0.985) act = i; }
    if (active >= 0) act = active;

    letters.forEach(function (l, i) { var d = drift[i];
      var tx = mnx * (8 + i * 1.5), ty = mny * 6; d.px += (tx - d.px) * .06; d.py += (ty - d.py) * .06;
      var dx = Math.sin(t * d.sp + d.ph) * d.ax + d.px, dy = Math.cos(t * d.sp * .9 + d.ph) * d.ay + d.py;
      var on = (i === act);
      l.style.transform = 'translate(' + dx.toFixed(1) + 'px,' + dy.toFixed(1) + 'px) scale(' + (on ? 1.07 : 1) + ')';
      l.style.borderColor = on ? 'rgba(125,185,255,.75)' : 'rgba(150,180,235,.28)';
      l.style.boxShadow = on ? '0 0 0 1px rgba(120,180,255,.3),0 0 30px -4px rgba(90,160,255,.6),inset 0 0 22px rgba(90,160,255,.14)' : 'none';
      l.style.color = on ? '#ffffff' : '#cfe0fb';
      var cap = l.querySelector('.cap'); if (cap) cap.style.color = on ? '#9fc2ff' : '#5f7aa6';
    });
    cards.forEach(function (c, i) { var r = rv[i] || 0;
      c.style.opacity = r.toFixed(3);
      c.style.transform = 'translateY(' + (30 * (1 - r)).toFixed(1) + 'px)';
      c.classList.toggle('on', (i === act) || r > 0.6);
    });

    var W = cv.width, Hh = cv.height; x.clearRect(0, 0, W, Hh);
    var L = letters.map(cen), C = cards.map(cen);
    x.globalAlpha = 0.42; x.strokeStyle = 'rgba(150,185,255,0.5)'; x.lineWidth = 1.1 * DPR; x.beginPath();
    for (var i = 0; i < L.length; i++) { if (i === 0) x.moveTo(L[i].x, L[i].y); else x.lineTo(L[i].x, L[i].y); } x.stroke();
    for (var i = 0; i < L.length - 1; i++) { var a = L[i], b = L[i + 1], tt = ((t * 0.22 + i * 0.2) % 1), px = a.x + (b.x - a.x) * tt, py = a.y + (b.y - a.y) * tt;
      x.globalAlpha = (1 - tt) * 0.8; x.beginPath(); x.arc(px, py, 2 * DPR, 0, 6.28); x.fillStyle = 'rgba(120,190,255,1)'; x.shadowColor = 'rgba(90,160,255,.9)'; x.shadowBlur = 8 * DPR; x.fill(); x.shadowBlur = 0; }

    for (var i = 0; i < n; i++) { var r = rv[i]; if (r <= 0.01) continue; var hot = (i === act);
      var a = { x: L[i].x, y: L[i].bot }, b = { x: C[i].x, y: C[i].top }, q = { x: (a.x + b.x) / 2, y: (a.y + b.y) / 2 };
      x.globalAlpha = hot ? 0.95 : 0.5; x.strokeStyle = hot ? 'rgba(120,185,255,0.95)' : 'rgba(150,185,255,0.55)'; x.lineWidth = (hot ? 2 : 1.3) * DPR;
      if (hot) { x.shadowColor = 'rgba(90,160,255,.6)'; x.shadowBlur = 12 * DPR; }
      var N = 26, lim = Math.max(1, Math.floor(N * r)); x.beginPath();
      for (var s = 0; s <= lim; s++) { var pt = quad(a, q, b, s / N); if (s === 0) x.moveTo(pt.x, pt.y); else x.lineTo(pt.x, pt.y); } x.stroke(); x.shadowBlur = 0;
      if (r < 0.99) { var hp = quad(a, q, b, r); x.globalAlpha = 1; x.beginPath(); x.arc(hp.x, hp.y, 3 * DPR, 0, 6.28);
        x.fillStyle = 'rgba(120,235,210,1)'; x.shadowColor = 'rgba(90,230,195,.95)'; x.shadowBlur = 11 * DPR; x.fill(); x.shadowBlur = 0; }
      else { var pn = hot ? 3 : 1; for (var k = 0; k < pn; k++) { var pt2 = ((t * (hot ? .5 : .3) + i * 0.17 + k / pn) % 1), pp = quad(a, q, b, pt2);
        x.globalAlpha = (1 - pt2) * 0.9; x.beginPath(); x.arc(pp.x, pp.y, (hot ? 3 : 2.2) * DPR, 0, 6.28);
        x.fillStyle = 'rgba(120,235,210,1)'; x.shadowColor = 'rgba(90,230,195,.85)'; x.shadowBlur = 9 * DPR; x.fill(); x.shadowBlur = 0; } }
    }
    x.globalAlpha = 1;
    requestAnimationFrame(frame);
  }
  function start() { if (!running && !MQ.matches) { running = true; size(); requestAnimationFrame(frame); } }
  addEventListener('resize', size);
  (MQ.addEventListener ? MQ.addEventListener('change', start) : MQ.addListener(start));
  start();
})();
