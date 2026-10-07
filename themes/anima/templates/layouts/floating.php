<?php
/**
 * Floating widget (site-wide, included from footer.php):
 *   • Satu AI launcher — bottom-right round button carrying the Satu AI logo
 *     (rendered monochrome white so it blends with the brand-blue button),
 *     with a hover animation (lift + rotating ring) and an idle pulse halo.
 *   • Prompt tooltip (Crisp-style) — appears above the launcher with a
 *     typing-text greeting and two actions:
 *        1) Chat dengan Satu AI  → opens the assistant chat panel
 *        2) Chat ke WhatsApp     → opens wa.me with the CMS number
 *   • Assistant panel — greeting, quick links, and a message box that opens
 *     WhatsApp with the typed text.
 * Everything is CMS-driven; a channel with no URL is skipped (no dead links).
 * No emoji — Lucide-style / brand SVG icons only. Bilingual via t().
 */
$T = fn($k, $d) => function_exists('t') ? t($k, $d) : $d;

$__wa_raw = preg_replace('/\D/', '', (string) get_setting('wa_number', ''));
$__wa     = $__wa_raw !== '' ? 'https://wa.me/' . $__wa_raw : '';

// Satu AI mark: CMS upload overrides the bundled theme asset.
$__sa_logo = trim((string) get_setting('satu_ai_logo', ''));
$__sa_logo_url = $__sa_logo !== '' ? uploads_url($__sa_logo) : theme_url('assets/img/satu-ai.png');

// Typing-text lines for the tooltip (editable in CMS; names stay as-is).
$__sa_lines = array_values(array_filter([
  trim((string) $T('sa_line_1', 'Ada yang bisa Satu AI bantu?')),
  trim((string) $T('sa_line_2', 'Tanya apa saja, kapan saja.')),
  trim((string) $T('sa_line_3', 'Butuh info layanan STT?')),
], fn($v) => $v !== ''));
?>

<!-- Satu AI floating widget (bottom-right) -->
<div class="va-widget" id="vaWidget"<?= $__wa_raw !== '' ? ' data-wa="' . htmlspecialchars($__wa_raw) . '"' : '' ?> data-lines="<?= htmlspecialchars(json_encode($__sa_lines, JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>">

  <!-- Prompt tooltip -->
  <div class="sa-tip" id="saTip" role="dialog" aria-label="<?= htmlspecialchars($T('sa_title', 'Satu AI')) ?>" hidden>
    <button class="sa-tip-x" id="saTipClose" type="button" aria-label="<?= htmlspecialchars($T('close', 'Tutup')) ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
    <div class="sa-tip-head">
      <span class="sa-tip-logo"><img src="<?= htmlspecialchars($__sa_logo_url) ?>" alt="Satu AI"></span>
      <div class="sa-tip-hi"><span id="saType"></span><i class="sa-caret" aria-hidden="true"></i></div>
    </div>
    <div class="sa-tip-status"><i></i><span class="notranslate" translate="no"><?= htmlspecialchars($T('sa_status', 'Satu AI & tim online')) ?></span></div>
    <div class="sa-tip-acts">
      <button class="sa-btn sa-btn-ai" id="saOpenChat" type="button">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a9 9 0 0 1 9 9 9 9 0 0 1-9 9c-1.4 0-2.7-.3-3.9-.9L3 21l1.1-4.1A9 9 0 0 1 12 3z"/><path d="M8 11h.01M12 11h.01M16 11h.01"/></svg>
        <span><?= htmlspecialchars($T('sa_btn_ai', 'Chat dengan Satu AI')) ?></span>
      </button>
      <?php if ($__wa !== ''): ?>
      <a class="sa-btn sa-btn-wa" href="<?= htmlspecialchars($__wa) ?>" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.945C.16 5.335 5.495 0 12.05 0a11.82 11.82 0 018.413 3.488 11.82 11.82 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884a9.82 9.82 0 001.519 5.26l-.999 3.648 3.97-1.027zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.372-.025-.521-.074-.148-.669-1.611-.916-2.206-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
        <span><?= htmlspecialchars($T('sa_btn_wa', 'Chat ke WhatsApp')) ?></span>
      </a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Assistant chat panel -->
  <div class="va-panel" id="vaPanel" role="dialog" aria-label="<?= htmlspecialchars($T('sa_title', 'Satu AI')) ?>" hidden>
    <div class="va-head">
      <span class="va-ava"><img src="<?= htmlspecialchars($__sa_logo_url) ?>" alt="Satu AI"></span>
      <div class="va-id">
        <div class="va-name notranslate" translate="no"><?= htmlspecialchars($T('sa_title', 'Satu AI')) ?></div>
        <div class="va-status"><i></i><?= htmlspecialchars($T('va_online', 'Online')) ?></div>
      </div>
      <button class="va-x" id="vaClose" type="button" aria-label="<?= htmlspecialchars($T('close', 'Tutup')) ?>"><svg viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
    </div>
    <div class="va-body">
      <div class="va-msg"><?= htmlspecialchars($T('va_greeting', 'Hi! Selamat datang di Sapta Tunas Teknologi. Ada yang bisa kami bantu?')) ?></div>
      <div class="va-quick">
        <a href="<?= htmlspecialchars(url('contact-us') . '#request-proposal') ?>"><?= htmlspecialchars($T('request_proposal', 'Request Proposal')) ?></a>
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

  <!-- Launcher -->
  <button class="va-toggle" id="vaToggle" type="button" aria-expanded="false" aria-controls="saTip" aria-label="<?= htmlspecialchars($T('sa_title', 'Satu AI')) ?>">
    <span class="va-logo"><img src="<?= htmlspecialchars($__sa_logo_url) ?>" alt="Satu AI"></span>
    <svg class="va-ic-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
  </button>
</div>

<script>
(function(){
  var W = document.getElementById('vaWidget');
  if(!W) return;
  var toggle  = document.getElementById('vaToggle'),
      panel   = document.getElementById('vaPanel'),
      paClose = document.getElementById('vaClose'),
      tip     = document.getElementById('saTip'),
      tipX    = document.getElementById('saTipClose'),
      openChat= document.getElementById('saOpenChat'),
      typeEl  = document.getElementById('saType');

  function setPanel(o){
    W.classList.toggle('open', o);
    if(panel) panel.hidden = !o;
    toggle.setAttribute('aria-expanded', o ? 'true' : 'false');
    if(o){ setTip(false); var t=document.getElementById('vaText'); if(t) setTimeout(function(){t.focus();},60); }
  }
  function setTip(o){
    W.classList.toggle('tip-open', o);
    if(tip) tip.hidden = !o;
  }

  // Launcher: close the panel if open, otherwise toggle the prompt tooltip.
  toggle.addEventListener('click', function(){
    if(W.classList.contains('open')){ setPanel(false); return; }
    setTip(!W.classList.contains('tip-open'));
  });
  if(paClose) paClose.addEventListener('click', function(){ setPanel(false); });
  if(tipX) tipX.addEventListener('click', function(){
    setTip(false);
    try{ localStorage.setItem('saTipDismissed','1'); }catch(e){}
  });
  if(openChat) openChat.addEventListener('click', function(){ setPanel(true); });

  // Assistant message box -> open WhatsApp with the typed text.
  var vaF = document.getElementById('vaForm');
  if(vaF){ vaF.addEventListener('submit', function(e){ e.preventDefault();
    var wa = W.getAttribute('data-wa'); if(!wa) return;
    var v = (document.getElementById('vaText').value||'').trim();
    window.open('https://wa.me/'+wa+(v?('?text='+encodeURIComponent(v)):''), '_blank', 'noopener');
  }); }

  document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ setPanel(false); setTip(false); } });

  // Auto-reveal the tooltip once per visitor, shortly after load.
  var dismissed=false; try{ dismissed = localStorage.getItem('saTipDismissed')==='1'; }catch(e){}
  if(!dismissed){ setTimeout(function(){ if(!W.classList.contains('open')) setTip(true); }, 1400); }

  // Typing-text animation (loops through the CMS lines).
  var phrases=[]; try{ phrases = JSON.parse(W.getAttribute('data-lines')||'[]'); }catch(e){}
  if(typeEl && phrases.length){
    var pi=0, ci=0, del=false;
    (function tick(){
      var p = phrases[pi] || '';
      if(!del){ typeEl.textContent = p.slice(0, ++ci); if(ci>=p.length){ del=true; return setTimeout(tick, 1600); } }
      else    { typeEl.textContent = p.slice(0, --ci); if(ci<=0){ del=false; pi=(pi+1)%phrases.length; } }
      setTimeout(tick, del ? 34 : 66);
    })();
  }
})();
</script>
