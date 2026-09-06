<?php
if (!isset($db)) $db = Database::getInstance();
$wa_contacts = $db->fetchAll('SELECT * FROM wa_contacts WHERE is_active = 1 ORDER BY urutan ASC');
if (empty($wa_contacts)) return;
$pesan = get_setting('wa_text', 'Halo, saya ingin berkonsultasi.');
$panel_title = get_setting('wa_panel_title', 'Hubungi Kami');
$jam = get_setting('wa_business_hours');
$g_on    = (int) get_setting('wa_greeting_enabled', '1');
$g_title = get_setting('wa_greeting_title', 'Kami Online!');
$g_text  = get_setting('wa_greeting_text', 'Bagaimana kami bisa membantu Anda hari ini?');
$g_delay = (int) (get_setting('wa_greeting_delay', '5')) * 1000;
$wa_ic = '<svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.16-.17.2-.35.22-.64.08-.3-.15-1.26-.46-2.39-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.92-2.21-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.21 3.07.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.69.62.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35M12.05 21.79a9.87 9.87 0 01-5.04-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.86 9.86 0 01-1.51-5.26c0-5.45 4.44-9.88 9.89-9.88 2.64 0 5.12 1.03 6.99 2.9a9.83 9.83 0 012.89 6.99c0 5.45-4.44 9.88-9.89 9.88m8.41-18.3A11.82 11.82 0 0012.05 0C5.5 0 .16 5.34.16 11.89c0 2.1.55 4.14 1.59 5.95L.06 24l6.3-1.65a11.88 11.88 0 005.69 1.45h.01c6.55 0 11.89-5.34 11.89-11.89 0-3.18-1.24-6.17-3.48-8.42"/></svg>';
?>
<div class="wa-float">
  <?php if ($g_on): ?>
  <div class="wa-greeting" id="wa-greeting" role="dialog">
    <button class="wa-greeting-close" onclick="rkCloseGreeting()" aria-label="Tutup">&times;</button>
    <div class="wa-greeting-title"><?= htmlspecialchars($g_title) ?></div>
    <div class="wa-greeting-text"><?= htmlspecialchars($g_text) ?></div>
  </div>
  <?php endif; ?>
  <div class="wa-panel" id="wa-panel">
    <div class="wa-panel-title"><?= htmlspecialchars($panel_title) ?></div>
    <div class="wa-panel-sub"><?= $jam ? htmlspecialchars($jam) : 'Tim kami siap membantu Anda' ?></div>
    <?php foreach ($wa_contacts as $wa): ?>
      <a href="<?= wa_url($wa['nomor'], $pesan) ?>" target="_blank" rel="noopener" class="wa-contact"
         onclick="rkWaClick(<?= (int)$wa['id'] ?>); return true;">
        <span class="wa-avatar"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0a12 12 0 100 24 12 12 0 000-24zm0 6a3 3 0 110 6 3 3 0 010-6zm0 14.2a7.2 7.2 0 01-5.5-2.6c.03-1.8 3.66-2.8 5.5-2.8s5.47 1 5.5 2.8A7.2 7.2 0 0112 20.2z"/></svg></span>
        <span>
          <span class="wa-cname"><?= htmlspecialchars($wa['nama']) ?></span><br>
          <span class="wa-crole"><?= htmlspecialchars($wa['deskripsi']) ?></span>
        </span>
      </a>
    <?php endforeach; ?>
  </div>
  <button class="wa-btn" onclick="rkToggleWa()" aria-label="WhatsApp"><?= $wa_ic ?></button>
</div>
<script>
function rkToggleWa(){var p=document.getElementById('wa-panel');p.classList.toggle('open');rkCloseGreeting();}
function rkCloseGreeting(){var g=document.getElementById('wa-greeting');if(g){g.classList.remove('show');try{sessionStorage.setItem('wa_g','1');}catch(e){}}}
function rkWaClick(id){try{fetch('<?= url('/api/wa-click') ?>',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'contact_id='+id,keepalive:true});}catch(e){}return true;}
<?php if ($g_on): ?>
(function(){var show=true;try{show=sessionStorage.getItem('wa_g')!=='1';}catch(e){}if(show){setTimeout(function(){var g=document.getElementById('wa-greeting'),p=document.getElementById('wa-panel');if(g&&(!p||!p.classList.contains('open')))g.classList.add('show');},<?= (int)$g_delay ?>);}})();
<?php endif; ?>
document.addEventListener('click',function(e){var w=document.querySelector('.wa-float');if(w&&!w.contains(e.target)){document.getElementById('wa-panel').classList.remove('open');}});
</script>
