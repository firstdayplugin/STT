<?php
/**
 * Anima theme — Home page.
 * PROJECT RULE #1: Home uses design/master/home.html (NOT the Figma Home).
 * Static text is editable via hc('key') -> get_content('home', key) with defaults in home.registry.php
 * (§14 content registry). Header/footer live in layouts/.
 * TODO(next passes): hero slider / orbit / prism content is JS-driven (anima.js) -> inject config
 * PHP->JS (CSP-safe, §14.2); news cards -> blog module; testimonial cards -> testimonial module;
 * media (hero/portfolio bg, card images) -> uploads/ (replaces Pexels placeholders).
 */
require_once __DIR__ . '/../../inc/anima.php';
?>

<!-- ===== HERO (dark cinematic WebGL) ===== -->
<section class="v3hero" id="hero">
  <div class="v3hero-in">
    <div class="tk-stage">
      <div class="tk-cur" id="tkCur">
        <div class="tk-bg" id="tkCurBg" style="background-image:url(https://images.pexels.com/photos/17489163/pexels-photo-17489163.jpeg?auto=compress&cs=tinysrgb&w=1600)"></div>
        <div class="tk-ov"></div>
        <div class="tk-eyebrow" id="tkEye">Technology Industry</div>
        <h1 class="tk-h1" id="tkH1">Growing The Global</h1>
        <div class="tk-foot">
          <a class="tk-btn" href="#contact">Get in Touch <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
          <div class="tk-dots" id="tkDots"></div>
        </div>
      </div>
      <div class="tk-next" id="tkNext">
        <div class="tk-bg" id="tkNextBg" style="background-image:url(https://images.pexels.com/photos/17323801/pexels-photo-17323801.jpeg?auto=compress&cs=tinysrgb&w=1000)"></div>
        <div class="tk-ov2"></div>
      </div>
      <!-- Telkom corner masks sit OUTSIDE the image clipping containers. This is important: the SVG must be able to overlap the image edge cleanly. -->
      <div class="tk-tab" id="tkTab"></div>
      <div class="tk-navpod">
        <button class="np-btn" id="tkPrev" aria-label="Previous"><svg viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6"/></svg></button>
        <button class="np-btn np-active" id="tkNextBtn" aria-label="Next"><svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg></button>
      </div>
    </div>
    <div class="tk-below">
      <div class="tk-below-l">
        <h2 class="tk-sub"><span class="blue" id="tkSub">Technology Industry</span></h2>
        <p><?= hc('hero_subcopy') ?></p>
      </div>
      <div class="tk-discover">
        <div class="tk-disc-h"><?= hc('discover_heading') ?></div>
        <div class="tk-disc-grid">
          <a href="#portfolio"><?= hc('discover_portfolio') ?> <i>↗</i></a>
          <a href="#news"><?= hc('discover_news') ?> <i>↗</i></a>
          <a href="#solutions"><?= hc('discover_solution') ?> <i>↗</i></a>
          <a href="#why"><?= hc('discover_why') ?> <i>↗</i></a>
          <a href="#industries"><?= hc('discover_industries') ?> <i>↗</i></a>
          <a href="#testimonials"><?= hc('discover_testi') ?> <i>↗</i></a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== PORTFOLIO ===== -->
<!-- ===== OUR PORTFOLIO (scroll-driven) ===== -->
<section class="tfi" id="portfolio">
  <div class="tfi-pin">
    <div class="tfi-stage" id="tfiStage">
      <div class="tfi-media" id="tfiMedia">
        <div class="tfi-img on" data-i="0" style="background-image:url(https://images.pexels.com/photos/586104/pexels-photo-586104.jpeg?auto=compress&cs=tinysrgb&w=1400)"></div><div class="tfi-img" data-i="1" style="background-image:url(https://images.pexels.com/photos/36714208/pexels-photo-36714208.jpeg?auto=compress&cs=tinysrgb&w=1400)"></div><div class="tfi-img" data-i="2" style="background-image:url(https://images.pexels.com/photos/10376257/pexels-photo-10376257.jpeg?auto=compress&cs=tinysrgb&w=1400)"></div><div class="tfi-img" data-i="3" style="background-image:url(https://images.pexels.com/photos/37730211/pexels-photo-37730211.jpeg?auto=compress&cs=tinysrgb&w=1400)"></div>
        <div class="tfi-media-ov"></div>
      </div>
      <div class="tfi-panel" id="tfiPanel">
        <div class="tfi-eyebrow"><?= hc('portfolio_eyebrow') ?></div>
        <h2 class="tfi-title"><?= hc('portfolio_title') ?></h2>
        <p class="tfi-lead"><?= hc('portfolio_lead') ?></p>
        <div class="tfi-list" id="tfiList">
          <div class="tfi-prog"><span class="tfi-prog-fill" id="tfiFill"></span></div>
          <div class="tfi-item act" data-i="0"><h3><b><?= hc('pf1_num') ?></b> <?= hc('pf1_label') ?></h3><div class="tfi-d"><?= hc('pf1_desc') ?></div></div>
          <div class="tfi-item dim" data-i="1"><h3><b><?= hc('pf2_num') ?></b> <?= hc('pf2_label') ?></h3><div class="tfi-d"><?= hc('pf2_desc') ?></div></div>
          <div class="tfi-item dim" data-i="2"><h3><b><?= hc('pf3_num') ?></b> <?= hc('pf3_label') ?></h3><div class="tfi-d"><?= hc('pf3_desc') ?></div></div>
          <div class="tfi-item dim" data-i="3"><h3><b><?= hc('pf4_num') ?></b> <?= hc('pf4_label') ?></h3><div class="tfi-d"><?= hc('pf4_desc') ?></div></div>
          </div>
      </div>
    </div>
  </div>
</section>



<!-- ===== PRISM (WebGL scroll story) ===== -->
<section class="prism" id="solutions">
  <div class="stage">
    <canvas id="prismCanvas"></canvas>
    <div class="prism-hint" id="prismHint">Scroll</div>
    <div class="prism-overlay">
      <div class="prism-caps" id="prismCaps"></div>
      <div class="prism-dots" id="prismDots"></div>
      <div class="prism-logos" id="prismLogos"></div>
    </div>
  </div>
</section>

<!-- ===== STATEMENT (scroll word-reveal, pemisah Solutions & Industries) ===== -->
<section class="statement" id="statement">
  <div class="stmt-sticky">
    <div class="wrap">
      <p class="stmt" id="stmtText"><?= hc('statement_text') ?></p>
    </div>
  </div>
</section>

<!-- ===== ORBIT (WebGL) ===== -->
<section class="ind2" id="industries">
  <div class="ind2-cards" id="ind2cards">
      <a class="ind2-card" href="#"><span class="ex">EXPLORE →</span><span class="lbl"><?= hc('ind1') ?></span></a>
      <a class="ind2-card" href="#"><span class="ex">EXPLORE →</span><span class="lbl"><?= hc('ind2') ?></span></a>
      <a class="ind2-card" href="#"><span class="ex">EXPLORE →</span><span class="lbl"><?= hc('ind3') ?></span></a>
      <a class="ind2-card" href="#"><span class="ex">EXPLORE →</span><span class="lbl"><?= hc('ind4') ?></span></a>
      <a class="ind2-card" href="#"><span class="ex">EXPLORE →</span><span class="lbl"><?= hc('ind5') ?></span></a>
      <a class="ind2-card" href="#"><span class="ex">EXPLORE →</span><span class="lbl"><?= hc('ind6') ?></span></a>
      <a class="ind2-card" href="#"><span class="ex">EXPLORE →</span><span class="lbl"><?= hc('ind7') ?></span></a>
      <a class="ind2-card" href="#"><span class="ex">EXPLORE →</span><span class="lbl"><?= hc('ind8') ?></span></a>
  </div>
  <div class="ind2-center">
    <div class="ind2-eye"><?= hc('industries_eyebrow') ?></div>
    <h2 class="ind2-word"><?= hc('industries_title') ?></h2>
    <div class="ind2-sub"><?= hc('industries_sub') ?></div>
  </div>
</section>



<!-- ===== NEWS ===== -->
<section class="news" id="news">
  <div class="news-aurora"><i class="n1"></i><i class="n2"></i></div>
  <div class="wrap news-in">
    <div class="news-intro">
      <h2><?= hc('news_heading') ?></h2>
      <div class="news-line"></div>
      <p><?= hc('news_intro') ?></p>
      <div class="news-nav">
        <button class="nprev" aria-label="Previous"><svg viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6"/></svg></button>
        <button class="nnext" aria-label="Next"><svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg></button>
      </div>
    </div>
    <div class="news-viewport">
      <div class="news-track" id="newsTrack">
        <article class="ncard">
          <div class="ncard-img">
            <img src="https://images.pexels.com/photos/36169769/pexels-photo-36169769.jpeg?auto=compress&cs=tinysrgb&w=1000" data-fallback="bg" alt="" loading="lazy" decoding="async">
            <div class="ncard-ribbon"><span class="date">July 9, 2026</span><span class="cat">Awards</span></div>
          </div>
          <div class="ncard-body">
            <h3>Mengatasi Kompleksitas Jaringan Enterprise Lewat Pendekatan Otomatisasi Cisco AgenticOps</h3>
            <p>Paradoks baru dunia TI: sisi positif dan tantangan di balik kehadiran AI. Perkembangan teknologi kecerdasan buatan telah membawa industri…</p>
            <a class="read" href="#">Read More <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
          </div>
        </article>
        <article class="ncard">
          <div class="ncard-img">
            <img src="https://images.pexels.com/photos/17489163/pexels-photo-17489163.jpeg?auto=compress&cs=tinysrgb&w=1000" data-fallback="bg" alt="" loading="lazy" decoding="async">
            <div class="ncard-ribbon"><span class="date">February 9, 2026</span><span class="cat">Articles & News</span></div>
          </div>
          <div class="ncard-body">
            <h3>Membangun Infrastruktur Cloud yang Resilient untuk Skala Enterprise</h3>
            <p>Strategi arsitektur cloud modern yang menjaga uptime, keamanan, dan efisiensi biaya di tengah pertumbuhan bisnis yang pesat…</p>
            <a class="read" href="#">Read More <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
          </div>
        </article>
        <article class="ncard">
          <div class="ncard-img">
            <img src="https://images.pexels.com/photos/32324512/pexels-photo-32324512.jpeg?auto=compress&cs=tinysrgb&w=1000" data-fallback="bg" alt="" loading="lazy" decoding="async">
            <div class="ncard-ribbon"><span class="date">January 22, 2026</span><span class="cat">Insight</span></div>
          </div>
          <div class="ncard-body">
            <h3>Peran Generative AI dalam Transformasi Layanan Kesehatan</h3>
            <p>Bagaimana AI membantu diagnosa lebih cepat dan akurat, serta mengubah pengalaman pasien di rumah sakit pintar masa kini…</p>
            <a class="read" href="#">Read More <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
          </div>
        </article>
        <article class="ncard">
          <div class="ncard-img">
            <img src="https://images.pexels.com/photos/17489158/pexels-photo-17489158.jpeg?auto=compress&cs=tinysrgb&w=1000" data-fallback="bg" alt="" loading="lazy" decoding="async">
            <div class="ncard-ribbon"><span class="date">December 3, 2025</span><span class="cat">Security</span></div>
          </div>
          <div class="ncard-body">
            <h3>Kerangka Cybersecurity Menyeluruh untuk Bisnis Digital</h3>
            <p>Pendekatan zero-trust dan managed SOC untuk melindungi aset digital serta menjaga kontinuitas operasional perusahaan…</p>
            <a class="read" href="#">Read More <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
          </div>
        </article>
      </div>
    </div>
  </div>
</section>

<!-- ===== WHY US ===== -->
<section class="why2" id="why">
  <div class="why2-head">
    <div class="why2-eye"><?= hc('why_eyebrow') ?></div>
    <h2><?= hc('why_title') ?></h2>
    <p><?= hc('why_intro') ?></p>
  </div>
  <div class="whyloop">
    <div class="whyloop-track" id="whyTrack">
        <div class="wl-card">
          <div class="wl-img"><img src="https://images.pexels.com/photos/17489163/pexels-photo-17489163.jpeg?auto=compress&cs=tinysrgb&w=900" data-fallback="remove" alt="" loading="lazy" decoding="async"></div>
          <div class="wl-body"><span class="wl-num">1</span><div class="wl-t"><?= hc('why1_title') ?></div><div class="wl-s"><?= hc('why1_sub') ?></div></div>
        </div>
        <div class="wl-card">
          <div class="wl-img"><img src="https://images.pexels.com/photos/17489150/pexels-photo-17489150.jpeg?auto=compress&cs=tinysrgb&w=900" data-fallback="remove" alt="" loading="lazy" decoding="async"></div>
          <div class="wl-body"><span class="wl-num">2</span><div class="wl-t"><?= hc('why2_title') ?></div><div class="wl-s"><?= hc('why2_sub') ?></div></div>
        </div>
        <div class="wl-card">
          <div class="wl-img"><img src="https://images.pexels.com/photos/37730211/pexels-photo-37730211.jpeg?auto=compress&cs=tinysrgb&w=900" data-fallback="remove" alt="" loading="lazy" decoding="async"></div>
          <div class="wl-body"><span class="wl-num">3</span><div class="wl-t"><?= hc('why3_title') ?></div><div class="wl-s"><?= hc('why3_sub') ?></div></div>
        </div>
        <div class="wl-card">
          <div class="wl-img"><img src="https://images.pexels.com/photos/10376257/pexels-photo-10376257.jpeg?auto=compress&cs=tinysrgb&w=900" data-fallback="remove" alt="" loading="lazy" decoding="async"></div>
          <div class="wl-body"><span class="wl-num">4</span><div class="wl-t"><?= hc('why4_title') ?></div><div class="wl-s"><?= hc('why4_sub') ?></div></div>
        </div><div class="wl-card">
          <div class="wl-img"><img src="https://images.pexels.com/photos/17489163/pexels-photo-17489163.jpeg?auto=compress&cs=tinysrgb&w=900" data-fallback="remove" alt="" loading="lazy" decoding="async"></div>
          <div class="wl-body"><span class="wl-num">1</span><div class="wl-t"><?= hc('why1_title') ?></div><div class="wl-s"><?= hc('why1_sub') ?></div></div>
        </div>
        <div class="wl-card">
          <div class="wl-img"><img src="https://images.pexels.com/photos/17489150/pexels-photo-17489150.jpeg?auto=compress&cs=tinysrgb&w=900" data-fallback="remove" alt="" loading="lazy" decoding="async"></div>
          <div class="wl-body"><span class="wl-num">2</span><div class="wl-t"><?= hc('why2_title') ?></div><div class="wl-s"><?= hc('why2_sub') ?></div></div>
        </div>
        <div class="wl-card">
          <div class="wl-img"><img src="https://images.pexels.com/photos/37730211/pexels-photo-37730211.jpeg?auto=compress&cs=tinysrgb&w=900" data-fallback="remove" alt="" loading="lazy" decoding="async"></div>
          <div class="wl-body"><span class="wl-num">3</span><div class="wl-t"><?= hc('why3_title') ?></div><div class="wl-s"><?= hc('why3_sub') ?></div></div>
        </div>
        <div class="wl-card">
          <div class="wl-img"><img src="https://images.pexels.com/photos/10376257/pexels-photo-10376257.jpeg?auto=compress&cs=tinysrgb&w=900" data-fallback="remove" alt="" loading="lazy" decoding="async"></div>
          <div class="wl-body"><span class="wl-num">4</span><div class="wl-t"><?= hc('why4_title') ?></div><div class="wl-s"><?= hc('why4_sub') ?></div></div>
        </div>
    </div>
  </div>
</section>

<!-- ===== WHAT THEY SAY (OpenAI-style) ===== -->
<section class="testi" id="testimonials">
  <div class="wrap">
    <div class="testi-head">
      <div class="testi-eye"><?= hc('testi_eyebrow') ?></div>
      <h2><?= hc('testi_title') ?></h2>
      <p><?= hc('testi_intro') ?></p>
    </div>
    <div class="tst-grid">
      <a class="tcard" href="#testimonial-detail">
        <span class="tbadge video"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z" fill="currentColor"/></svg>Video</span>
        <p class="quote">&ldquo;Kami sangat mengapresiasi STT dalam mendukung managed service IT infrastructure kami. Responsivitas tim dan keterbukaan terhadap masukan menjadikan kolaborasi kami produktif dan positif.&rdquo;</p>
        <div class="person">
          <div class="tperson-av"><img src="https://images.pexels.com/photos/10376257/pexels-photo-10376257.jpeg?auto=compress&cs=tinysrgb&w=300" data-fallback="remove" alt=""><span class="play"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z" fill="#fff"/></svg></span></div>
          <div><div class="pname">Yonathan Moniaga</div><div class="prole">Chief Information Officer, Erha Clinic Indonesia</div></div>
        </div>
        <span class="watch">Watch story <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </a>
      <a class="tcard" href="#testimonial-detail">
        <span class="tbadge text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 8h10M7 12h10M7 16h6"/></svg>Text</span>
        <p class="quote">&ldquo;Migrasi sistem transaksi kami berjalan mulus dan aman. Tim STT memahami kebutuhan compliance industri finansial dengan baik.&rdquo;</p>
        <div class="person">
          <div class="tperson-av"><img src="https://images.pexels.com/photos/36714208/pexels-photo-36714208.jpeg?auto=compress&cs=tinysrgb&w=300" data-fallback="remove" alt=""></div>
          <div><div class="pname">IT Director</div><div class="prole">Financial Services</div></div>
        </div>
        <span class="watch">Read story <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </a>
      <a class="tcard" href="#testimonial-detail">
        <span class="tbadge video"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z" fill="currentColor"/></svg>Video</span>
        <p class="quote">&ldquo;Otomatisasi supply chain dari STT memangkas waktu proses secara signifikan. Partner yang benar-benar paham operasional pabrik.&rdquo;</p>
        <div class="person">
          <div class="tperson-av"><img src="https://images.pexels.com/photos/10376257/pexels-photo-10376257.jpeg?auto=compress&cs=tinysrgb&w=300" data-fallback="remove" alt=""><span class="play"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z" fill="#fff"/></svg></span></div>
          <div><div class="pname">Head of Operations</div><div class="prole">Manufacture &amp; FMCG</div></div>
        </div>
        <span class="watch">Watch story <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </a>
      <a class="tcard" href="#testimonial-detail">
        <span class="tbadge text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 8h10M7 12h10M7 16h6"/></svg>Text</span>
        <p class="quote">&ldquo;Platform kami kini scalable menghadapi lonjakan traffic. Arsitektur yang dirancang STT terbukti andal saat peak season.&rdquo;</p>
        <div class="person">
          <div class="tperson-av"><img src="https://images.pexels.com/photos/36714208/pexels-photo-36714208.jpeg?auto=compress&cs=tinysrgb&w=300" data-fallback="remove" alt=""></div>
          <div><div class="pname">Chief Technology Officer</div><div class="prole">E-Commerce Platform</div></div>
        </div>
        <span class="watch">Read story <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </a>
      <a class="tcard" href="#testimonial-detail">
        <span class="tbadge video"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z" fill="currentColor"/></svg>Video</span>
        <p class="quote">&ldquo;Implementasi cloud, data, dan AI berjalan sesuai roadmap. Eksekusi rapi dan komunikasi transparan sepanjang proyek.&rdquo;</p>
        <div class="person">
          <div class="tperson-av"><img src="https://images.pexels.com/photos/10376257/pexels-photo-10376257.jpeg?auto=compress&cs=tinysrgb&w=300" data-fallback="remove" alt=""><span class="play"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z" fill="#fff"/></svg></span></div>
          <div><div class="pname">VP Technology</div><div class="prole">Enterprise IT</div></div>
        </div>
        <span class="watch">Watch story <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </a>
      <a class="tcard" href="#testimonial-detail">
        <span class="tbadge text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 8h10M7 12h10M7 16h6"/></svg>Text</span>
        <p class="quote">&ldquo;Dukungan managed IT 24/7 membuat operasional rumah sakit kami jauh lebih tenang. Highly recommended.&rdquo;</p>
        <div class="person">
          <div class="tperson-av"><img src="https://images.pexels.com/photos/36714208/pexels-photo-36714208.jpeg?auto=compress&cs=tinysrgb&w=300" data-fallback="remove" alt=""></div>
          <div><div class="pname">IT Manager</div><div class="prole">Healthcare Group</div></div>
        </div>
        <span class="watch">Read story <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </a>
    </div>
  </div>
</section>

<section class="contact-sec" id="contactSection">
  <div class="wrap">
    <div class="form" id="contact">
      <div class="form-eyebrow"><?= hc('contact_eyebrow') ?></div>
      <h3><?= hc('contact_title') ?></h3>
      <p><?= hc('contact_intro') ?></p>
      <div class="form-grid">
        <div class="field"><label for="em">Email</label>
          <div class="field-wrap"><svg class="fic" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
          <input id="em" type="email" placeholder="you@company.com"></div></div>
        <div class="field"><label for="ph">No. Telp / WhatsApp</label>
          <div class="field-wrap"><svg class="fic" viewBox="0 0 24 24"><path d="M5 4h4l2 5-3 2a11 11 0 005 5l2-3 5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z"/></svg>
          <input id="ph" type="tel" placeholder="+62 8xx-xxxx-xxxx"></div></div>
      </div>
      <div class="field full"><label for="ms">Message</label>
        <textarea id="ms" placeholder="What can we help you with?"></textarea></div>
      <button class="form-submit" type="button" data-proposal-submit>Submit <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
    </div>
  </div>
</section>


