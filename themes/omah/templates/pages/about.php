<?php
/* OMAH THEME — Tentang Kami
   #4: NO testimoni, NO logo klien at bottom
   All text from admin via get_content/get_setting */
if (!isset($db)) $db = Database::getInstance();
$page_seo = get_page_seo('about');
$seo = [
  'title'       => $page_seo['meta_title'] ?? seo_title('Tentang Kami'),
  'description' => $page_seo['meta_description'] ?? get_setting('site_description',''),
];
$site_name  = get_setting('site_name', 'DIFA Property');
$about_foto = get_setting('about_foto_1','') ?: get_setting('about_gambar','');
include theme_path('templates/layouts/header.php');
?>

<?php $flex_position = 'about_top'; include theme_path('templates/partials/flex-content.php'); ?>

<div class="page-hero">
  <div class="page-hero-inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><span><?= htmlspecialchars(get_content('about','hero_title','Tentang Kami')) ?></span></div>
    <h1 style="text-transform:none;"><?= htmlspecialchars(get_content('about','hero_title','Tentang Kami')) ?></h1>
    <?php $hero_desc = get_content('about','hero_desc',''); if ($hero_desc): ?><p><?= htmlspecialchars($hero_desc) ?></p><?php endif; ?>
  </div>
</div>

<section style="padding-block:var(--sec-y);padding-inline:var(--pad-x);background:var(--om-white);">
  <div style="max-width:var(--container-max);margin-inline:auto;display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center;">

    <!-- Left: Image from about_foto_1 -->
    <div data-reveal="left">
      <div style="width:100%;aspect-ratio:4/3;border-radius:var(--radius-lg);overflow:hidden;background:var(--om-gray-lt);">
        <?php if ($about_foto): ?>
          <img src="<?= uploads_url($about_foto) ?>" alt="<?= htmlspecialchars($site_name) ?>" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
        <?php else: ?>
          <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;padding:24px;text-align:center;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--om-gray-mid)" stroke-width="1.2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
            <p style="font-size:13px;color:var(--om-gray);line-height:1.5;max-width:180px;">Upload "Foto About 1" di Admin → Pengaturan → Gambar</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Right: Title + desc + adv_cards from admin -->
    <div data-reveal="right" style="display:flex;flex-direction:column;gap:24px;">
      <?php
        $about_title = get_content('about','title','');
        $about_label = get_content('about','label','');
        $about_body  = get_content('about','body', get_setting('site_description',''));
      ?>
      <?php if ($about_label): ?><span class="eyebrow"><?= htmlspecialchars($about_label) ?></span><?php endif; ?>
      <?php if ($about_title): ?>
        <h2 style="font-size:clamp(22px,2.5vw,36px);font-weight:600;line-height:1.2em;text-transform:none;color:var(--om-dark);"><?= htmlspecialchars($about_title) ?></h2>
      <?php endif; ?>
      <?php if ($about_body): ?>
        <p style="font-size:16px;font-weight:300;line-height:1.6em;color:var(--om-dark);"><?= nl2br(htmlspecialchars($about_body)) ?></p>
      <?php endif; ?>

      <!-- adv_card fields from admin -->
      <?php
        $adv_cards = [];
        for ($i = 1; $i <= 6; $i++) {
          $t = get_content('about', "adv_card{$i}_title", '');
          $d = get_content('about', "adv_card{$i}_desc",  '');
          if (empty(trim($t))) break;
          $adv_cards[] = ['title'=>$t,'desc'=>$d];
        }
      ?>
      <?php if (!empty($adv_cards)): ?>
      <div style="display:flex;flex-direction:column;gap:12px;">
        <?php foreach ($adv_cards as $card): ?>
        <div style="background:var(--om-white);border:1px solid var(--om-border);border-radius:12px;padding:18px 22px;display:flex;flex-direction:column;gap:6px;">
          <h3 style="font-size:16px;font-weight:600;color:var(--om-dark);margin:0;"><?= htmlspecialchars($card['title']) ?></h3>
          <?php if ($card['desc']): ?>
            <p style="font-size:14px;font-weight:300;line-height:1.5em;color:var(--om-gray);margin:0;"><?= htmlspecialchars($card['desc']) ?></p>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php $flex_position = 'about_middle'; include theme_path('templates/partials/flex-content.php'); ?>
<?php $flex_position = 'about_bottom'; include theme_path('templates/partials/flex-content.php'); ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
