/* =============================================================================
   About Us — "Awards" 3D spotlight coverflow (markup in about.php, styles in
   pages.css).
   Interaction mirrors "Our Milestone": the section is ~one screen tall, so
   vertical scroll flows straight down. The coverflow only travels sideways when
   the cursor HOVERS an award card — it auto-pans toward the oldest year; once it
   reaches the end it stops, and a fresh hover pans it back toward the newest
   (back-and-forth). Moving off a card leaves vertical scrolling untouched. The
   centred card zooms a touch while hovering; org + title crossfade below.
   Disabled at <=860px and under prefers-reduced-motion (static grid). CMS-driven:
   cards carry data-org / data-ttl.
   ========================================================================== */
(function () {
  var wrap = document.getElementById('awWrap');
  if (!wrap) return;
  var stage = document.getElementById('awStage');
  var info = document.getElementById('awInfo');
  if (!stage || !info) return;
  var cards = [].slice.call(stage.querySelectorAll('.aw-card'));
  if (!cards.length) return;
  var infoOrg = info.querySelector('.org'), infoTtl = info.querySelector('.ttl');

  var MQ = window.matchMedia('(max-width:860px), (prefers-reduced-motion:reduce)');
  var clamp = function (v, a, b) { return v < a ? a : (v > b ? b : v); };
  var N = cards.length;
  var p = 0, target = 0, hovering = false, running = false, lastAct = -1;
  var dir = 1, armed = true;   // dir: +1 toward oldest, -1 back toward newest; a fresh hover flips it at each end
  var SPEED = 1 / Math.max(1, (N - 1)) / 90;   // full sweep in a few seconds, scales with the number of awards

  function render() {
    var pos = p * (N - 1);
    cards.forEach(function (c, i) {
      var d = i - pos, ad = Math.abs(d);
      var tx = d * 170, tz = -Math.min(ad, 2.4) * 170, ry = clamp(-d, -1.3, 1.3) * 42, sc = 1 - Math.min(ad, 1) * 0.18;
      var op = 1 - Math.min(ad, 2.4) * 0.34, zi = 100 - Math.round(ad * 10);
      c.style.transform = 'translateX(' + tx.toFixed(1) + 'px) translateZ(' + tz.toFixed(1) + 'px) rotateY(' + ry.toFixed(1) + 'deg) scale(' + sc.toFixed(3) + ')';
      c.style.opacity = op.toFixed(3);
      c.style.zIndex = String(zi);
    });
    var act = Math.round(pos), fract = Math.abs(pos - act);
    info.style.opacity = clamp(1 - fract * 0.9, 0.6, 1).toFixed(3);
    info.style.transform = 'translateY(' + (fract * 6).toFixed(1) + 'px)';
    cards.forEach(function (c, i) { c.classList.toggle('zoom', hovering && i === act); });
    if (act !== lastAct) {
      lastAct = act; var c = cards[act];
      if (c) { infoOrg.textContent = c.getAttribute('data-org') || ''; infoTtl.textContent = c.getAttribute('data-ttl') || ''; }
    }
  }

  function loop() {
    if (MQ.matches) { running = false; return; }
    if (hovering && armed) {                          // auto-pan in the current direction
      target = clamp(target + dir * SPEED, 0, 1);
      if (target >= 1) { target = 1; dir = -1; armed = false; }       // hit the oldest → next hover travels back
      else if (target <= 0) { target = 0; dir = 1; armed = false; }   // hit the newest → next hover travels forward
    }
    p += (target - p) * 0.09;
    if (Math.abs(target - p) < 0.0004) p = target;
    render();
    requestAnimationFrame(loop);
  }
  function start() { if (!running && !MQ.matches) { running = true; requestAnimationFrame(loop); } }

  // Hover is "active" only while the pointer is over an award card — off-card leaves vertical scroll free.
  stage.addEventListener('mousemove', function (e) {
    var h = !!(e.target.closest && e.target.closest('.aw-card'));
    if (!h) armed = true;   // leaving a card re-arms the next sweep, so "hover again" reverses once at an end
    hovering = h;
  });
  stage.addEventListener('mouseleave', function () { hovering = false; armed = true; });

  (MQ.addEventListener ? MQ.addEventListener('change', start) : MQ.addListener(start));
  start();
})();
