/* ===== Interactive tech-network hero FX (cursor-reactive node mesh) =========
   Firm, geometric "connectivity" motif for an IT / infrastructure brand.
   Nodes drift on a loose engineered grid; near the cursor they light up and
   the mesh pulls toward it. Sits below the hero copy and fades out over the
   text zone so it never competes with the headline / CTA.

   Usage: place  <canvas class="tk-fx"></canvas>  inside any position:relative
   hero. Optional  data-fade="bl" | "center" | "none"  controls where the mesh
   is dimmed to protect text (default "bl" = bottom-left, like the Home hero).
   The script auto-discovers every .tk-fx canvas and no-ops when there are none. */
(function(){
  'use strict';
  var REDUCE=false; try{REDUCE=matchMedia('(prefers-reduced-motion: reduce)').matches;}catch(e){}

  function NetFx(canvas){
    var ctx=canvas.getContext('2d'); if(!ctx) return null;
    var W=0,H=0,DPR=1,nodes=[],raf=0,running=false,t=0;
    var mouse={x:-9999,y:-9999,on:false};
    var LINK=155,PULL=200;
    var FADE=(canvas.getAttribute('data-fade')||'bl').toLowerCase();

    function resize(){
      var r=canvas.getBoundingClientRect();
      if(r.width<2||r.height<2){ requestAnimationFrame(resize); return; }
      DPR=Math.min(window.devicePixelRatio||1,2); W=r.width; H=r.height;
      canvas.width=Math.round(W*DPR); canvas.height=Math.round(H*DPR);
      ctx.setTransform(DPR,0,0,DPR,0,0); build();
    }
    function build(){
      var target=Math.round(Math.min(64,Math.max(16,(W*H)/18000)));
      var cols=Math.max(3,Math.round(Math.sqrt(target*(W/Math.max(H,1)))));
      var rows=Math.max(2,Math.round(target/cols));
      var gx=W/cols,gy=H/rows; nodes=[];
      for(var yy=0;yy<rows;yy++)for(var xx=0;xx<cols;xx++){
        var hx=gx*(xx+0.5)+(Math.random()-0.5)*gx*0.6,
            hy=gy*(yy+0.5)+(Math.random()-0.5)*gy*0.6;
        nodes.push({hx:hx,hy:hy,x:hx,y:hy,
          vx:(Math.random()-0.5)*0.10,vy:(Math.random()-0.5)*0.10,ph:Math.random()*6.283});
      }
    }
    function step(){
      t+=0.016; var mx=mouse.x,my=mouse.y,on=mouse.on,i,n;
      for(i=0;i<nodes.length;i++){ n=nodes[i];
        n.hx+=n.vx; n.hy+=n.vy;
        if(n.hx<0||n.hx>W)n.vx*=-1; if(n.hy<0||n.hy>H)n.vy*=-1;
        var tx=n.hx+Math.sin(t*0.6+n.ph)*4, ty=n.hy+Math.cos(t*0.5+n.ph)*4;
        if(on){ var dx=mx-n.x,dy=my-n.y,d=Math.sqrt(dx*dx+dy*dy);
          if(d<PULL){ var f=1-d/PULL; tx+=dx*f*0.20; ty+=dy*f*0.20; } }
        n.x+=(tx-n.x)*0.08; n.y+=(ty-n.y)*0.08;
      }
    }
    function fadeMask(){
      if(FADE==='none') return;
      var g;
      if(FADE==='center'){
        g=ctx.createRadialGradient(W*0.5,H*0.5,10, W*0.5,H*0.5, Math.max(W,H)*0.62);
        g.addColorStop(0,'rgba(0,0,0,0.92)'); g.addColorStop(0.42,'rgba(0,0,0,0.5)'); g.addColorStop(1,'rgba(0,0,0,0)');
      } else { // 'bl'
        g=ctx.createRadialGradient(W*0.03,H*0.97,8, W*0.03,H*0.97, Math.max(W,H)*0.55);
        g.addColorStop(0,'rgba(0,0,0,1)'); g.addColorStop(0.5,'rgba(0,0,0,0.5)'); g.addColorStop(1,'rgba(0,0,0,0)');
      }
      ctx.globalCompositeOperation='destination-out'; ctx.fillStyle=g; ctx.fillRect(0,0,W,H);
      ctx.globalCompositeOperation='source-over';
    }
    function render(){
      ctx.clearRect(0,0,W,H);
      var mx=mouse.x,my=mouse.y,on=mouse.on,a,b,na,nb;
      ctx.lineWidth=1;
      for(a=0;a<nodes.length;a++){ na=nodes[a];
        for(b=a+1;b<nodes.length;b++){ nb=nodes[b];
          var ddx=na.x-nb.x,ddy=na.y-nb.y,dd=Math.sqrt(ddx*ddx+ddy*ddy);
          if(dd<LINK){ var al=(1-dd/LINK)*0.42;
            ctx.strokeStyle='rgba(118,172,255,'+al.toFixed(3)+')';
            ctx.beginPath(); ctx.moveTo(na.x,na.y); ctx.lineTo(nb.x,nb.y); ctx.stroke(); }
        }
      }
      for(a=0;a<nodes.length;a++){ na=nodes[a]; var near=false,cf=0;
        if(on){ var cdx=mx-na.x,cdy=my-na.y,cd=Math.sqrt(cdx*cdx+cdy*cdy);
          if(cd<PULL){ near=true; cf=1-cd/PULL;
            ctx.strokeStyle='rgba(150,220,255,'+(cf*0.85).toFixed(3)+')'; ctx.lineWidth=1.1;
            ctx.beginPath(); ctx.moveTo(mx,my); ctx.lineTo(na.x,na.y); ctx.stroke(); } }
        var rad=near?(1.5+cf*2.3):1.4;
        ctx.beginPath(); ctx.arc(na.x,na.y,rad,0,6.283);
        ctx.fillStyle=near?'rgba(212,240,255,'+(0.55+cf*0.45).toFixed(3)+')':'rgba(150,196,255,0.48)';
        ctx.fill();
      }
      if(on){
        ctx.beginPath(); ctx.arc(mx,my,2.6,0,6.283); ctx.fillStyle='rgba(224,246,255,0.95)'; ctx.fill();
        ctx.beginPath(); ctx.arc(mx,my,9,0,6.283); ctx.strokeStyle='rgba(150,215,255,0.32)'; ctx.lineWidth=1; ctx.stroke();
      }
      fadeMask();
    }
    function frame(){ if(!running)return; step(); render(); raf=requestAnimationFrame(frame); }
    function start(){ if(running||REDUCE)return; running=true; raf=requestAnimationFrame(frame); }
    function stop(){ running=false; if(raf)cancelAnimationFrame(raf); raf=0; }
    function setMouse(cx,cy){ var r=canvas.getBoundingClientRect(); var x=cx-r.left,y=cy-r.top;
      if(x>=-40&&x<=W+40&&y>=-40&&y<=H+40){mouse.x=x;mouse.y=y;mouse.on=true;} else mouse.on=false; }
    function clearMouse(){ mouse.on=false; }
    function still(){ render(); }
    return {el:canvas,vis:true,resize:resize,start:start,stop:stop,
            setMouse:setMouse,clearMouse:clearMouse,still:still};
  }

  function init(){
    var cs=[].slice.call(document.querySelectorAll('canvas.tk-fx'));
    var fxs=cs.map(NetFx).filter(Boolean); if(!fxs.length)return;
    function each(fn){ fxs.forEach(fn); }
    function resizeAll(){ each(function(f){f.resize();}); }
    resizeAll(); addEventListener('resize',resizeAll); addEventListener('load',resizeAll);
    setTimeout(resizeAll,400); setTimeout(resizeAll,1000);

    if(REDUCE){ setTimeout(function(){ each(function(f){f.still();}); },500); return; }

    addEventListener('mousemove',function(e){ each(function(f){f.setMouse(e.clientX,e.clientY);}); },{passive:true});
    addEventListener('mouseout',function(e){ if(!e.relatedTarget && !e.toElement){ each(function(f){f.clearMouse();}); } });

    if(typeof IntersectionObserver!=='undefined'){
      var io=new IntersectionObserver(function(ents){
        ents.forEach(function(en){
          for(var i=0;i<fxs.length;i++){ if(fxs[i].el===en.target){
            fxs[i].vis=en.isIntersecting;
            if(en.isIntersecting) fxs[i].start(); else fxs[i].stop(); break; } }
        });
      },{threshold:0.02});
      each(function(f){ io.observe(f.el); });
    } else { each(function(f){f.start();}); }

    document.addEventListener('visibilitychange',function(){
      if(document.hidden) each(function(f){f.stop();});
      else each(function(f){ if(f.vis) f.start(); });
    });
  }
  if(document.readyState!=='loading')init(); else document.addEventListener('DOMContentLoaded',init);
})();
