<?php
if (!isset($db)) $db = Database::getInstance();
if (empty($layanan)) { http_response_code(404); require theme_path('templates/pages/404.php'); exit; }
$seo = [
  'title'       => seo_title($layanan['meta_title'] ?: $layanan['nama']),
  'description' => $layanan['meta_description'] ?: excerpt(strip_tags($layanan['deskripsi'] ?? ''), 160),
  'image'       => $layanan['gambar'] ? uploads_url($layanan['gambar']) : '',
];
include theme_path('templates/layouts/header.php');
?>
<?php $flex_position = 'layanan_detail_top'; $current_layanan_id = $layanan['id'] ?? null; include theme_path('templates/partials/flex-content.php'); ?>

<div class="page-hero">
  <div class="page-hero-inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><a href="<?= url('/layanan') ?>">Layanan</a><span class="sep">/</span><span><?= htmlspecialchars($layanan['nama']) ?></span></div>
    <h1 style="text-transform:none;"><?= htmlspecialchars($layanan['nama']) ?></h1>
  </div>
</div>

<section style="padding-block:var(--sec-y);padding-inline:var(--pad-x);background:var(--om-white);">
  <div style="max-width:var(--container-max);margin-inline:auto;display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:flex-start;">
    <?php if ($layanan['gambar']): ?>
    <div style="border-radius:var(--radius-lg);overflow:hidden;height:400px;" data-reveal="left">
      <img src="<?= uploads_url($layanan['gambar']) ?>" alt="<?= htmlspecialchars($layanan['nama']) ?>" style="width:100%;height:100%;object-fit:cover;">
    </div>
    <?php endif; ?>
    <div data-reveal="right">
      <h2 style="font-size:clamp(24px,2.5vw,36px);font-weight:600;margin-bottom:20px;text-transform:none;"><?= htmlspecialchars($layanan['nama']) ?></h2>
      <?php if (!empty(trim($layanan['deskripsi'] ?? ''))): ?>
      <div class="prose"><?= $layanan['deskripsi'] ?></div>
      <?php endif; ?>
      <?php $wa_link = wa_url(get_setting('wa_number'), 'Halo, saya tertarik dengan layanan "' . $layanan['nama'] . '".'); ?>
      <a href="<?= $wa_link ?>" target="_blank" rel="noopener"
         style="display:inline-flex;align-items:center;gap:10px;background:#25d366;color:#fff;padding:12px 24px;border-radius:99px;font-size:16px;font-weight:600;text-decoration:none;margin-top:24px;">
        Tanya via WhatsApp
      </a>
    </div>
  </div>
</section>

<?php $flex_position = 'layanan_detail_bottom'; include theme_path('templates/partials/flex-content.php'); ?>
<?php include theme_path('templates/layouts/footer.php'); ?>
