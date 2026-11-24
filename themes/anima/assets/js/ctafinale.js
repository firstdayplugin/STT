/* =============================================================================
   Pre-footer CTA "Finale" (light) — markup in the page, styles in pages.css.
   The headline (data-text, [word] = gradient accent) splits into words that rise
   + sharpen in, staggered, when the band scrolls into view; a shine sweeps the
   accent, the subtext and buttons follow, soft particles drift. Light so it
   meets the footer's white band seamlessly. Static under reduced-motion.
   ========================================================================== */
(function () {
  var sec = document.querySelector('.cf');
  if (!sec) return;
  var h = sec.querySelector('.cf-h');
  if (h && h.getAttribute('data-text')) {
    // Split into word spans; a [ ... ] span (one or many words) becomes the gradient
    // accent and its brackets are stripped. Backward-compatible with single-word [x].
    var raw = h.getAttribute('data-text'), out = [];
    var push = function (str, grad) {
      str.split(/\s+/).forEach(function (w) {
        if (!w) return;
        out.push('<span class="w">' + (grad ? '<span class="g">' + w + '</span>' : w) + '</span>');
      });
    };
    var gi = raw.indexOf('['), gj = raw.indexOf(']');
    if (gi !== -1 && gj !== -1 && gj > gi) {
      push(raw.slice(0, gi), false); push(raw.slice(gi + 1, gj), true); push(raw.slice(gj + 1), false);
    } else { push(raw, false); }
    h.innerHTML = out.join(' ');
    [].slice.call(h.querySelectorAll('.w')).forEach(function (w, i) { w.style.transitionDelay = (0.12 + i * 0.08) + 's'; });
  }

  var MQ = window.matchMedia('(prefers-reduced-motion:reduce)');
  if (MQ.matches) { sec.classList.add('lit'); }
  else {
    var check = function () {
      var r = sec.getBoundingClientRect();
      if (r.top < innerHeight * 0.8 && r.bottom > 0) sec.classList.add('lit');
      else if (r.top > innerHeight) sec.classList.remove('lit');
    };
    addEventListener('scroll', check, { passive: true }); check();
  }

  // soft drifting particles
  var cv = sec.querySelector('.cf-bg');
  if (cv && cv.getContext) {
    var ctx = cv.getContext('2d'), DPR = Math.min(devicePixelRatio || 1, 2), W, H, ps = [];
    var rz = function () { W = cv.width = sec.offsetWidth * DPR; H = cv.height = sec.offsetHeight * DPR; };
    rz(); addEventListener('resize', rz);
    for (var i = 0; i < 46; i++) ps.push({ x: Math.random(), y: Math.random(), r: (Math.random() * 2 + 0.6) * DPR, s: Math.random() * 0.0004 + 0.0001, a: Math.random() * 0.25 + 0.06 });
    (function loop() {
      if (MQ.matches) { ctx.clearRect(0, 0, W, H); return; }
      ctx.clearRect(0, 0, W, H);
      ps.forEach(function (p) { p.y -= p.s; if (p.y < 0) p.y = 1; ctx.beginPath(); ctx.arc(p.x * W, p.y * H, p.r, 0, 7); ctx.fillStyle = 'rgba(47,123,224,' + p.a + ')'; ctx.fill(); });
      requestAnimationFrame(loop);
    })();
  }
})();
