<?php
if (!isset($db)) $db = Database::getInstance();
if (empty($produk)) { http_response_code(404); require theme_path('templates/pages/404.php'); exit; }

$gallery = $db->fetchAll("SELECT * FROM produk_gallery WHERE produk_id=? ORDER BY urutan", [$produk['id']]);
$marketplace = $db->fetchAll("SELECT * FROM produk_marketplace WHERE produk_id=?", [$produk['id']]);

// Categories (with clickable links)
try {
    $product_cats = $db->fetchAll(
        "SELECT k.id, k.nama, k.slug FROM produk_kategori k
         JOIN produk_kategori_rel r ON r.kategori_id = k.id
         WHERE r.produk_id = ?", [$produk['id']]
    );
} catch (Throwable $e) { $product_cats = []; }

$wa_link = wa_url(get_setting('wa_number'), 'Halo, saya tertarik dengan produk "'.$produk['nama'].'". Boleh minta informasi lebih lanjut?');

// Short description: explicit field, fallback to excerpt of full
$short_desc = trim($produk['short_description'] ?? '') ?: excerpt($produk['deskripsi'] ?? '', 200);
$full_desc  = trim($produk['deskripsi'] ?? '');

$seo = [
  'title' => seo_title($produk['meta_title'] ?: $produk['nama']),
  'description' => $produk['meta_description'] ?: $short_desc,
];

$mp_icons = ['Tokopedia'=>'🟢','Shopee'=>'🟠','Lazada'=>'🔵','Blibli'=>'🔵','Bukalapak'=>'🔴','Website'=>'🌐','Lainnya'=>'🛍️'];

include theme_path('templates/layouts/header.php');
?>

<section class="page-hero-wrap" style="padding-bottom:24px">
  <?php include theme_path('templates/partials/navbar.php'); ?>
  <div class="page-hero" style="padding:40px 24px 12px">
    <div class="page-hero-breadcrumb" style="display:flex;align-items:center;justify-content:center;gap:8px;flex-wrap:wrap">
      <a href="<?= url('/') ?>">Home</a>
      <span class="sep">/</span>
      <a href="<?= url('/produk') ?>">Produk</a>
      <span class="sep">/</span>
      <span style="display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:280px;vertical-align:bottom" title="<?= htmlspecialchars($produk['nama']) ?>"><?php
        $bc = $produk['nama']; echo htmlspecialchars(mb_strlen($bc) > 40 ? mb_substr($bc,0,40).'…' : $bc);
      ?></span>
    </div>
  </div>
</section>

<!-- WooCommerce-style product top: gallery LEFT | summary RIGHT -->
<section class="woo-product-top">
  <div class="container">
    <div class="woo-top-grid">
      <!-- Gallery -->
      <div class="woo-gallery">
        <div class="woo-main-img">
          <img id="main-img" src="<?= $produk['gambar_utama'] ? uploads_url($produk['gambar_utama']) : '' ?>"
               alt="<?= htmlspecialchars($produk['nama']) ?>">
        </div>
        <?php if (!empty($gallery) || $produk['gambar_utama']): ?>
        <div class="woo-thumbs">
          <?php if ($produk['gambar_utama']): ?>
            <button type="button" class="woo-thumb active" onclick="wooSwap(this,'<?= uploads_url($produk['gambar_utama']) ?>')">
              <img src="<?= uploads_url($produk['gambar_utama']) ?>" alt="">
            </button>
          <?php endif; ?>
          <?php foreach ($gallery as $gi): ?>
            <button type="button" class="woo-thumb" onclick="wooSwap(this,'<?= uploads_url($gi['gambar']) ?>')">
              <img src="<?= uploads_url($gi['gambar']) ?>" alt="">
            </button>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Summary (compact, short desc + CTA visible) -->
      <div class="woo-summary">
        <?php if (!empty($product_cats)): ?>
        <div class="woo-cats">
          <?php foreach ($product_cats as $cat): ?>
            <a href="<?= url('/produk?kategori=' . urlencode($cat['slug'])) ?>" class="woo-cat-chip"><?= htmlspecialchars($cat['nama']) ?></a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($produk['badge']): ?>
        <span class="woo-badge"><?= htmlspecialchars($produk['badge']) ?></span>
        <?php endif; ?>

        <h1 class="woo-title"><?= htmlspecialchars($produk['nama']) ?></h1>

        <div class="woo-price">
          <?php if ($produk['harga']): ?>
            <span class="woo-price-now"><?= format_rupiah($produk['harga']) ?></span>
            <?php if ($produk['harga_coret']): ?>
              <span class="woo-price-old"><?= format_rupiah($produk['harga_coret']) ?></span>
            <?php endif; ?>
          <?php else: ?>
            <span class="woo-price-now" style="font-size:22px">Hubungi Kami</span>
          <?php endif; ?>
        </div>

        <?php if ($short_desc): ?>
        <div class="woo-short-desc"><?= nl2br(htmlspecialchars($short_desc)) ?></div>
        <?php endif; ?>

        <!-- CTAs: green WhatsApp button (main) -->
        <a href="<?= $wa_link ?>" target="_blank" rel="noopener" class="woo-wa-cta">
          <span style="width:22px;height:22px;display:inline-flex"><?= social_icon_svg('whatsapp') ?></span>
          Chat WhatsApp Sekarang
        </a>

        <?php if (!empty($marketplace)): ?>
        <div class="woo-marketplace">
          <div class="woo-mp-label">Atau beli di:</div>
          <div class="woo-mp-list">
            <?php foreach ($marketplace as $mp): ?>
              <a href="<?= htmlspecialchars($mp['url']) ?>" target="_blank" rel="noopener" class="woo-mp-btn">
                <span><?= $mp_icons[$mp['platform']] ?? '🛍️' ?></span>
                <?= htmlspecialchars($mp['platform']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if ($produk['stok'] >= 0): ?>
        <div class="woo-stock">
          <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:<?= $produk['stok']>0?'#22c55e':'#ef4444' ?>"></span>
          <?= $produk['stok']>0 ? "Stok tersedia: <strong>{$produk['stok']}</strong>" : 'Stok habis' ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- Full description section (below) -->
<?php if ($full_desc && $full_desc !== $short_desc): ?>
<section class="woo-product-desc">
  <div class="container" style="max-width:900px">
    <h2 class="woo-desc-heading">Deskripsi Lengkap</h2>
    <div class="woo-desc-body"><?= nl2br(htmlspecialchars($full_desc)) ?></div>
  </div>
</section>
<?php endif; ?>

<script>
function wooSwap(btn, src) {
  document.getElementById('main-img').src = src;
  document.querySelectorAll('.woo-thumb').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
}
</script>

<?php include theme_path('templates/layouts/footer.php'); ?>
