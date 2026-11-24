/* =============================================================================
   Services index interactions.
   1) Lifecycle ring: the orbiting dot rotates clockwise (CSS). This script lights
      the node + caption the dot is currently nearest to, so activation is always
      in sync with the dot's real position (single source of truth).
   2) "Two Areas of Expertise": hover Infrastructure -> light Simplified IT
      Management + Reliable IT Operations; hover Cybersecurity -> light Stronger
      Business Continuity + Reduced Business Risk; hover Unified -> light all four.
   Both element-guarded.
   ========================================================================== */
(function () {
  // ---- 1) ring: a node glows ONLY while the orbiting dot is actually over it
  //        (pixel overlap, not "nearest") so it never lights before the dot
  //        reaches it. The caption holds the last node the dot touched. ----
  var dot = document.querySelector('.svl-adot');
  var nodes = [].slice.call(document.querySelectorAll('.svl-node'));
  var caps  = [].slice.call(document.querySelectorAll('.svl-cap'));
  var stage = document.querySelector('.svl-stage');
  if (dot && stage && nodes.length >= 2) {
    var litNow = -1, capNow = 0;
    function centre(el) { var r = el.getBoundingClientRect(); return { x: r.left + r.width / 2, y: r.top + r.height / 2, w: r.width }; }
    function tick() {
      var dc = centre(dot.querySelector('i'));
      var best = Infinity, bi = -1, touchR = 30;
      for (var i = 0; i < nodes.length; i++) {
        var ic = nodes[i].querySelector('.ic');
        var nc = centre(ic);
        touchR = nc.w * 0.5;                       // the dot must enter the icon's circle
        var d = Math.hypot(nc.x - dc.x, nc.y - dc.y);
        if (d < best) { best = d; bi = i; }
      }
      var over = (best <= touchR) ? bi : -1;        // node the dot is currently on (or none)
      if (over !== litNow) {
        litNow = over;
        for (var j = 0; j < nodes.length; j++) nodes[j].classList.toggle('lit', j === over);
      }
      if (over !== -1 && over !== capNow) {         // caption follows, and holds between nodes
        capNow = over;
        for (var k = 0; k < caps.length; k++) caps[k].classList.toggle('lit', k === over);
      }
      requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  }

  // ---- 2) two-areas hover ----
  var dia = document.querySelector('.sx-diagram');
  if (dia) {
    var benes = [].slice.call(document.querySelectorAll('.sx-benes .sx-bene'));
    if (benes.length >= 4) {
      function light(idxs) { for (var i = 0; i < benes.length; i++) benes[i].classList.toggle('on', idxs.indexOf(i) >= 0); }
      function clear() { for (var i = 0; i < benes.length; i++) benes[i].classList.remove('on'); }
      function bind(el, idxs) {
        if (!el) return;
        el.addEventListener('mouseenter', function () { light(idxs); });
        el.addEventListener('mouseleave', clear);
        el.addEventListener('focusin',  function () { light(idxs); });
        el.addEventListener('focusout', clear);
      }
      bind(dia.querySelector('.sx-acard:not(.cy)'), [2, 3]);
      bind(dia.querySelector('.sx-acard.cy'),       [0, 1]);
      bind(dia.querySelector('.sx-hub2'),           [0, 1, 2, 3]);
    }
  }
})();
