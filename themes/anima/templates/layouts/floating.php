<?php
/**
 * Floating widgets (site-wide, included from footer.php):
 *   • Need Help  — bottom-left pill that expands quick contact/social links.
 *   • Virtual Assistant — bottom-right popup with a greeting, quick actions,
 *     and a message box that opens WhatsApp with the typed text.
 * Everything is CMS-driven from settings; a channel with no URL is skipped,
 * so there are never dead links. No emoji — Lucide-style SVG icons only.
 */
$__wa_raw = preg_replace('/\D/', '', (string) get_setting('wa_number', ''));
$__wa     = $__wa_raw !== '' ? 'https://wa.me/' . $__wa_raw : '';
$__soc = [
  'linkedin'  => trim((string) get_setting('linkedin_url', '')),
  'youtube'   => trim((string) get_setting('youtube_url', '')),
  'instagram' => trim((string) get_setting('instagram_url', '')),
  'facebook'  => trim((string) get_setting('facebook_url', '')),
];
$__soc = array_filter($__soc, fn($u) => $u !== '' && $u !== '#');

$__svg = [
  'wa'        => '<path d="M20 12a8 8 0 01-11.9 7L4 20l1.1-4A8 8 0 1120 12z"/><path d="M9 9c0 4 2 6 6 6"/>',
  'linkedin'  => '<path d="M4.98 3.5A2.5 2.5 0 102.5 6a2.5 2.5 0 002.48-2.5zM3 9h4v12H3zM10 9h3.8v1.7h.05c.53-1 1.83-2.06 3.77-2.06 4.03 0 4.78 2.65 4.78 6.1V21h-4v-5.4c0-1.29-.02-2.95-1.8-2.95-1.8 0-2.08 1.4-2.08 2.85V21h-4z"/>',
  'youtube'   => '<rect x="2" y="5" width="20" height="14" rx="4"/><path d="M10 9l5 3-5 3z"/>',
  'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/>',
  'facebook'  => '<path d="M15 8h-2a2 2 0 00-2 2v10M8 12h6"/>',
];
$__soc_label = ['wa'=>'WhatsApp Chat','linkedin'=>'LinkedIn','youtube'=>'YouTube','instagram'=>'Instagram','facebook'=>'Facebook'];

$__has_help = ($__wa !== '' || !empty($__soc));
$T = fn($k, $d) => function_exists('t') ? t($k, $d) : $d;
?>
<?php if ($__has_help): ?>
<!-- Need Help (bottom-left) -->
<div class="nh-widget" id="nhWidget">
  <div class="nh-list" id="nhList" hidden>
    <?php if ($__wa !== ''): ?>
      <a class="nh-item nh-wa" href="<?= htmlspecialchars($__wa) ?>" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24"><?= $__svg['wa'] ?></svg><span><?= htmlspecialchars($__soc_label['wa']) ?></span></a>
    <?php endif; ?>
    <?php foreach ($__soc as $net => $u): ?>
      <a class="nh-item" href="<?= htmlspecialchars($u) ?>" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24"><?= $__svg[$net] ?></svg><span><?= htmlspecialchars($__soc_label[$net]) ?></span></a>
    <?php endforeach; ?>
  </div>
  <button class="nh-toggle" id="nhToggle" type="button" aria-expanded="false" aria-controls="nhList">
    <svg class="nh-ic-open" viewBox="0 0 24 24"><path d="M12 18h.01"/><path d="M9.1 9a3 3 0 015.8 1c0 2-3 3-3 3"/><circle cx="12" cy="12" r="10"/></svg>
    <svg class="nh-ic-close" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
    <span><?= htmlspecialchars($T('need_help', 'Need Help')) ?></span>
  </button>
</div>
<?php endif; ?>

<!-- Virtual Assistant (bottom-right) -->
<div class="va-widget" id="vaWidget"<?= $__wa_raw !== '' ? ' data-wa="' . htmlspecialchars($__wa_raw) . '"' : '' ?>>
  <div class="va-panel" id="vaPanel" role="dialog" aria-label="<?= htmlspecialchars($T('va_title', 'Virtual Assistant')) ?>" hidden>
    <div class="va-head">
      <span class="va-ava"><svg viewBox="0 0 24 24"><rect x="4" y="8" width="16" height="11" rx="3"/><path d="M12 8V5M9 3h6"/><circle cx="9" cy="13" r="1"/><circle cx="15" cy="13" r="1"/></svg></span>
      <div class="va-id">
        <div class="va-name"><?= htmlspecialchars($T('va_title', 'Virtual Assistant')) ?></div>
        <div class="va-status"><i></i><?= htmlspecialchars($T('va_online', 'Online')) ?></div>
      </div>
      <button class="va-x" id="vaClose" type="button" aria-label="<?= htmlspecialchars($T('close', 'Tutup')) ?>"><svg viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
    </div>
    <div class="va-body">
      <div class="va-msg"><?= htmlspecialchars($T('va_greeting', 'Hi! Selamat datang di Sapta Tunas Teknologi. Ada yang bisa kami bantu?')) ?></div>
      <div class="va-quick">
        <a href="<?= htmlspecialchars(url('') . '#contact') ?>"><?= htmlspecialchars($T('request_proposal', 'Request Proposal')) ?></a>
        <a href="<?= htmlspecialchars(url('contact-us')) ?>"><?= htmlspecialchars($T('contact_us', 'Contact Us')) ?></a>
      </div>
    </div>
    <?php if ($__wa_raw !== ''): ?>
    <form class="va-input" id="vaForm">
      <input id="vaText" type="text" autocomplete="off" placeholder="<?= htmlspecialchars($T('va_placeholder', 'Ketik pesan Anda…')) ?>">
      <button type="submit" aria-label="<?= htmlspecialchars($T('send', 'Kirim')) ?>"><svg viewBox="0 0 24 24"><path d="M22 2 11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg></button>
    </form>
    <?php endif; ?>
  </div>
  <button class="va-toggle" id="vaToggle" type="button" aria-expanded="false" aria-controls="vaPanel" aria-label="<?= htmlspecialchars($T('va_title', 'Virtual Assistant')) ?>">
    <svg class="va-ic-open" viewBox="0 0 24 24"><rect x="4" y="8" width="16" height="11" rx="3"/><path d="M12 8V5M9 3h6"/><circle cx="9" cy="13" r="1"/><circle cx="15" cy="13" r="1"/></svg>
    <svg class="va-ic-close" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
  </button>
</div>

<script>
(function(){
  // Need Help toggle
  var nhT=document.getElementById('nhToggle'), nhW=document.getElementById('nhWidget'), nhL=document.getElementById('nhList');
  if(nhT){nhT.addEventListener('click',function(){
    var open=nhW.classList.toggle('open'); nhL.hidden=!open; nhT.setAttribute('aria-expanded',open?'true':'false');
  });}
  // Virtual Assistant toggle
  var vaT=document.getElementById('vaToggle'), vaW=document.getElementById('vaWidget'),
      vaP=document.getElementById('vaPanel'), vaX=document.getElementById('vaClose');
  function vaOpen(o){vaW.classList.toggle('open',o);vaP.hidden=!o;vaT.setAttribute('aria-expanded',o?'true':'false');
    if(o){if(nhW){nhW.classList.remove('open');if(nhL)nhL.hidden=true;if(nhT)nhT.setAttribute('aria-expanded','false');}
      var t=document.getElementById('vaText'); if(t) setTimeout(function(){t.focus();},60);}}
  if(vaT){vaT.addEventListener('click',function(){vaOpen(vaP.hidden);});}
  if(vaX){vaX.addEventListener('click',function(){vaOpen(false);});}
  // Assistant message box -> open WhatsApp with the typed text
  var vaF=document.getElementById('vaForm');
  if(vaF){vaF.addEventListener('submit',function(e){e.preventDefault();
    var wa=vaW.getAttribute('data-wa'); if(!wa)return;
    var v=(document.getElementById('vaText').value||'').trim();
    var url='https://wa.me/'+wa+(v?('?text='+encodeURIComponent(v)):'');
    window.open(url,'_blank','noopener');});}
  document.addEventListener('keydown',function(e){if(e.key==='Escape'){vaOpen(false);}});
})();
</script>
