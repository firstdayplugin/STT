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
  // ---- 1) ring: light nearest node/caption to the orbiting dot ----
  var dot = document.querySelector('.svl-adot');
  var nodes = [].slice.call(document.querySelectorAll('.svl-node'));
  var caps  = [].slice.call(document.querySelectorAll('.svl-cap'));
  var stage = document.querySelector('.svl-stage');
  if (dot && stage && nodes.length >= 2) {
    var cur = -1;
    function centre(el) { var r = el.getBoundingClientRect(); return { x: r.left + r.width / 2, y: r.top + r.height / 2 }; }
    function tick() {
      var s = stage.getBoundingClientRect(), cx = s.left + s.width / 2, cy = s.top + s.height / 2;
      var dc = centre(dot.querySelector('i'));
      var best = Infinity, bi = 0;
      for (var i = 0; i < nodes.length; i++) {
        var nc = centre(nodes[i].querySelector('.ic'));
        var d = Math.hypot(nc.x - dc.x, nc.y - dc.y);
        if (d < best) { best = d; bi = i; }
      }
      if (bi !== cur) {
        cur = bi;
        for (var j = 0; j < nodes.length; j++) nodes[j].classList.toggle('lit', j === bi);
        for (var k = 0; k < caps.length; k++) caps[k].classList.toggle('lit', k === bi);
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
