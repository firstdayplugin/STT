<?php
/**
 * Reusable Testimonial Carousel.
 * Configurable: autoplay, speed, cols (desktop/tablet/mobile), arrows, dots.
 * Usage: just include this partial. Optional: $ts_compact = true.
 */
if (!isset($db)) $db = Database::getInstance();
$ts_limit = max(1, (int) get_setting('testimonial_limit_home', '50'));
$ts_items = $db->fetchAll("SELECT * FROM testimonial WHERE is_active = 1 ORDER BY urutan ASC LIMIT $ts_limit");
if (empty($ts_items)) return;

$ts_cols_d = (int) get_setting('testimonial_cols_desktop', '3');
$ts_cols_t = (int) get_setting('testimonial_cols_tablet', '2');
$ts_cols_m = (int) get_setting('testimonial_cols_mobile', '1');
$ts_autoplay = get_setting('testimonial_autoplay', '1') === '1';
$ts_speed = max(2, (int) get_setting('testimonial_speed', '6')); // seconds
$ts_loop = get_setting('testimonial_loop', '1') === '1';
$ts_show_dots = get_setting('testimonial_show_dots', '1') === '1';
$ts_show_nav = get_setting('testimonial_show_nav', '0') === '1';
$ts_uid = 'ts' . substr(md5(uniqid()), 0, 6);

$ts_label = $testi_label ?? get_content('home', 'testi_label', 'Real Stories, Real Success');
$ts_title = $testi_title ?? get_content('home', 'testi_title', "Feedback from Our\nValued Clients");
$ts_desc  = $testi_desc  ?? get_content('home', 'testi_desc', 'Kepercayaan dan kepuasan klien menjadi bukti komitmen kami dalam menghadirkan solusi reklame yang berkualitas dan terpercaya.');
?>
<section class="testimonial-section">
  <div class="testimonial-wrap">
    <?php if (empty($ts_hide_header)): ?>
    <div class="testimonial-header">
      <div>
        <div class="section-label"><?= htmlspecialchars($ts_label) ?></div>
        <h2 class="section-title"><?= nl2br(htmlspecialchars($ts_title)) ?></h2>
      </div>
      <p class="testimonial-desc"><?= htmlspecialchars($ts_desc) ?></p>
    </div>
    <?php endif; ?>

    <div class="ts-carousel" id="<?= $ts_uid ?>"
         data-autoplay="<?= $ts_autoplay ? '1' : '0' ?>"
         data-speed="<?= $ts_speed * 1000 ?>"
         data-loop="<?= $ts_loop ? '1' : '0' ?>"
         data-cols-d="<?= $ts_cols_d ?>"
         data-cols-t="<?= $ts_cols_t ?>"
         data-cols-m="<?= $ts_cols_m ?>"
         style="--ts-d:<?= $ts_cols_d ?>;--ts-t:<?= $ts_cols_t ?>;--ts-m:<?= $ts_cols_m ?>">
      <?php if ($ts_show_nav && count($ts_items) > $ts_cols_d): ?>
      <button type="button" class="ts-nav ts-prev" aria-label="Sebelumnya" onclick="tsGo('<?= $ts_uid ?>',-1)">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
      </button>
      <?php endif; ?>
      <div class="ts-viewport">
        <div class="ts-track">
          <?php foreach ($ts_items as $i => $t): ?>
          <div class="ts-card<?= $i === 1 ? ' featured' : '' ?>" data-i="<?= $i ?>">
            <div class="ts-author">
              <?php if (!empty($t['foto'])): ?>
                <img src="<?= uploads_url($t['foto']) ?>" alt="<?= htmlspecialchars($t['nama']) ?>" class="ts-photo">
              <?php else: ?>
                <div class="ts-avatar"><?= strtoupper(substr($t['nama'], 0, 1)) ?></div>
              <?php endif; ?>
              <div>
                <div class="ts-name"><?= htmlspecialchars($t['nama']) ?></div>
                <div class="ts-role"><?= htmlspecialchars($t['jabatan']) ?></div>
              </div>
            </div>
            <p class="ts-text"><?= htmlspecialchars($t['isi']) ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php if ($ts_show_nav && count($ts_items) > $ts_cols_d): ?>
      <button type="button" class="ts-nav ts-next" aria-label="Berikutnya" onclick="tsGo('<?= $ts_uid ?>',1)">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
      </button>
      <?php endif; ?>
      <?php if ($ts_show_dots && count($ts_items) > $ts_cols_d): ?>
      <div class="ts-dots" id="<?= $ts_uid ?>-dots"></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<script>
(function(){
  if (window.tsInit) { window.tsInit('<?= $ts_uid ?>'); return; }
  window.tsInit = function(uid){
    const root = document.getElementById(uid);
    if (!root) return;
    const viewport = root.querySelector('.ts-viewport');
    const track = root.querySelector('.ts-track');
    const cards = track.querySelectorAll('.ts-card');
    const dotsWrap = document.getElementById(uid + '-dots');
    const total = cards.length;
    let perView = parseInt(root.dataset.colsD || 3);
    function updatePerView(){
      const w = window.innerWidth;
      if (w <= 640) perView = parseInt(root.dataset.colsM || 1);
      else if (w <= 1024) perView = parseInt(root.dataset.colsT || 2);
      else perView = parseInt(root.dataset.colsD || 3);
    }
    updatePerView();
    const totalPages = Math.max(1, Math.ceil(total / perView));
    let current = 0;
    let autoplayTimer = null;
    function go(idx){
      current = ((idx % totalPages) + totalPages) % totalPages;
      const pct = -(current * 100);
      track.style.transform = 'translateX(' + pct + '%)';
      if (dotsWrap) {
        dotsWrap.querySelectorAll('.ts-dot').forEach((d,i) => d.classList.toggle('active', i === current));
      }
    }
    // Build dots
    if (dotsWrap) {
      dotsWrap.innerHTML = '';
      for (let i = 0; i < totalPages; i++) {
        const d = document.createElement('button');
        d.type = 'button';
        d.className = 'ts-dot' + (i === 0 ? ' active' : '');
        d.setAttribute('aria-label', 'Slide ' + (i+1));
        d.addEventListener('click', () => { stopAuto(); go(i); startAuto(); });
        dotsWrap.appendChild(d);
      }
    }
    // Arrows (window-level for simplicity)
    window.tsGo = function(id, dir){
      const r = document.getElementById(id);
      if (!r) return;
      const ev = new CustomEvent('ts-dir', { detail: dir });
      r.dispatchEvent(ev);
    };
    root.addEventListener('ts-dir', e => { stopAuto(); go(current + e.detail); startAuto(); });
    // Autoplay
    function startAuto(){
      if (root.dataset.autoplay !== '1') return;
      stopAuto();
      autoplayTimer = setInterval(() => go(current + 1), parseInt(root.dataset.speed || 6000));
    }
    function stopAuto(){ if (autoplayTimer) clearInterval(autoplayTimer); }
    root.addEventListener('mouseenter', stopAuto);
    root.addEventListener('mouseleave', startAuto);
    // Swipe
    let startX = 0, swiping = false;
    viewport.addEventListener('touchstart', e => { startX = e.touches[0].clientX; swiping = true; stopAuto(); }, {passive:true});
    viewport.addEventListener('touchend', e => {
      if (!swiping) return; swiping = false;
      const dx = e.changedTouches[0].clientX - startX;
      if (Math.abs(dx) > 40) go(current + (dx < 0 ? 1 : -1));
      startAuto();
    });
    // Responsive: recalc on resize
    window.addEventListener('resize', () => { updatePerView(); go(0); });
    // Apply column widths via CSS var (each card flex-basis)
    track.style.setProperty('--ts-perview', perView);
    // Init
    go(0); startAuto();
  };
  window.tsInit('<?= $ts_uid ?>');
})();
</script>
