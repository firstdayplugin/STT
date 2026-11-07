<?php
if (!isset($db)) $db = Database::getInstance();
if (empty($produk)) { http_response_code(404); require theme_path('templates/pages/404.php'); exit; }

$gallery     = $db->fetchAll("SELECT * FROM produk_gallery WHERE produk_id=? ORDER BY urutan", [$produk['id']]);
$marketplace = $db->fetchAll("SELECT * FROM produk_marketplace WHERE produk_id=?", [$produk['id']]);
$wa_link     = wa_url(get_setting('wa_number'), 'Halo, saya tertarik dengan properti "' . $produk['nama'] . '". Boleh minta informasi lebih lanjut?');
$short_desc  = trim($produk['short_description'] ?? '') ?: excerpt(strip_tags($produk['deskripsi'] ?? ''), 200);
$seo = [
  'title'       => seo_title($produk['meta_title'] ?: $produk['nama']),
  'description' => $produk['meta_description'] ?: $short_desc,
  'image'       => $produk['gambar_utama'] ? uploads_url($produk['gambar_utama']) : '',
];
$main_img = $produk['gambar_utama'] ?: ($gallery[0]['gambar'] ?? '');
include theme_path('templates/layouts/header.php');
?>

<div class="page-hero" style="padding-bottom:24px;">
  <div class="page-hero-inner" data-reveal>
    <div class="crumb">
      <a href="<?= url('/') ?>">Home</a><span class="sep">/</span>
      <a href="<?= url('/produk') ?>">Properti</a><span class="sep">/</span>
      <span><?= htmlspecialchars($produk['nama']) ?></span>
    </div>
  </div>
</div>

<section style="padding-block:40px var(--sec-y);padding-inline:var(--pad-x);background:var(--om-white);">
  <div style="max-width:var(--container-max);margin-inline:auto;">
    <div class="prod-detail-grid">
      <!-- Gallery -->
      <div data-reveal="left">
        <div class="prod-gallery-main">
          <?php if ($main_img): ?>
            <img id="prod-main-img" src="<?= uploads_url($main_img) ?>" alt="<?= htmlspecialchars($produk['nama']) ?>">
          <?php else: ?>
            <div style="width:100%;height:100%;background:var(--om-gray-lt);display:flex;align-items:center;justify-content:center;font-size:48px;color:var(--om-gray-mid);">🏠</div>
          <?php endif; ?>
        </div>
        <?php if (!empty($gallery) || $main_img): ?>
        <div class="prod-thumbs">
          <?php if ($main_img): ?>
          <div class="prod-thumb active" onclick="rkProdThumb(this,'<?= uploads_url($main_img) ?>')">
            <img src="<?= uploads_url($main_img) ?>" alt="">
          </div>
          <?php endif; ?>
          <?php foreach ($gallery as $g): ?>
          <div class="prod-thumb" onclick="rkProdThumb(this,'<?= uploads_url($g['gambar']) ?>')">
            <img src="<?= uploads_url($g['gambar']) ?>" alt="" loading="lazy">
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Info -->
      <div data-reveal="right">
        <?php if (!empty($produk['badge'])): ?>
          <span class="prod-badge-detail"><?= htmlspecialchars($produk['badge']) ?></span>
        <?php endif; ?>

        <h1 style="font-size:clamp(24px,2.5vw,36px);font-weight:600;line-height:1.2em;color:var(--om-dark);margin-bottom:18px;">
          <?= htmlspecialchars($produk['nama']) ?>
        </h1>

        <div class="prod-price" style="margin-bottom:18px;">
          <?php if ($produk['harga'] > 0): ?>
            <span class="now" style="font-size:28px;"><?= format_rupiah($produk['harga']) ?></span>
          <?php else: ?>
            <span class="now" style="font-size:22px;">Hubungi Kami</span>
          <?php endif; ?>
          <?php if (!empty($produk['harga_coret']) && $produk['harga_coret'] > $produk['harga']): ?>
            <span class="was"><?= format_rupiah($produk['harga_coret']) ?></span>
          <?php endif; ?>
        </div>

        <?php if ($short_desc): ?>
          <p style="font-size:18px;font-weight:300;line-height:1.5em;color:var(--om-gray);margin-bottom:24px;"><?= htmlspecialchars($short_desc) ?></p>
        <?php endif; ?>

        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:24px;">
          <a href="<?= $wa_link ?>" target="_blank" rel="noopener"
             style="display:inline-flex;align-items:center;gap:10px;background:#25d366;color:#fff;padding:12px 24px;border-radius:99px;font-size:16px;font-weight:600;text-decoration:none;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.16-.17.2-.35.22-.64.08-.3-.15-1.26-.46-2.39-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.92-2.21-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.21 3.07.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.69.62.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35M12.05 21.79a9.87 9.87 0 01-5.04-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.86 9.86 0 01-1.51-5.26c0-5.45 4.44-9.88 9.89-9.88 2.64 0 5.12 1.03 6.99 2.9a9.83 9.83 0 012.89 6.99c0 5.45-4.44 9.88-9.89 9.88m8.41-18.3A11.82 11.82 0 0012.05 0C5.5 0 .16 5.34.16 11.89c0 2.1.55 4.14 1.59 5.95L.06 24l6.3-1.65a11.88 11.88 0 005.69 1.45h.01c6.55 0 11.89-5.34 11.89-11.89 0-3.18-1.24-6.17-3.48-8.42"/></svg>
            Pesan via WhatsApp
          </a>
        </div>

        <?php if (!empty($marketplace)): ?>
        <div style="margin-top:8px;">
          <p style="font-size:13px;font-weight:300;color:var(--om-gray);margin-bottom:10px;">Beli juga di:</p>
          <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <?php foreach ($marketplace as $mp): ?>
              <a href="<?= htmlspecialchars($mp['url'] ?? '#') ?>" target="_blank" rel="noopener"
                 style="padding:8px 16px;border:1px solid var(--om-border);border-radius:8px;font-size:14px;font-weight:400;color:var(--om-dark);text-decoration:none;">
                <?= htmlspecialchars($mp['platform'] ?? 'Beli') ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Description -->
    <?php if (!empty(trim($produk['deskripsi'] ?? ''))): ?>
    <div style="max-width:800px;margin:64px auto 0;" data-reveal>
      <h2 style="font-size:clamp(20px,2vw,28px);font-weight:600;margin-bottom:20px;text-transform:none;">Deskripsi Properti</h2>
      <div class="prose"><?= $produk['deskripsi'] ?></div>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include theme_path('templates/layouts/footer.php'); ?>
