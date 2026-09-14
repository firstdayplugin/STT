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

/* Reveal-on-scroll for elements marked .reveal (adds .in when they enter the viewport). */
(function(){
  var els = document.querySelectorAll('.reveal');
  if (!els.length) return;
  if (!('IntersectionObserver' in window)) return; // no-JS/old browsers: content stays visible
  document.documentElement.classList.add('has-io'); // enables the hidden→reveal transition
  var io = new IntersectionObserver(function(entries){
    entries.forEach(function(en){ if (en.isIntersecting){ en.target.classList.add('in'); io.unobserve(en.target); } });
  }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
  els.forEach(function(e){ io.observe(e); });
})();

/* Blog "Publishing Year" dropdown — navigate on change (CSP-safe, no inline handler). */
(function(){
  document.addEventListener('change', function(e){
    var s = e.target;
    if (s && s.tagName === 'SELECT' && s.hasAttribute('data-nav') && s.value) { window.location.href = s.value; }
  });
})();

/* ================= ABOUT US sliders ================= */
/* Milestone — timeline drives a cross-fade of the text + image block above it. */
(function(){
  document.querySelectorAll('.ab-mile[data-milestones]').forEach(function(root){
    var data; try{ data=JSON.parse(root.getAttribute('data-milestones')); }catch(e){ return; }
    if(!data||!data.length) return;
    var titleEl=root.querySelector('[data-mile-title]'), bodyEl=root.querySelector('[data-mile-body]'),
        imgEl=root.querySelector('[data-mile-img]'), textWrap=root.querySelector('[data-mile-text]'),
        mediaWrap=root.querySelector('.ab-mile-media'), track=root.querySelector('[data-mile-track]'),
        nodes=[].slice.call(root.querySelectorAll('[data-mile-node]'));
    var cur=-1, busy=false;
    function paint(i,skip){
      var d=data[i];
      titleEl.textContent=d.title||''; bodyEl.textContent=d.text||'';
      if(d.img){ imgEl.src=d.img; mediaWrap.classList.remove('empty'); }
      else { imgEl.removeAttribute('src'); mediaWrap.classList.add('empty'); }
      nodes.forEach(function(n,k){ n.classList.toggle('on',k===i); });
      var an=nodes[i]; if(an&&track){ track.scrollTo({left:an.offsetLeft-track.clientWidth/2+an.offsetWidth/2, behavior:skip?'auto':'smooth'}); }
    }
    function go(i,skip){
      i=(i+data.length)%data.length; if(i===cur) return;
      if(skip){ cur=i; paint(i,true); return; }
      if(busy) return; busy=true;
      textWrap.classList.add('swap'); mediaWrap.classList.add('swap');
      setTimeout(function(){
        cur=i; paint(i); void textWrap.offsetWidth;
        textWrap.classList.remove('swap'); mediaWrap.classList.remove('swap'); busy=false;
      },240);
    }
    nodes.forEach(function(n,k){ n.addEventListener('click',function(){ go(k); }); });
    var pv=root.querySelector('[data-mile-prev]'), nx=root.querySelector('[data-mile-next]');
    if(pv) pv.addEventListener('click',function(){ go(cur-1); });
    if(nx) nx.addEventListener('click',function(){ go(cur+1); });
    go(0,true);
  });
})();

/* Awards / Certification — page through years or brands with a fade. */
(function(){
  document.querySelectorAll('[data-year-slider]').forEach(function(root){
    var pages=[].slice.call(root.querySelectorAll('[data-year-page]'));
    if(!pages.length) return;
    var label=root.querySelector('[data-year-label]'), cur=0;
    function show(i){
      i=(i+pages.length)%pages.length;
      pages.forEach(function(p,k){
        if(k===i){ p.hidden=false; p.classList.remove('yr-in'); void p.offsetWidth; p.classList.add('yr-in'); }
        else p.hidden=true;
      });
      if(label) label.textContent=pages[i].getAttribute('data-year-page');
      cur=i;
    }
    var pv=root.querySelector('[data-year-prev]'), nx=root.querySelector('[data-year-next]');
    if(pv) pv.addEventListener('click',function(){ show(cur-1); });
    if(nx) nx.addEventListener('click',function(){ show(cur+1); });
    if(pages.length<2){ if(pv)pv.style.visibility='hidden'; if(nx)nx.style.visibility='hidden'; }
  });
})();

/* Quality — horizontal carousel scrolled by arrows. */
(function(){
  document.querySelectorAll('[data-carousel]').forEach(function(root){
    var track=root.querySelector('[data-carousel-track]'); if(!track) return;
    function by(dir){ track.scrollBy({left:dir*Math.max(240,track.clientWidth*0.7),behavior:'smooth'}); }
    var pv=root.querySelector('[data-carousel-prev]'), nx=root.querySelector('[data-carousel-next]');
    if(pv) pv.addEventListener('click',function(){ by(-1); });
    if(nx) nx.addEventListener('click',function(){ by(1); });
  });
})();
