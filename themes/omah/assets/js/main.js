/* =============================================================
   OMAH THEME — interaction & animation engine
   Mirrors Framer difaproperty.framer.website exactly:
   - data-nce-scroll: opacity:0 + translateY(40px) → in
   - Word-by-word h2 reveal
   - Property cards scale(0.8) → scale(1)
   - Navbar: transparent over hero, solid on scroll
   - FAQ accordion
   - Counter animation
   - Easing: cubic-bezier(.44,0,.56,1)
   ============================================================= */
(function () {
  'use strict';

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var EASE = 'cubic-bezier(.44,0,.56,1)';

  /* ---- 1. Navbar scroll behavior ---- */
  function initNavbar() {
    var nav = document.querySelector('#om-nav');
    if (!nav) return;
    var isHome = nav.getAttribute('data-is-home') === '1';

    if (!isHome) {
      // Inner pages: always solid, always dark. Done.
      return;
    }

    // Homepage only: nav starts solid/dark.
    // When scrolled INTO the dark hero card, switch to transparent/light.
    // When scrolled back out, switch back to solid/dark.
    var heroCard = document.querySelector('.hero-card');
    if (!heroCard) return;

    function applyNavState() {
      var heroRect = heroCard.getBoundingClientRect();
      // User is inside hero if hero top is above nav bottom
      var insideHero = heroRect.top < -(heroRect.height * 0.05);

      if (insideHero) {
        // Dark hero behind nav: white text
        nav.classList.remove('nav-solid');
        nav.classList.add('nav-in-hero');
        nav.querySelector('.logo-dark').style.display  = 'none';
        nav.querySelector('.logo-light').style.display = 'block';
      } else {
        // White page behind nav: dark text
        nav.classList.add('nav-solid');
        nav.classList.remove('nav-in-hero');
        nav.querySelector('.logo-dark').style.display  = 'block';
        nav.querySelector('.logo-light').style.display = 'none';
      }
    }

    applyNavState();
    window.addEventListener('scroll', applyNavState, { passive: true });
  }

  /* ---- 2. Scroll reveals (Framer NCE pattern) ----
     All elements with data-nce-scroll="true" start at opacity:0
     translateY(40px) and animate in via IntersectionObserver.
     Also handles [data-reveal], [data-stagger], .word-anim         */
  function initReveals() {
    if (reduced) {
      // Show everything immediately for reduced-motion
      document.querySelectorAll(
        '[data-nce-scroll],[data-reveal],[data-stagger],[data-hero],.word-anim span'
      ).forEach(function(el) {
        el.classList.add('in', 'nce-in');
      });
      return;
    }
    if (!('IntersectionObserver' in window)) {
      document.querySelectorAll('[data-nce-scroll],[data-reveal],[data-stagger],[data-hero]').forEach(function(el) {
        el.classList.add('in', 'nce-in');
      });
      return;
    }

    var io = new IntersectionObserver(function(entries) {
      entries.forEach(function(en) {
        if (en.isIntersecting) {
          en.target.classList.add('in', 'nce-in');
          io.unobserve(en.target);
        }
      });
    }, { threshold: 0.10, rootMargin: '0px 0px -6% 0px' });

    // NCE scroll elements (from Framer export)
    document.querySelectorAll('[data-nce-scroll="true"]').forEach(function(el) {
      io.observe(el);
    });
    // Standard reveal
    document.querySelectorAll('[data-reveal],[data-stagger]').forEach(function(el) {
      io.observe(el);
    });
    // Property cards (scale reveal)
    document.querySelectorAll('[data-prop-card]').forEach(function(el) {
      io.observe(el);
    });
  }

  /* ---- 3. Hero entrance (fires immediately on page load) ---- */
  function initHeroEntrance() {
    var els = document.querySelectorAll('[data-hero]');
    if (!els.length) return;
    // Short delay then animate in
    setTimeout(function() {
      els.forEach(function(el) { el.classList.add('in'); });
    }, 80);
  }

  /* ---- 4. Word-by-word h2 animation (Framer style) ---- */
  function initWordAnims() {
    document.querySelectorAll('.word-anim').forEach(function(h2) {
      // Wrap each word in a span if not already wrapped
      if (!h2.querySelector('span')) {
        var words = h2.textContent.trim().split(/\s+/);
        h2.innerHTML = words.map(function(w) {
          return '<span style="display:inline-block">' + w + '</span>';
        }).join(' ');
      }
      // Observe
      if (reduced) { h2.classList.add('in'); return; }
      var io2 = new IntersectionObserver(function(entries) {
        entries.forEach(function(en) {
          if (en.isIntersecting) { en.target.classList.add('in'); io2.unobserve(en.target); }
        });
      }, { threshold: 0.2 });
      io2.observe(h2);
    });
  }

  /* ---- 5. Mobile nav ---- */
  window.rkOpenMenu = function() {
    var n = document.getElementById('m-nav'),
        o = document.getElementById('m-overlay');
    if (n) n.classList.add('open');
    if (o) o.classList.add('open');
    document.body.style.overflow = 'hidden';
  };
  window.rkCloseMenu = function() {
    var n = document.getElementById('m-nav'),
        o = document.getElementById('m-overlay');
    if (n) n.classList.remove('open');
    if (o) o.classList.remove('open');
    document.body.style.overflow = '';
  };

  /* ---- 6. FAQ accordion ---- */
  function initFaq() {
    document.querySelectorAll('.faq-item').forEach(function(item) {
      var q = item.querySelector('.faq-q');
      if (!q) return;
      q.addEventListener('click', function() {
        var isOpen = item.classList.contains('active');
        // Close all in same list
        var list = item.closest('.faq-right, .faq-list');
        if (list) {
          list.querySelectorAll('.faq-item').forEach(function(i) {
            i.classList.remove('active');
          });
        }
        if (!isOpen) item.classList.add('active');
      });
    });
    // Open first FAQ by default
    var first = document.querySelector('.faq-item');
    if (first) first.classList.add('active');
  }

  /* ---- 7. Stat counters (0 → target, easeOutCubic) ---- */
  function initCounters() {
    var els = document.querySelectorAll('.om-count');
    if (!els.length) return;
    function fmt(n) { return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
    function run(el) {
      var to  = parseInt(el.dataset.to || '0', 10);
      var pre = el.dataset.prefix || '';
      var suf = el.dataset.suffix || '';
      if (reduced || to === 0) { el.textContent = pre + fmt(to) + suf; return; }
      var dur = 1600, start = null;
      function step(ts) {
        if (start === null) start = ts;
        var p = Math.min((ts - start) / dur, 1);
        var eased = 1 - Math.pow(1 - p, 3);
        el.textContent = pre + fmt(Math.round(eased * to)) + suf;
        if (p < 1) requestAnimationFrame(step);
        else el.textContent = pre + fmt(to) + suf;
      }
      requestAnimationFrame(step);
    }
    if (!('IntersectionObserver' in window)) { els.forEach(run); return; }
    var io = new IntersectionObserver(function(entries) {
      entries.forEach(function(en) {
        if (en.isIntersecting) { run(en.target); io.unobserve(en.target); }
      });
    }, { threshold: 0.5 });
    els.forEach(function(el) { io.observe(el); });
  }

  /* ---- 8. Product gallery thumbnails ---- */
  window.rkProdThumb = function(el, src) {
    var main = document.getElementById('prod-main-img');
    if (main) main.src = src;
    document.querySelectorAll('.prod-thumb').forEach(function(t) { t.classList.remove('active'); });
    el.classList.add('active');
  };

  /* ---- 9. WA float toggle ---- */
  window.rkToggleWa = function() {
    var p = document.getElementById('wa-panel');
    if (p) p.classList.toggle('open');
    rkCloseGreeting();
  };
  window.rkCloseGreeting = function() {
    var g = document.getElementById('wa-greeting');
    if (g) {
      g.classList.remove('show');
      try { sessionStorage.setItem('wa_g','1'); } catch(e) {}
    }
  };
  window.rkWaClick = function(id) {
    try {
      fetch('/api/wa-click', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'contact_id=' + id,
        keepalive: true
      });
    } catch(e) {}
    return true;
  };
  // Close WA panel when clicking outside
  document.addEventListener('click', function(e) {
    var w = document.querySelector('.wa-float');
    var p = document.getElementById('wa-panel');
    if (w && p && !w.contains(e.target)) p.classList.remove('open');
  });

  /* ---- 10. NCE scroll-link (scale property cards Framer pattern) ----
     Framer uses data-nce-scroll-link for the carousel/scale effect on property cards */
  function initPropCardScale() {
    var cards = document.querySelectorAll('[data-nce-scroll-link]');
    if (!cards.length || reduced) {
      cards.forEach(function(c) { c.style.opacity = '1'; c.style.transform = 'scale(1)'; });
      return;
    }
    var io = new IntersectionObserver(function(entries) {
      entries.forEach(function(en) {
        if (en.isIntersecting) {
          en.target.style.opacity = '1';
          en.target.style.transform = 'scale(1)';
          en.target.style.transition = 'opacity .7s ' + EASE + ', transform .7s ' + EASE;
          io.unobserve(en.target);
        }
      });
    }, { threshold: 0.15 });
    cards.forEach(function(c) {
      c.style.opacity = '0';
      c.style.transform = 'scale(0.8)';
      io.observe(c);
    });
  }

  /* ---- Boot ---- */
  function boot() {
    initNavbar();
    initHeroEntrance();
    initReveals();
    initWordAnims();
    initFaq();
    initCounters();
    initPropCardScale();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();

  /* ---- Fasilitas accordion/tab (FIX #2) ---- */
  window.omFasSelect = function(idx) {
    // Update panels
    document.querySelectorAll('.om-fas-panel').forEach(function(p, i) {
      p.classList.toggle('active', i === idx);
    });
    // Update content (animate out old, animate in new)
    var contents = document.querySelectorAll('.om-fas-content');
    contents.forEach(function(c) {
      if (c.classList.contains('active')) {
        c.style.opacity = '0';
        c.style.transform = 'translateY(16px)';
        setTimeout(function() {
          c.classList.remove('active');
          c.style.opacity = '';
          c.style.transform = '';
        }, 250);
      }
    });
    setTimeout(function() {
      var next = document.querySelector('.om-fas-content[data-fas-idx="' + idx + '"]');
      if (next) next.classList.add('active');
    }, 260);
  };

  /* ---- Also init fasilitas on boot ---- */
  function initFasilitas() {
    // Make sure first panel is active on load
    var firstPanel = document.querySelector('.om-fas-panel');
    var firstContent = document.querySelector('.om-fas-content');
    if (firstPanel) firstPanel.classList.add('active');
    if (firstContent) firstContent.classList.add('active');
  }
  // Call after existing boot function
  var _origBoot = typeof boot === 'function' ? boot : null;
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() { initFasilitas(); });
  } else {
    initFasilitas();
  }
