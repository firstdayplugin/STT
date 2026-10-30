/* =============================================================================
   About Us — "Our Milestone" horizontal journey (markup in about.php, styles in
   pages.css). Light, scroll-scrubbed: vertical scroll travels the timeline
   sideways through the years. The centred year is in focus (huge year + bullets
   + photo); neighbours dim + parallax; a progress rail fills with a glowing head
   and the active year node lights. Native scroll + heavy eased/trailing progress.
   Disabled at <=860px and under prefers-reduced-motion (static vertical stack).
   ========================================================================== */
(function () {
  var wrap = document.getElementById('mjWrap');
  if (!wrap) return;
  var track = document.getElementById('mjTrack');
  var rail = document.getElementById('mjRail');
  var fill = document.getElementById('mjFill');
  var dot = document.getElementById('mjDot');
  if (!track || !rail) return;
  var slides = [].slice.call(track.querySelectorAll('.mj-slide'));
  var yrs = [].slice.call(rail.querySelectorAll('.mj-yr'));
  var imgs = slides.map(function (s) { return s.querySelector('img'); });
  if (!slides.length) return;

  var MQ = window.matchMedia('(max-width:860px), (prefers-reduced-motion:reduce)');
  var clamp = function (v, a, b) { return v < a ? a : (v > b ? b : v); };
  var p = 0, running = false;

  function frame() {
    if (MQ.matches) { running = false; return; }
    var vh = innerHeight, dtop = wrap.offsetTop, H = wrap.offsetHeight;
    var rawP = clamp((scrollY - dtop) / Math.max(1, (H - vh * 2)), 0, 1);
    p += (rawP - p) * 0.048; if (Math.abs(rawP - p) < 0.0003) p = rawP;   // heavy trailing

    var N = slides.length, pos = p * (N - 1);
    var stageW = track.parentElement.clientWidth;
    var sw = slides[0].offsetWidth, gap = 60, stride = sw + gap;
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
    slides.forEach(function (s, i) { s.classList.toggle('is-on', i === act); });   // drives the year bar-draw + underline sweep

    requestAnimationFrame(frame);
  }
  function start() { if (!running && !MQ.matches) { running = true; requestAnimationFrame(frame); } }
  (MQ.addEventListener ? MQ.addEventListener('change', start) : MQ.addListener(start));
  start();
})();
