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
    var el = e.target.closest ? e.target.closest('[data-scrolltop],[data-proposal-submit]') : null;
    if (!el) return;
    if (el.hasAttribute('data-scrolltop')) { e.preventDefault(); window.scrollTo({top:0,behavior:'smooth'}); }
    else if (el.hasAttribute('data-proposal-submit')) { e.preventDefault(); /* TODO: POST Request Proposal to CMS endpoint */ }
  });
})();
