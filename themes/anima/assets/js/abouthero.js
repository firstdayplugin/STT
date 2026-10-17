/* =============================================================================
   About Us hero — scale.com-style constellation.
   Two parent capability cards link to their service-area cards via animated
   connector lines. Each card drifts organically (amoeba-like) and follows the
   cursor (parallax, depth by size); hover zoom is handled in CSS. The connector
   lines are redrawn every frame so they stay glued to the moving cards.
   Disabled below 860px, where the cards fall back to a static grid (CSS).
   ========================================================================== */
(function () {
  var hero = document.getElementById('abhero');
  var svg  = document.getElementById('abhLinks');
  if (!hero || !svg) return;

  var EDGES = [
    ['infra', 'consult'], ['infra', 'deploy'], ['infra', 'managed-infra'],
    ['cyber', 'assess'],  ['cyber', 'risk'],   ['cyber', 'managed-cyber']
  ];

  var boxes = {}, D = {};
  var rnd = function (a, z) { return a + Math.random() * (z - a); };

  document.querySelectorAll('.abh-box').forEach(function (b) {
    var k = b.getAttribute('data-k'); boxes[k] = b;
    var sz = parseFloat(getComputedStyle(b).getPropertyValue('--w')) || 120;
    var depth = (168 - sz) * 0.34 + rnd(-3, 3);   // smaller card => follows cursor more (depth)
    D[k] = {
      ax1: rnd(5, 9), ax2: rnd(3, 6), ay1: rnd(5, 9), ay2: rnd(3, 6),
      wx1: rnd(.14, .24), wx2: rnd(.26, .4), wy1: rnd(.13, .22), wy2: rnd(.25, .38),
      px: Math.random() * 6.28, py: Math.random() * 6.28, pr: Math.random() * 6.28,
      wr: rnd(.1, .18), depth: depth, dxx: rnd(.85, 1.15), dyy: rnd(.7, 1.0),
      px0: 0, py0: 0
    };
  });

  // create persistent path elements (so the CSS flow animation never resets)
  var paths = [];
  EDGES.forEach(function (e, i) {
    var base = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    var flow = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    flow.setAttribute('class', 'flow');
    flow.setAttribute('stroke-dasharray', '6 260');
    flow.style.animation = 'abhDash ' + (3.8 + i * 0.22) + 's linear infinite';
    svg.appendChild(base); svg.appendChild(flow);
    paths.push({ base: base, flow: flow, e: e });
  });

  // cursor position, normalised to [-1,1] from hero centre
  var mnx = 0, mny = 0;
  hero.addEventListener('mousemove', function (ev) {
    var r = hero.getBoundingClientRect();
    mnx = ((ev.clientX - r.left) / r.width - 0.5) * 2;
    mny = ((ev.clientY - r.top) / r.height - 0.5) * 2;
  });
  hero.addEventListener('mouseleave', function () { mnx = 0; mny = 0; });

  function cen(k) {
    var b = boxes[k].querySelector('.abh-frame').getBoundingClientRect(), h = hero.getBoundingClientRect();
    return { x: b.left - h.left + b.width / 2, y: b.top - h.top + b.height / 2, r: Math.max(b.width, b.height) / 2 };
  }
  function edgePt(a, b) {
    var dx = b.x - a.x, dy = b.y - a.y, d = Math.hypot(dx, dy) || 1;
    return { x: a.x + dx / d * (a.r + 5), y: a.y + dy / d * (a.r + 5) };
  }

  var mq = window.matchMedia('(max-width:860px)');
  var running = false;

  function frame(ts) {
    if (mq.matches) { running = false; return; }   // mobile: static grid, stop loop
    var t = ts / 1000;
    for (var k in boxes) {
      var d = D[k];
      var dx = d.ax1 * Math.sin(t * d.wx1 + d.px) + d.ax2 * Math.sin(t * d.wx2 + d.px * 1.7);
      var dy = d.ay1 * Math.cos(t * d.wy1 + d.py) + d.ay2 * Math.sin(t * d.wy2 + d.py * 1.3);
      var tx = mnx * d.depth * d.dxx, ty = mny * d.depth * d.dyy;
      d.px0 += (tx - d.px0) * 0.06; d.py0 += (ty - d.py0) * 0.06;   // lerp for buttery motion
      var rot = Math.sin(t * d.wr + d.pr) * 0.5 + d.px0 * 0.015;
      boxes[k].style.transform = 'translate(' + (dx + d.px0).toFixed(2) + 'px,' + (dy + d.py0).toFixed(2) + 'px) rotate(' + rot.toFixed(2) + 'deg)';
    }
    var w = hero.clientWidth, h = hero.clientHeight;
    svg.setAttribute('viewBox', '0 0 ' + w + ' ' + h);
    paths.forEach(function (P, i) {
      if (!boxes[P.e[0]] || !boxes[P.e[1]]) return;
      var A = cen(P.e[0]), B = cen(P.e[1]);
      var p1 = edgePt(A, B), p2 = edgePt(B, A);
      var mx = (p1.x + p2.x) / 2, my = (p1.y + p2.y) / 2,
          ddx = p2.x - p1.x, ddy = p2.y - p1.y, len = Math.hypot(ddx, ddy) || 1;
      var off = Math.min(58, len * 0.12) * (i % 2 ? 1 : -1);
      var cx = mx + (-ddy / len) * off, cy = my + (ddx / len) * off;
      var ds = 'M ' + p1.x.toFixed(1) + ' ' + p1.y.toFixed(1) + ' Q ' + cx.toFixed(1) + ' ' + cy.toFixed(1) + ' ' + p2.x.toFixed(1) + ' ' + p2.y.toFixed(1);
      P.base.setAttribute('d', ds); P.flow.setAttribute('d', ds);
    });
    requestAnimationFrame(frame);
  }
  function start() { if (!running && !mq.matches) { running = true; requestAnimationFrame(frame); } }
  (mq.addEventListener ? mq.addEventListener('change', start) : mq.addListener(start));
  start();
})();
