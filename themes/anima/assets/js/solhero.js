/* ============================================================================
   SOLUTIONS HERO — scale.com-style 3D glass panels (CSS/SVG). CMS-driven.
   Reads <section class="solhero" data-slides="[...]">. Each slide:
     {eyebrow,h,p,label,img,video,logos:[url...],url}
   Middle panel = media (foto/video) yang AUTO ganti; teks kiri + logo + headline
   fullscreen ikut berganti SINKRON tiap media ganti. Panel depan/belakang kaca.
   Line-art kontur melengkung di-clip di dalam panel. Scroll = morph fullscreen→kotak,
   cursor = miring (jelly). No external deps.
   ========================================================================== */
(function(){
  var sec=document.getElementById('solutions');
  if(!sec||!sec.classList.contains('solhero')) return;
  var slides=[];
  try{ slides=JSON.parse(sec.getAttribute('data-slides')||'[]'); }catch(e){ slides=[]; }
  if(!Array.isArray(slides)||!slides.length) return;

  var NS='http://www.w3.org/2000/svg';
  var scrollEl=sec.querySelector('.sh-scroll'), deck=document.getElementById('shDeck');
  var shCopy=document.getElementById('shCopy'), shOver=document.getElementById('shOver');
  var shEye=document.getElementById('shEye'), shH=document.getElementById('shH'), shP=document.getElementById('shP'), shCta=document.getElementById('shCta');
  var ovEye=document.getElementById('shOverEye'), ovH=document.getElementById('shOverH'), ovP=document.getElementById('shOverP');
  var shMedia=document.getElementById('shMedia'), shLogos=document.getElementById('shLogos'), shChip=document.getElementById('shChip');
  var pB=document.getElementById('shB'), pM=document.getElementById('shM'), pF=document.getElementById('shF');
  var artB=document.getElementById('shArtB'), artM=document.getElementById('shArtM'), artF=document.getElementById('shArtF');
  function cl(v,a,b){return v<a?a:(v>b?b:v);} function lerp(a,b,t){return a+(b-a)*t;}
  function mk(t,a){var e=document.createElementNS(NS,t);for(var k in a)e.setAttribute(k,a[k]);return e;}

  /* ---------- build media per slide (crossfade lembut) ---------- */
  var scenes=[];
  slides.forEach(function(s,i){
    var el;
    if(s.video){ el=document.createElement('video'); el.src=s.video; el.muted=true; el.loop=true; el.autoplay=true; el.playsInline=true; el.setAttribute('playsinline',''); }
    else { el=document.createElement('img'); el.src=s.img||''; el.alt=''; }
    el.className='sh-scene'+(i===0?' on':''); shMedia.appendChild(el); scenes.push(el);
  });
  function buildLogos(i){
    shLogos.innerHTML='';
    (slides[i].logos||[]).slice(0,12).forEach(function(u){ if(!u)return; var im=document.createElement('img'); im.src=u; im.alt=''; im.loading='lazy'; im.onerror=function(){ this.remove(); }; shLogos.appendChild(im); });
  }
  function setCopy(i){
    var s=slides[i];
    shEye.textContent=s.eyebrow||''; shH.innerHTML=s.h||''; shP.textContent=s.p||'';
    ovEye.textContent=s.eyebrow||''; ovH.innerHTML=s.h||''; ovP.textContent=s.p||'';
    if(s.url){ shCta.setAttribute('href', /^(https?:|\/)/.test(s.url)? s.url : ('/'+s.url.replace(/^\/+/,''))); }
    shChip.querySelector('b').textContent=(s.video?'VIDEO':'IMAGE')+(s.label?(' · '+s.label):'');
  }
  // media + chip crossfade; logos+text fade-out lalu ganti (biar tidak numpuk)
  function setSlide(i,instant){
    scenes.forEach(function(el,k){ el.classList.toggle('on',k===i); if(k===i&&el.tagName==='VIDEO'){ try{el.currentTime=0;el.play();}catch(e){} } });
    if(instant){ setCopy(i); buildLogos(i); return; }
    shLogos.style.opacity='0';
    setTimeout(function(){ setCopy(i); buildLogos(i); shLogos.style.opacity='1'; }, 260);
  }
  setSlide(0,true);
  var cur=0;
  setInterval(function(){ cur=(cur+1)%slides.length; setSlide(cur,false); }, 4600);

  /* ---------- line-art: kontur melengkung + blob + garis titik + bendera ---------- */
  function contY(x,y,i,t){return y+30*Math.sin(x*0.0105+t*0.55+i*1.3)+15*Math.sin(x*0.0229+t*0.9+i*2.1)+7*Math.sin(x*0.049+t*1.35+i*0.7);}
  function smoothOpen(p){var d='M'+p[0][0].toFixed(1)+' '+p[0][1].toFixed(1);for(var i=0;i<p.length-1;i++){var a=p[i-1]||p[i],b=p[i],c=p[i+1],e=p[i+2]||c;d+=' C'+(b[0]+(c[0]-a[0])/6).toFixed(1)+' '+(b[1]+(c[1]-a[1])/6).toFixed(1)+' '+(c[0]-(e[0]-b[0])/6).toFixed(1)+' '+(c[1]-(e[1]-b[1])/6).toFixed(1)+' '+c[0].toFixed(1)+' '+c[1].toFixed(1);}return d;}
  function smoothClosed(p){var n=p.length,d='M'+p[0][0].toFixed(1)+' '+p[0][1].toFixed(1);for(var i=0;i<n;i++){var a=p[(i-1+n)%n],b=p[i],c=p[(i+1)%n],e=p[(i+2)%n];d+=' C'+(b[0]+(c[0]-a[0])/6).toFixed(1)+' '+(b[1]+(c[1]-a[1])/6).toFixed(1)+' '+(c[0]-(e[0]-b[0])/6).toFixed(1)+' '+(c[1]-(e[1]-b[1])/6).toFixed(1)+' '+c[0].toFixed(1)+' '+c[1].toFixed(1);}return d+'Z';}
  function contourD(y,i,t){var p=[];for(var x=-30;x<=630;x+=22)p.push([x,contY(x,y,i,t)]);return smoothOpen(p);}
  function curvyD(y,amp,seed,t){var p=[];for(var x=-40;x<=640;x+=18)p.push([x,y+amp*Math.sin(x*0.011+t*0.6+seed)+amp*0.52*Math.sin(x*0.026+t*0.95+seed*1.6)+amp*0.26*Math.sin(x*0.052+t*1.25+seed)]);return p;}
  function blobD(cx,cy,r0,t,seed){var p=[],N=12;for(var k=0;k<N;k++){var a=k/N*6.2832,r=r0+6*Math.sin(3*a+t+seed)+4*Math.sin(5*a-t*1.3+seed)+2.5*Math.sin(2*a+t*0.7+seed);p.push([cx+Math.cos(a)*r,cy+Math.sin(a)*r*0.82]);}return smoothClosed(p);}

  function buildLayer(container,spec){
    var svg=mk('svg',{viewBox:'0 0 600 420',preserveAspectRatio:'none',fill:'none',stroke:'#fff','stroke-linecap':'round','stroke-linejoin':'round'});
    var L={solid:[],dotted:[],blobs:[]};
    (spec.solid||[]).forEach(function(s){var p=mk('path',{'stroke-width':s.w||1,opacity:s.op||0.8});svg.appendChild(p);L.solid.push({el:p,c:s});});
    (spec.dotted||[]).forEach(function(s){var p=mk('path',{'stroke-width':1.1,opacity:s.op||0.72,'stroke-dasharray':'2.5 7'});svg.appendChild(p);var fl=[];for(var i=0;i<(s.flags||0);i++){var tr=mk('polygon',{points:'0,-4.5 0,4.5 10,0',fill:'#fff',stroke:'none',opacity:0.9});svg.appendChild(tr);fl.push(tr);}L.dotted.push({el:p,c:s,fl:fl});});
    (spec.blobs||[]).forEach(function(b){var p=mk('path',{'stroke-width':1,opacity:b.op||0.5});svg.appendChild(p);L.blobs.push({el:p,c:b});});
    container.appendChild(svg);return L;
  }
  function updateLayer(L,t){
    L.solid.forEach(function(o){o.el.setAttribute('d',contourD(o.c.y,o.c.i,t));});
    L.dotted.forEach(function(o){var p=curvyD(o.c.y,o.c.amp,o.c.seed,t);o.el.setAttribute('d',smoothOpen(p));o.el.style.strokeDashoffset=(-t*55);
      o.fl.forEach(function(tr,i){var idx=Math.floor((i+1)/(o.fl.length+1)*(p.length-1));var a=p[idx],b=p[Math.min(idx+1,p.length-1)];var ang=Math.atan2(b[1]-a[1],b[0]-a[0])*180/Math.PI;
        if(a[0]<24||a[0]>576){tr.style.opacity=0;}else{tr.style.opacity=0.9;tr.setAttribute('transform','translate('+a[0].toFixed(1)+' '+a[1].toFixed(1)+') rotate('+ang.toFixed(1)+')');}});});
    L.blobs.forEach(function(o){o.el.setAttribute('d',blobD(o.c.cx,o.c.cy,o.c.r,t,o.c.seed));});
  }
  var LM=buildLayer(artM,{solid:[{y:95,i:0,op:.85},{y:170,i:1,op:.7},{y:250,i:2,op:.8},{y:330,i:3,op:.6}],blobs:[{cx:170,cy:150,r:18,seed:0,op:.55},{cx:330,cy:250,r:22,seed:2,op:.5},{cx:470,cy:190,r:15,seed:4,op:.5}]});
  var LF=buildLayer(artF,{solid:[{y:210,i:4,op:.4}],dotted:[{y:120,amp:30,seed:0.5,op:.8,flags:4},{y:300,amp:34,seed:2.7,op:.75,flags:4}]});
  var LB=buildLayer(artB,{solid:[{y:150,i:5,op:.35}],dotted:[{y:80,amp:28,seed:4.2,op:.7,flags:4},{y:360,amp:32,seed:1.9,op:.68,flags:4}]});

  /* ---------- morph + rotasi (jelly + cursor) ---------- */
  var pin=sec.querySelector('.sh-pin');
  var M={};
  // Offset dihitung relatif ke .sh-pin (yang saat ter-pin = viewport), bukan posisi dokumen,
  // supaya state fullscreen tetap center walau section ada di tengah halaman.
  function measure(){deck.style.transform='none';var pr=pin.getBoundingClientRect(),r=deck.getBoundingClientRect();
    M.w=r.width;M.h=r.height;var cx=(r.left-pr.left)+r.width/2,cy=(r.top-pr.top)+r.height/2;
    M.cover=Math.max(innerWidth/M.w,innerHeight/M.h)*1.16;M.dx=innerWidth/2-cx;M.dy=innerHeight/2-cy;}
  var mx=0,my=0,hoverK=0;
  sec.addEventListener('mousemove',function(e){mx=e.clientX/innerWidth-0.5;my=e.clientY/innerHeight-0.5;hoverK=1;});
  sec.addEventListener('mouseleave',function(){hoverK=0;});
  var RX={c:0,v:0,t:0},RY={c:0,v:0,t:0},STIFF=0.30,DAMP=0.70;
  function springStep(S){S.v+=(S.t-S.c)*STIFF;S.v*=DAMP;S.c+=S.v;}
  function prog(){var r=scrollEl.getBoundingClientRect();return cl((-r.top)/(r.height-innerHeight||1),0,1);}

  function frame(ts){
    var p=prog(),e=p*p*(3-2*p);
    var sc=lerp(M.cover,1,e),tx=lerp(M.dx,-60,e),ty=lerp(M.dy,0,e);
    var baseRy=lerp(0,-18,e),baseRx=lerp(0,11,e),cx=mx,cy=my,infl=e*hoverK;
    RY.t=baseRy+cx*48*infl;RX.t=baseRx-cy*30*infl;springStep(RX);springStep(RY);
    deck.style.transform='translate3d('+tx.toFixed(1)+'px,'+ty.toFixed(1)+'px,0) rotateX('+RX.c.toFixed(2)+'deg) rotateY('+RY.c.toFixed(2)+'deg) scale('+sc.toFixed(3)+')';
    pF.style.transform='translate3d('+(-42*e)+'px,'+(20*e)+'px,'+(52*e)+'px)';
    pM.style.transform='translate3d(0,0,0)';
    pB.style.transform='translate3d('+(74*e)+'px,'+(-24*e)+'px,'+(-72*e)+'px)';
    shOver.style.opacity=cl(1-(p-0.04)/0.26,0,1).toFixed(2);
    shCopy.style.opacity=cl((p-0.5)/0.28,0,1).toFixed(2);
    var dp=cl((p-0.42)/0.5,0,1);
    artB.style.opacity=artM.style.opacity=artF.style.opacity=cl(dp*1.2,0,1).toFixed(2);
    var T=(ts||0)/1000; updateLayer(LM,T); updateLayer(LF,T+1.3); updateLayer(LB,T+2.6);
    requestAnimationFrame(frame);
  }
  addEventListener('resize',measure);
  measure(); requestAnimationFrame(frame);
})();

/* ============================================================================
   STATEMENT — scroll-synced TYPING effect (futuristic). Characters type out as
   the user scrolls through the section; keywords get a glowing gradient; a
   neon caret sits at the last typed character. Old word-reveal in anima.js
   self-disables when #statement has class "typing".
   ========================================================================== */
(function(){
  var sec=document.getElementById('statement');
  if(!sec||!sec.classList.contains('typing')) return;
  var el=document.getElementById('stmtText'); if(!el) return;
  var kw=/^(Sapta|Tunas|Teknologi|Business|Technology|Solutions|Services|Indonesia|2015)$/i;
  var text=(el.textContent||'').replace(/\s+/g,' ').trim();
  if(!text) return;
  var esc=function(c){return c==='<'?'&lt;':c==='>'?'&gt;':c==='&'?'&amp;':c;};
  // Spasi = teks biasa (breakable) biar kalimat tetap wrap jadi beberapa baris;
  // hanya karakter non-spasi yang dibungkus span & diketik satu per satu.
  var html='', toks=text.split(/(\s+)/);
  toks.forEach(function(tok){
    if(/^\s+$/.test(tok)){ html+=' '; return; }
    var isk=kw.test(tok.replace(/[.,]/g,''));
    html+='<span class="wd">';
    for(var i=0;i<tok.length;i++) html+='<span class="c'+(isk?' kw':'')+'">'+esc(tok[i])+'</span>';
    html+='</span>';
  });
  el.innerHTML=html;
  var cs=[].slice.call(el.querySelectorAll('.c'));
  function cl(v,a,b){return v<a?a:(v>b?b:v);}
  function upd(){
    var r=sec.getBoundingClientRect(), tot=sec.offsetHeight-innerHeight||1;
    var p=cl(-r.top/tot,0,1);
    var n=Math.floor(cl(p/0.9,0,1)*cs.length);  // selesai ketik sebelum section lepas pin
    for(var i=0;i<cs.length;i++){ cs[i].classList.toggle('on', i<n); cs[i].classList.toggle('cursor', i===n-1); }
  }
  addEventListener('scroll',upd,{passive:true}); addEventListener('resize',upd); upd();
})();

/* ============================================================================
   SOLUTIONS PAGE (/solutions) — "Constellation" hero. Separate from the home
   #solutions scale.com panels above; guards on #solHero so each page runs only
   its own block. Markup in solutions.php, styles in pages.css.
   ========================================================================== */
(function () {
  var sec = document.getElementById('solHero');
  if (!sec) return;
  var arena = document.getElementById('solArena');
  var core = document.getElementById('solCore');
  var copy = document.getElementById('solCopy');
  var cv = document.getElementById('solNet');
  if (!arena || !core || !cv) return;
  var nodes = [].slice.call(arena.querySelectorAll('.scn-node'));
  if (!nodes.length) return;

  // Fan-arc layout: x spread 14%→86%, y dips up in the middle (edges lower).
  var N = nodes.length;
  nodes.forEach(function (n, i) {
    var fx = N > 1 ? i / (N - 1) : 0.5;
    var x = 14 + fx * 72;
    var y = 60 - Math.sin(fx * Math.PI) * 21;
    n.style.left = x.toFixed(2) + '%';
    n.style.top = y.toFixed(2) + '%';
  });

  var MQ = window.matchMedia('(max-width:860px), (prefers-reduced-motion:reduce)');
  var ctx = cv.getContext('2d'), DPR = Math.min(devicePixelRatio || 1, 2), W, H;
  function rz() { W = cv.width = sec.offsetWidth * DPR; H = cv.height = sec.offsetHeight * DPR; }
  rz(); addEventListener('resize', rz);

  var dust = [];
  for (var i = 0; i < 56; i++) dust.push({ x: Math.random(), y: Math.random(), r: (Math.random() * 1.5 + 0.3) * DPR, s: Math.random() * 0.0003 + 0.00008, a: Math.random() * 0.5 + 0.15 });

  var mx = 0, my = 0, tx = 0, ty = 0, t = 0, running = false;
  addEventListener('mousemove', function (e) {
    var r = sec.getBoundingClientRect();
    tx = (e.clientX - r.left) / r.width - 0.5;
    ty = (e.clientY - r.top) / r.height - 0.5;
  });
  function cc(el) { var r = el.getBoundingClientRect(), s = sec.getBoundingClientRect(); return { x: (r.left - s.left + r.width / 2) * DPR, y: (r.top - s.top + r.height / 2) * DPR }; }

  var sp = 0;   // lerped scroll progress → heavy, weighty recede as the promo covers
  function frame() {
    if (MQ.matches) { running = false; ctx.clearRect(0, 0, W, H); return; }
    t += 0.016; mx += (tx - mx) * 0.05; my += (ty - my) * 0.05;
    var spT = Math.min(Math.max(scrollY / (innerHeight * 1.15), 0), 1);
    sp += (spT - sp) * 0.055;                    // heavy trailing (About Us feel)
    var lift = sp * 70, sc = 1 - sp * 0.07;
    arena.style.opacity = (1 - sp).toFixed(3);
    arena.style.transform = 'translate(' + (mx * 26).toFixed(1) + 'px,' + (my * 20 - lift).toFixed(1) + 'px) scale(' + sc.toFixed(3) + ')';
    if (copy) { copy.style.opacity = Math.max(0, 1 - sp * 1.2).toFixed(3); copy.style.transform = 'translate(' + (mx * -10).toFixed(1) + 'px,' + (my * -8 - lift * 0.6).toFixed(1) + 'px)'; }
    var sd = sec.querySelector('.scn-scroll'); if (sd) sd.style.opacity = Math.max(0, 1 - sp * 3).toFixed(3);
    ctx.clearRect(0, 0, W, H);
    dust.forEach(function (d) { d.y -= d.s; if (d.y < 0) d.y = 1; ctx.beginPath(); ctx.arc(d.x * W, d.y * H, d.r, 0, 7); ctx.fillStyle = 'rgba(120,170,255,' + d.a + ')'; ctx.fill(); });
    var c = cc(core);
    nodes.forEach(function (n, i) {
      var p = cc(n);
      var grad = ctx.createLinearGradient(c.x, c.y, p.x, p.y);
      grad.addColorStop(0, 'rgba(90,160,255,.5)'); grad.addColorStop(1, 'rgba(73,214,199,.15)');
      ctx.beginPath(); ctx.moveTo(c.x, c.y); ctx.lineTo(p.x, p.y); ctx.strokeStyle = grad; ctx.lineWidth = 1.2 * DPR; ctx.stroke();
      var tp = (t * 0.5 + i * 0.2) % 1, px = c.x + (p.x - c.x) * tp, py = c.y + (p.y - c.y) * tp;
      ctx.beginPath(); ctx.arc(px, py, 2.6 * DPR, 0, 7); ctx.fillStyle = 'rgba(150,210,255,.95)';
      ctx.shadowBlur = 12 * DPR; ctx.shadowColor = 'rgba(90,160,255,.9)'; ctx.fill(); ctx.shadowBlur = 0;
    });
    ctx.beginPath();
    nodes.forEach(function (n, i) { var p = cc(n); if (i === 0) ctx.moveTo(p.x, p.y); else ctx.lineTo(p.x, p.y); });
    ctx.strokeStyle = 'rgba(120,170,255,.12)'; ctx.lineWidth = 1 * DPR; ctx.stroke();
    requestAnimationFrame(frame);
  }
  function start() { if (!running && !MQ.matches) { running = true; requestAnimationFrame(frame); } }
  (MQ.addEventListener ? MQ.addEventListener('change', start) : MQ.addListener(start));
  start();

  // hover dims the other nodes
  nodes.forEach(function (n) {
    n.addEventListener('mouseenter', function () { nodes.forEach(function (o) { if (o !== n) o.classList.add('dim'); }); });
    n.addEventListener('mouseleave', function () { nodes.forEach(function (o) { o.classList.remove('dim'); }); });
  });
})();
