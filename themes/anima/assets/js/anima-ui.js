/* Anima UI behaviors — replaces inline on* handlers (CSP-A: no inline scripts/handlers). */
(function(){
  // Image fallbacks (was inline onerror). 'error' doesn't bubble → capture phase.
  document.addEventListener('error', function(e){
    var t = e.target;
    if (!t || t.tagName !== 'IMG' || !t.dataset || !t.dataset.fallback) return;
    if (t.dataset.fallback === 'remove') { t.remove(); }
    else if (t.dataset.fallback === 'bg') {
      if (t.parentElement) t.parentElement.style.background = 'linear-gradient(135deg,#dceafb,#eef4fc)';
      t.style.visibility = 'hidden';
    }
  }, true);
  // Delegated clicks (was inline onclick).
  document.addEventListener('click', function(e){
    // Tabs: <button data-tab-btn="X" data-tab-group="G"> toggles [data-tab-panel="X"][data-tab-group="G"].
    var tab = e.target.closest ? e.target.closest('[data-tab-btn]') : null;
    if (tab) {
      var g = tab.getAttribute('data-tab-group'), key = tab.getAttribute('data-tab-btn');
      document.querySelectorAll('[data-tab-btn][data-tab-group="' + g + '"]').forEach(function(b){
        b.classList.toggle('on', b === tab);
      });
      document.querySelectorAll('[data-tab-panel][data-tab-group="' + g + '"]').forEach(function(p){
        p.hidden = (p.getAttribute('data-tab-panel') !== key);
      });
      return;
    }
    var el = e.target.closest ? e.target.closest('[data-scrolltop],[data-proposal-submit],[data-contact-submit]') : null;
    if (!el) return;
    if (el.hasAttribute('data-scrolltop')) { e.preventDefault(); window.scrollTo({top:0,behavior:'smooth'}); }
    else if (el.hasAttribute('data-proposal-submit')) { e.preventDefault(); /* TODO: POST Request Proposal to CMS endpoint */ }
    else if (el.hasAttribute('data-contact-submit')) { e.preventDefault(); /* TODO: POST Contact form to CMS endpoint */ }
  });
})();

/* Generic nav behaviors (all pages). On hero pages anima.js also drives this — idempotent. */
(function(){
  var hd = document.querySelector('header'); if (!hd) return;
  if (!document.body.classList.contains('page-inner')) {
    var on = function(){ hd.classList.toggle('scrolled', (window.scrollY||document.documentElement.scrollTop) > 70); };
    addEventListener('scroll', on, { passive:true }); on();
  }
  var hamb = hd.querySelector('.hamb');
  if (hamb) hamb.addEventListener('click', function(){ document.body.classList.toggle('nav-open'); });
})();
