/* =============================================================
   REKLAMENESIA THEME — interaction & animation engine
   Vanilla JS. Lenis smooth-scroll is progressive enhancement.
   ============================================================= */
(function () {
  'use strict';

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- 1. Smooth scroll (Lenis if present, else native) ---------- */
  function initSmoothScroll() {
    if (reduced || typeof window.Lenis === 'undefined') return;
    try {
      var lenis = new window.Lenis({
        duration: 1.1,
        easing: function (t) { return Math.min(1, 1.001 - Math.pow(2, -10 * t)); },
        smoothWheel: true,
        wheelMultiplier: 1,
        touchMultiplier: 1.6
      });
      function raf(time) { lenis.raf(time); requestAnimationFrame(raf); }
      requestAnimationFrame(raf);
      window.__lenis = lenis;
      // anchor links
      document.querySelectorAll('a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
          var id = a.getAttribute('href');
          if (id.length > 1) { var t = document.querySelector(id); if (t) { e.preventDefault(); lenis.scrollTo(t, { offset: -90 }); } }
        });
      });
    } catch (e) { /* fail silently → native scroll */ }
  }

  /* ---------- 2. Scroll reveals (IntersectionObserver) ---------- */
  function initReveals() {
    var els = document.querySelectorAll('[data-reveal],[data-stagger]');
    if (!els.length) return;
    if (reduced || !('IntersectionObserver' in window)) {
      els.forEach(function (el) { el.classList.add('in'); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
    els.forEach(function (el) { io.observe(el); });
  }

  /* ---------- 3. Navbar scrolled state ---------- */
  function initNavbar() {
    var nav = document.querySelector('.nav');
    if (!nav || nav.classList.contains('on-light')) return; // inner pages stay solid
    var onScroll = function () { nav.classList.toggle('scrolled', window.scrollY > 40); };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  /* ---------- 4. Mobile nav ---------- */
  window.rkOpenMenu = function () {
    var n = document.getElementById('m-nav'), o = document.getElementById('m-overlay');
    if (n) n.classList.add('open'); if (o) o.classList.add('open');
    document.body.style.overflow = 'hidden';
  };
  window.rkCloseMenu = function () {
    var n = document.getElementById('m-nav'), o = document.getElementById('m-overlay');
    if (n) n.classList.remove('open'); if (o) o.classList.remove('open');
    document.body.style.overflow = '';
  };

  /* ---------- 5. Seamless marquees (duplicate track once) ---------- */
  function initMarquees() {
    document.querySelectorAll('.marquee-track').forEach(function (track) {
      if (track.dataset.cloned) return;
      track.dataset.cloned = '1';
      var kids = Array.prototype.slice.call(track.children);
      kids.forEach(function (k) {
        var c = k.cloneNode(true); c.setAttribute('aria-hidden', 'true'); track.appendChild(c);
      });
    });
  }

  /* ---------- 6. Testimonial carousel (center-featured, looped) ---------- */
  function initTestimonials() {
    document.querySelectorAll('[data-testi]').forEach(function (root) {
      var track = root.querySelector('.testi-track');
      var slides = root.querySelectorAll('.testi-slide');
      var prev = root.querySelector('[data-testi-prev]');
      var next = root.querySelector('[data-testi-next]');
      var dotsWrap = root.querySelector('.testi-dots');
      var total = slides.length;
      if (!track || total === 0) return;
      var cur = 0, timer = null;
      var autoplay = root.dataset.autoplay === '1';
      var speed = parseInt(root.dataset.speed || '6000', 10);

      // build dots
      if (dotsWrap) {
        for (var i = 0; i < total; i++) {
          var d = document.createElement('button');
          d.type = 'button'; d.className = 'testi-dot' + (i === 0 ? ' active' : '');
          d.setAttribute('aria-label', 'Slide ' + (i + 1));
          (function (idx) { d.addEventListener('click', function () { go(idx); restart(); }); })(i);
          dotsWrap.appendChild(d);
        }
      }
      function go(i) {
        cur = (i % total + total) % total;
        track.style.transform = 'translateX(' + (-cur * 100) + '%)';
        if (dotsWrap) dotsWrap.querySelectorAll('.testi-dot').forEach(function (dt, k) { dt.classList.toggle('active', k === cur); });
      }
      function start() { if (autoplay && total > 1 && !reduced) timer = setInterval(function () { go(cur + 1); }, speed); }
      function stop() { if (timer) clearInterval(timer); }
      function restart() { stop(); start(); }
      if (prev) prev.addEventListener('click', function () { go(cur - 1); restart(); });
      if (next) next.addEventListener('click', function () { go(cur + 1); restart(); });
      root.addEventListener('mouseenter', stop);
      root.addEventListener('mouseleave', start);
      // swipe
      var sx = 0, sw = false;
      var vp = root.querySelector('.testi-viewport');
      if (vp) {
        vp.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; sw = true; stop(); }, { passive: true });
        vp.addEventListener('touchend', function (e) {
          if (!sw) return; sw = false;
          var dx = e.changedTouches[0].clientX - sx;
          if (Math.abs(dx) > 45) go(cur + (dx < 0 ? 1 : -1));
          start();
        });
      }
      go(0); start();
    });
  }

  /* ---------- 7. FAQ accordion ---------- */
  function initFaq() {
    document.querySelectorAll('.faq-item').forEach(function (item) {
      var q = item.querySelector('.faq-q');
      if (!q) return;
      q.addEventListener('click', function () {
        var open = item.classList.contains('active');
        var list = item.closest('.faq-list');
        if (list) list.querySelectorAll('.faq-item').forEach(function (i) { i.classList.remove('active'); });
        if (!open) item.classList.add('active');
      });
    });
  }

  /* ---------- 8. Product gallery thumbnails ---------- */
  window.rkProdThumb = function (el, src) {
    var main = document.getElementById('prod-main-img');
    if (main) main.src = src;
    document.querySelectorAll('.prod-thumb').forEach(function (t) { t.classList.remove('active'); });
    el.classList.add('active');
  };

  /* ---------- 9. Stat counters (animate 0 → target on scroll, once) ---------- */
  function initCounters() {
    var els = document.querySelectorAll('.rk-count');
    if (!els.length) return;
    function fmt(n) { return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
    function run(el) {
      var to = parseInt(el.dataset.to || '0', 10);
      var pre = el.dataset.prefix || '', suf = el.dataset.suffix || '';
      if (reduced || to === 0) { el.textContent = pre + fmt(to) + suf; return; }
      var dur = 1600, start = null;
      function step(ts) {
        if (start === null) start = ts;
        var p = Math.min((ts - start) / dur, 1);
        var eased = 1 - Math.pow(1 - p, 3); // easeOutCubic
        el.textContent = pre + fmt(Math.round(eased * to)) + suf;
        if (p < 1) requestAnimationFrame(step);
        else el.textContent = pre + fmt(to) + suf;
      }
      requestAnimationFrame(step);
    }
    if (!('IntersectionObserver' in window)) { els.forEach(run); return; }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) { run(en.target); io.unobserve(en.target); } });
    }, { threshold: 0.5 });
    els.forEach(function (el) { io.observe(el); });
  }

  /* ---------- boot ---------- */
  function boot() {
    initSmoothScroll();
    initReveals();
    initNavbar();
    initMarquees();
    initTestimonials();
    initFaq();
    initCounters();
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
  else boot();
})();
