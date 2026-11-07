<?php
if (!isset($db)) $db = Database::getInstance();
if (empty($produk)) { http_response_code(404); require theme_path('templates/pages/404.php'); exit; }

$gallery = $db->fetchAll("SELECT * FROM produk_gallery WHERE produk_id=? ORDER BY urutan", [$produk['id']]);
$marketplace = $db->fetchAll("SELECT * FROM produk_marketplace WHERE produk_id=?", [$produk['id']]);
$wa_link = wa_url(get_setting('wa_number'), 'Halo, saya tertarik dengan produk "'.$produk['nama'].'". Boleh minta informasi lebih lanjut?');
$short_desc = trim($produk['short_description'] ?? '') ?: excerpt(strip_tags($produk['deskripsi'] ?? ''), 200);
$seo = [
  'title'       => seo_title($produk['meta_title'] ?: $produk['nama']),
  'description' => $produk['meta_description'] ?: $short_desc,
  'image'       => $produk['gambar_utama'] ? uploads_url($produk['gambar_utama']) : '',
];
$main_img = $produk['gambar_utama'] ?: ($gallery[0]['gambar'] ?? '');
include theme_path('templates/layouts/header.php');
?>

<section class="page-hero" style="padding-bottom:40px">
  <div class="inner" data-reveal>
    <div class="crumb"><a href="<?= url('/') ?>">Home</a><span class="sep">/</span><a href="<?= url('/produk') ?>">Produk</a><span class="sep">/</span><span><?= htmlspecialchars($produk['nama']) ?></span></div>
  </div>
</section>

<section class="section bg-white" style="padding-top:20px">
  <div class="container">
    <div class="prod-detail-grid">
      <div data-reveal="left">
        <div class="prod-gallery-main"><img id="prod-main-img" src="<?= uploads_url($main_img) ?>" alt="<?= htmlspecialchars($produk['nama']) ?>"></div>
        <?php if (!empty($gallery)): ?>
        <div class="prod-thumbs">
          <div class="prod-thumb active" onclick="rkProdThumb(this,'<?= uploads_url($main_img) ?>')"><img src="<?= uploads_url($main_img) ?>" alt=""></div>
          <?php foreach ($gallery as $g): ?>
            <div class="prod-thumb" onclick="rkProdThumb(this,'<?= uploads_url($g['gambar']) ?>')"><img src="<?= uploads_url($g['gambar']) ?>" alt="" loading="lazy"></div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <div data-reveal="right">
        <?php if (!empty($produk['badge'])): ?><span class="prod-badge" style="position:static;display:inline-block;margin-bottom:14px"><?= htmlspecialchars($produk['badge']) ?></span><?php endif; ?>
        <h1 style="font-size:var(--fs-h2)"><?= htmlspecialchars($produk['nama']) ?></h1>
        <div class="prod-price" style="margin-top:18px">
          <?php if ($produk['harga'] > 0): ?><span class="now" style="font-size:28px"><?= format_rupiah($produk['harga']) ?></span><?php else: ?><span class="now" style="font-size:24px">Hubungi Kami</span><?php endif; ?>
          <?php if (!empty($produk['harga_coret']) && $produk['harga_coret'] > $produk['harga']): ?><span class="was"><?= format_rupiah($produk['harga_coret']) ?></span><?php endif; ?>
        </div>
        <?php if ($short_desc): ?><p class="lead" style="margin-top:18px"><?= htmlspecialchars($short_desc) ?></p><?php endif; ?>

        <div style="margin-top:28px;display:flex;gap:12px;flex-wrap:wrap">
          <a href="<?= $wa_link ?>" target="_blank" rel="noopener" class="btn red"><span class="ic"><svg width="36" height="36" viewBox="0 0 24 24" fill="currentColor"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.16-.17.2-.35.22-.64.08-.3-.15-1.26-.46-2.39-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.92-2.21-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.21 3.07.15.2 2.1 3.2 5.08 4.49 2.98 1.29 2.98.86 3.52.8.54-.05 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35"/></svg></span>Pesan via WhatsApp</a>
        </div>

        <?php if (!empty($marketplace)): ?>
        <div style="margin-top:24px">
          <div class="testi-role" style="margin-bottom:10px">Beli juga di:</div>
          <div style="display:flex;gap:10px;flex-wrap:wrap">
            <?php foreach ($marketplace as $mp): ?>
              <a href="<?= htmlspecialchars($mp['url'] ?? '#') ?>" target="_blank" rel="noopener" class="btn ghost sm"><?= htmlspecialchars($mp['platform'] ?? 'Beli') ?></a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty(trim($produk['deskripsi'] ?? ''))): ?>
    <div style="max-width:800px;margin:64px auto 0" data-reveal>
      <h2 style="font-size:var(--fs-h3);margin-bottom:20px">Deskripsi Produk</h2>
      <div class="prose"><?= $produk['deskripsi'] ?></div>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include theme_path('templates/layouts/footer.php'); ?>
