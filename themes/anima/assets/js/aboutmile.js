/* =============================================================================
   About Us — "Our Milestone" horizontal journey (markup in about.php, styles in
   pages.css).
   Option B interaction: the section is ~one screen tall, so vertical scroll flows
   straight down (it no longer eats ~10 screens). The timeline only travels sideways
   when the cursor HOVERS a photo — the centred photo zooms a touch and the years
   auto-pan toward the end; moving off a photo pauses it. Clicking a year on the
   rail jumps there. Disabled at <=860px and under prefers-reduced-motion (static
   vertical stack).
   ========================================================================== */
(function () {
  var wrap = document.getElementById('mjWrap');
  if (!wrap) return;
  var track = document.getElementById('mjTrack');
  var rail = document.getElementById('mjRail');
  var fill = document.getElementById('mjFill');
  var dot = document.getElementById('mjDot');
  var stage = wrap.querySelector('.mj-stage');
  if (!track || !rail || !stage) return;
  var slides = [].slice.call(track.querySelectorAll('.mj-slide'));
  var yrs = [].slice.call(rail.querySelectorAll('.mj-yr'));
  var imgs = slides.map(function (s) { return s.querySelector('img'); });
  if (!slides.length) return;

  var MQ = window.matchMedia('(max-width:860px), (prefers-reduced-motion:reduce)');
  var clamp = function (v, a, b) { return v < a ? a : (v > b ? b : v); };
  var N = slides.length;
  var p = 0, target = 0, hovering = false, running = false;
  var SPEED = 1 / Math.max(1, (N - 1)) / 90;   // ~ full sweep in a few seconds, scales with year count

  function render() {
    var stageW = track.parentElement.clientWidth;
    var sw = slides[0].offsetWidth, gap = 60, stride = sw + gap;
    var pos = p * (N - 1);
    track.style.transform = 'translateX(' + (stageW / 2 - (pos * stride + sw / 2)).toFixed(1) + 'px)';
    slides.forEach(function (s, i) {
      var d = Math.min(Math.abs(i - pos), 1.4);
      s.style.opacity = (1 - d * 0.52).toFixed(3);
      s.style.transform = 'scale(' + (1 - Math.min(d, 1) * 0.12).toFixed(3) + ')';
      if (imgs[i]) imgs[i].style.transform = 'translateX(' + ((i - pos) * -26).toFixed(1) + 'px)';
    });
    var fr = pos / (N - 1 || 1);
    if (fill) fill.style.width = (fr * 100) + '%';
    if (dot) { var r = rail.getBoundingClientRect(); dot.style.left = (fr * r.width) + 'px'; }
    var act = Math.round(pos);
    yrs.forEach(function (y, i) { y.classList.toggle('on', i === act); y.classList.toggle('past', i < act); });
    slides.forEach(function (s, i) {
      s.classList.toggle('is-on', i === act);
      s.classList.toggle('zoom', hovering && i === act);   // zoom the centred photo while hovering
    });
  }

  function loop() {
    if (MQ.matches) { running = false; return; }
    if (hovering && target < 1) target = Math.min(1, target + SPEED);   // auto-pan toward the end
    p += (target - p) * 0.09;
    if (Math.abs(target - p) < 0.0004) p = target;
    render();
    requestAnimationFrame(loop);
  }
  function start() { if (!running && !MQ.matches) { running = true; requestAnimationFrame(loop); } }

  // Hover is "active" only while the pointer is over a photo (.mj-media) — so the
  // non-photo area leaves vertical scrolling untouched, exactly as asked.
  stage.addEventListener('mousemove', function (e) {
    hovering = !!(e.target.closest && e.target.closest('.mj-media'));
  });
  stage.addEventListener('mouseleave', function () { hovering = false; });

  // Click a year on the rail to jump straight to it.
  yrs.forEach(function (y, i) {
    y.style.cursor = 'pointer';
    y.addEventListener('click', function () { target = clamp(i / (N - 1 || 1), 0, 1); });
  });

  (MQ.addEventListener ? MQ.addEventListener('change', start) : MQ.addListener(start));
  start();
})();
