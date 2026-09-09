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
    var el = e.target.closest ? e.target.closest('[data-scrolltop]') : null;
    if (!el) return;
    if (el.hasAttribute('data-scrolltop')) { e.preventDefault(); window.scrollTo({top:0,behavior:'smooth'}); }
  });
})();

/* Footer newsletter subscribe — POST to /api/subscribe via fetch, stay on page. */
(function(){
  document.addEventListener('submit', function(e){
    var f = e.target;
    if (!f || !f.matches || !f.matches('form[data-newsletter]')) return;
    e.preventDefault();
    var msg = f.parentElement ? f.parentElement.querySelector('[data-newsletter-msg]') : null;
    var input = f.querySelector('input[type=email]');
    var body = new URLSearchParams(new FormData(f));
    fetch(f.getAttribute('action'), { method:'POST', headers:{'X-Requested-With':'fetch'}, body: body })
      .then(function(r){ return r.json().catch(function(){ return {status:'error'}; }); })
      .then(function(d){
        var ok = d && d.status === 'ok';
        if (msg) {
          msg.hidden = false;
          msg.className = 'tel-footer-nl-msg ' + (ok ? 'ok' : 'err');
          msg.textContent = ok ? 'Terima kasih! Anda sudah berlangganan.' : (d && d.message ? d.message : 'Gagal berlangganan. Coba lagi.');
        }
        if (ok && input) input.value = '';
      })
      .catch(function(){ if (msg){ msg.hidden=false; msg.className='tel-footer-nl-msg err'; msg.textContent='Gagal berlangganan. Coba lagi.'; } });
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
  function setNav(open){ document.body.classList.toggle('nav-open', open); if (hamb) hamb.setAttribute('aria-expanded', open ? 'true' : 'false'); }
  if (hamb) hamb.addEventListener('click', function(){ setNav(!document.body.classList.contains('nav-open')); });
  // Close the mobile drawer on scrim/X click, on any drawer link, or Esc.
  document.addEventListener('click', function(e){
    if (e.target.closest && e.target.closest('[data-nav-close]')) { setNav(false); return; }
    var link = e.target.closest && e.target.closest('.mnav-list a');
    if (link) setNav(false);
  });
  addEventListener('keydown', function(e){ if (e.key === 'Escape') setNav(false); });
})();
