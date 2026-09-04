<?php
if (!isset($db)) $db = Database::getInstance();
$wa_contacts = $db->fetchAll('SELECT * FROM wa_contacts WHERE is_active = 1 ORDER BY urutan ASC');
if (empty($wa_contacts)) return;
$pesan_default = get_setting('wa_text', 'Halo, saya ingin berkonsultasi.');
$panel_title = get_setting('wa_panel_title', 'Hubungi Kami');
$jam = get_setting('wa_business_hours');

// Greeting bubble settings
$greeting_enabled = (int) get_setting('wa_greeting_enabled', '1');
$greeting_title   = get_setting('wa_greeting_title', 'Kami Online!');
$greeting_text    = get_setting('wa_greeting_text', 'Bagaimana saya bisa membantu Anda hari ini?');
$greeting_delay   = (int) (get_setting('wa_greeting_delay', '5')) * 1000; // ms
$greeting_once    = (int) get_setting('wa_greeting_once_per_session', '1');

// Google Ads conversion settings
$gads_enabled  = (int) get_setting('gads_conversion_enabled', '0');
$gads_id       = trim(get_setting('gads_conversion_id', ''));
$gads_label    = trim(get_setting('gads_conversion_label', ''));
?>

<?php if ($gads_enabled && $gads_id): ?>
<!-- Google Ads gtag.js (only loaded if conversion tracking enabled) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-<?= htmlspecialchars($gads_id) ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'AW-<?= htmlspecialchars($gads_id) ?>');
</script>
<?php endif; ?>

<div class="wa-float">
  <!-- Greeting bubble -->
  <?php if ($greeting_enabled): ?>
  <div class="wa-greeting" id="wa-greeting" role="dialog" aria-label="Greeting">
    <button class="wa-greeting-close" onclick="closeWAGreeting()" aria-label="Tutup">×</button>
    <div class="wa-greeting-title"><?= htmlspecialchars($greeting_title) ?></div>
    <div class="wa-greeting-text"><?= htmlspecialchars($greeting_text) ?></div>
  </div>
  <?php endif; ?>
  
  <!-- Contacts panel -->
  <div class="wa-float-panel" id="wa-panel">
    <div class="wa-panel-title"><?= htmlspecialchars($panel_title) ?></div>
    <div class="wa-panel-subtitle">
      <?= $jam ? htmlspecialchars($jam) : 'Tim kami siap membantu Anda' ?>
    </div>
    <?php foreach ($wa_contacts as $wa): ?>
      <a href="<?= wa_url($wa['nomor'], $pesan_default) ?>" target="_blank" class="wa-contact-item" 
         data-contact-id="<?= (int)$wa['id'] ?>"
         onclick="handleWAClick(this, <?= (int)$wa['id'] ?>); return true;">
        <div class="wa-contact-avatar">💬</div>
        <div>
          <div class="wa-contact-name"><?= htmlspecialchars($wa['nama']) ?></div>
          <div class="wa-contact-role"><?= htmlspecialchars($wa['deskripsi']) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
  
  <button class="wa-float-btn" onclick="toggleWAPanel()" aria-label="WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
  </button>
</div>

<script>
function toggleWAPanel() { 
  document.getElementById('wa-panel').classList.toggle('open'); 
  closeWAGreeting();
}

function closeWAGreeting() {
  const g = document.getElementById('wa-greeting');
  if (g) {
    g.classList.remove('show');
    try { sessionStorage.setItem('wa_greeting_dismissed', '1'); } catch(e){}
  }
}

function handleWAClick(el, contactId) {
  // Track to DB (analytics)
  try {
    fetch('<?= url('/api/wa-click') ?>', { 
      method:'POST', 
      headers:{'Content-Type':'application/x-www-form-urlencoded'}, 
      body:'contact_id='+contactId,
      keepalive: true
    });
  } catch(e){}
  
  <?php if ($gads_enabled && $gads_id && $gads_label): ?>
  // Fire Google Ads conversion event
  if (typeof gtag === 'function') {
    try {
      gtag('event', 'conversion', {
        'send_to': 'AW-<?= htmlspecialchars($gads_id) ?>/<?= htmlspecialchars($gads_label) ?>',
        'event_callback': function() { /* navigation happens via target=_blank */ }
      });
    } catch(e){ console.warn('GAds tracking failed:', e); }
  }
  <?php endif; ?>
  
  return true; // Allow link to proceed
}

// Auto-show greeting bubble
<?php if ($greeting_enabled): ?>
(function() {
  const delay = <?= (int)$greeting_delay ?>;
  const oncePerSession = <?= (int)$greeting_once ?>;
  
  function shouldShow() {
    if (!oncePerSession) return true;
    try { return sessionStorage.getItem('wa_greeting_dismissed') !== '1'; } catch(e){ return true; }
  }
  
  if (shouldShow()) {
    setTimeout(function() {
      const g = document.getElementById('wa-greeting');
      const panel = document.getElementById('wa-panel');
      if (g && (!panel || !panel.classList.contains('open'))) {
        g.classList.add('show');
      }
    }, delay);
  }
})();
<?php endif; ?>

document.addEventListener('click', function(e) {
  const wa = document.querySelector('.wa-float');
  if (wa && !wa.contains(e.target)) {
    document.getElementById('wa-panel').classList.remove('open');
  }
});
</script>
