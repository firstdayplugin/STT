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

/* Solutions "See More" — smooth animated popup (CSP-safe, delegated). */
(function(){
  var lastTrigger = null;
  function openModal(m, trigger){
    if (!m) return;
    lastTrigger = trigger || null;
    m.classList.add('open');
    m.setAttribute('aria-hidden', 'false');
    document.body.classList.add('sol-modal-open');
    var panel = m.querySelector('.sol-modal-panel');
    if (panel) { panel.scrollTop = 0; var x = m.querySelector('.sol-modal-x'); if (x) x.focus(); }
  }
  function closeModal(m){
    if (!m) return;
    m.classList.remove('open');
    m.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('sol-modal-open');
    if (lastTrigger && lastTrigger.focus) { lastTrigger.focus(); lastTrigger = null; }
  }
  document.addEventListener('click', function(e){
    var opener = e.target.closest ? e.target.closest('[data-sol-open]') : null;
    if (opener) { e.preventDefault(); openModal(document.getElementById('solm-' + opener.getAttribute('data-sol-open')), opener); return; }
    if (e.target.closest && e.target.closest('[data-sol-close]')) { closeModal(e.target.closest('.sol-modal')); }
  });
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') { var m = document.querySelector('.sol-modal.open'); if (m) closeModal(m); }
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
;
/* About gallery lightbox — click an Award/Quality/Cert image to view it full size. */
(function(){
  var imgs=[].slice.call(document.querySelectorAll('.ab-card-img img'));
  if(!imgs.length) return;
  var lb=document.createElement('div'); lb.className='ablx'; lb.setAttribute('role','dialog'); lb.setAttribute('aria-modal','true');
  lb.innerHTML='<button class="ablx-x" type="button" aria-label="Tutup"><svg viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg></button><img alt=""><div class="ablx-cap"></div>';
  document.body.appendChild(lb);
  var big=lb.querySelector('img'), cap=lb.querySelector('.ablx-cap'), x=lb.querySelector('.ablx-x');
  function open(src,alt){ big.src=src; big.alt=alt||''; cap.textContent=alt||''; lb.classList.add('on'); document.body.style.overflow='hidden'; }
  function close(){ lb.classList.remove('on'); document.body.style.overflow=''; setTimeout(function(){ big.src=''; },200); }
  imgs.forEach(function(im){ im.addEventListener('click',function(){ open(im.currentSrc||im.src, im.getAttribute('alt')); }); });
  x.addEventListener('click',close);
  lb.addEventListener('click',function(e){ if(e.target===lb) close(); });
  document.addEventListener('keydown',function(e){ if(e.key==='Escape' && lb.classList.contains('on')) close(); });
})();
;
/* Industry detail — pillars read by scrolling: sticky scroll-spy rail highlights
   the current pillar, click smooth-scrolls, and sections fade up on entry. */
(function(){
  var nav=document.getElementById('idtNav');
  var sections=[].slice.call(document.querySelectorAll('.idt2-panel[data-spy-section]'));
  if(!sections.length) return;
  // fade-up reveal (mark first so no-JS keeps content visible)
  sections.forEach(function(s){ s.setAttribute('data-reveal',''); });
  if('IntersectionObserver' in window){
    var io=new IntersectionObserver(function(es){es.forEach(function(en){ if(en.isIntersecting){ en.target.classList.add('vis'); io.unobserve(en.target); } });},{threshold:.12,rootMargin:'0px 0px -8% 0px'});
    sections.forEach(function(s){ io.observe(s); });
  } else { sections.forEach(function(s){ s.classList.add('vis'); }); }
  var links=nav ? [].slice.call(nav.querySelectorAll('[data-spy-to]')) : [];
  if(nav){
    nav.addEventListener('click',function(e){
      var b=e.target.closest && e.target.closest('[data-spy-to]'); if(!b) return;
      var t=document.getElementById(b.getAttribute('data-spy-to'));
      if(t) t.scrollIntoView({behavior:'smooth',block:'start'});
    });
  }
  var ticking=false;
  function spy(){
    ticking=false;
    var y=window.scrollY+170, cur=sections[0];
    sections.forEach(function(s){ if(s.offsetTop<=y) cur=s; });
    var id=cur.id;
    links.forEach(function(l){
      var on=l.getAttribute('data-spy-to')===id; l.classList.toggle('on',on);
      if(on && nav && nav.scrollWidth>nav.clientWidth){ /* keep active chip in view on mobile */
        var lx=l.offsetLeft-16; if(Math.abs(nav.scrollLeft-lx)>4) nav.scrollLeft=lx;
      }
    });
  }
  window.addEventListener('scroll',function(){ if(!ticking){ ticking=true; requestAnimationFrame(spy); } },{passive:true});
  spy();
})();
