/**
 * REKLAMEPEDIA CMS - Main JavaScript
 * Vanilla JS - No heavy frameworks
 */

'use strict';

// ============================================================
// NAVBAR SCROLL
// ============================================================
const header = document.getElementById('site-header');
if (header) {
  window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 50);
  }, { passive: true });
}

// ============================================================
// MOBILE MENU
// ============================================================
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobile-menu');
const mobileOverlay = document.getElementById('mobile-overlay');

function openMobileMenu() {
  mobileMenu?.classList.add('open');
  mobileOverlay?.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeMobileMenu() {
  mobileMenu?.classList.remove('open');
  mobileOverlay?.classList.remove('open');
  document.body.style.overflow = '';
}

hamburger?.addEventListener('click', () => {
  mobileMenu?.classList.contains('open') ? closeMobileMenu() : openMobileMenu();
});

mobileOverlay?.addEventListener('click', closeMobileMenu);

// ============================================================
// FAQ ACCORDION
// ============================================================
document.querySelectorAll('.faq-item').forEach(item => {
  const question = item.querySelector('.faq-question');
  const icon = item.querySelector('.faq-icon');
  
  question?.addEventListener('click', () => {
    const isOpen = item.classList.contains('open');
    
    // Close all
    document.querySelectorAll('.faq-item.open').forEach(openItem => {
      openItem.classList.remove('open');
      openItem.querySelector('.faq-icon').textContent = '+';
    });
    
    // Open clicked if it was closed
    if (!isOpen) {
      item.classList.add('open');
      if (icon) icon.textContent = '×';
    }
  });
});

// ============================================================
// SCROLL ANIMATIONS
// ============================================================
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      observer.unobserve(entry.target);
    }
  });
}, observerOptions);

document.querySelectorAll('.animate-on-scroll').forEach(el => {
  observer.observe(el);
});

// ============================================================
// LAZY LOAD IMAGES
// ============================================================
const imageObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const img = entry.target;
      if (img.dataset.src) {
        img.src = img.dataset.src;
        img.addEventListener('load', () => img.classList.add('loaded'));
        imageObserver.unobserve(img);
      }
    }
  });
}, { rootMargin: '200px' });

document.querySelectorAll('img[data-src]').forEach(img => imageObserver.observe(img));

// ============================================================
// GALLERY FILTER
// ============================================================
const filterBtns = document.querySelectorAll('.filter-btn');
const galleryItems = document.querySelectorAll('.gallery-item');

filterBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    const filter = btn.dataset.filter;
    
    filterBtns.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    
    galleryItems.forEach(item => {
      if (filter === 'all' || item.dataset.category === filter) {
        item.style.display = '';
        setTimeout(() => item.style.opacity = '1', 10);
      } else {
        item.style.opacity = '0';
        setTimeout(() => item.style.display = 'none', 300);
      }
    });
  });
});

// ============================================================
// LIGHTBOX
// ============================================================
function initLightbox() {
  const lightbox = document.getElementById('lightbox');
  if (!lightbox) return;
  
  const lightboxImg = lightbox.querySelector('img');
  const lightboxClose = lightbox.querySelector('.lightbox-close');
  
  document.querySelectorAll('[data-lightbox]').forEach(trigger => {
    trigger.addEventListener('click', (e) => {
      e.preventDefault();
      const src = trigger.dataset.lightbox || trigger.querySelector('img')?.src;
      if (src && lightboxImg) {
        lightboxImg.src = src;
        lightbox.classList.add('open');
        document.body.style.overflow = 'hidden';
      }
    });
  });
  
  function closeLightbox() {
    lightbox.classList.remove('open');
    document.body.style.overflow = '';
  }
  
  lightboxClose?.addEventListener('click', closeLightbox);
  lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) closeLightbox();
  });
  
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeLightbox();
  });
}

initLightbox();

// ============================================================
// WHATSAPP FLOATING
// ============================================================
function toggleWaPopup() {
  const popup = document.getElementById('wa-popup');
  popup?.classList.toggle('open');
}

function trackWaClick() {
  fetch('/track/wa', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' }
  }).catch(() => {});
}

// ============================================================
// FLASH MESSAGE AUTO HIDE
// ============================================================
const flash = document.querySelector('.flash-alert');
if (flash) {
  setTimeout(() => {
    flash.style.transition = 'opacity 0.4s ease';
    flash.style.opacity = '0';
    setTimeout(() => flash.remove(), 400);
  }, 4000);
}

// ============================================================
// SMOOTH SCROLL untuk anchor links
// ============================================================
document.querySelectorAll('a[href^="#"]').forEach(link => {
  link.addEventListener('click', (e) => {
    const target = document.querySelector(link.getAttribute('href'));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});

// ============================================================
// SERVICE HERO SLIDESHOW (jika ada)
// ============================================================
function initHeroSlider() {
  const slides = document.querySelectorAll('.hero-slide');
  if (slides.length <= 1) return;
  
  let current = 0;
  
  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === index);
    });
  }
  
  showSlide(0);
  
  setInterval(() => {
    current = (current + 1) % slides.length;
    showSlide(current);
  }, 5000);
}

initHeroSlider();

// ============================================================
// COUNTER ANIMATION untuk stats
// ============================================================
function animateCounter(el) {
  const target = el.textContent.replace(/[^0-9]/g, '');
  const suffix = el.textContent.replace(/[0-9]/g, '');
  if (!target) return;
  
  let start = 0;
  const end = parseInt(target);
  const duration = 2000;
  const step = (timestamp) => {
    if (!start) start = timestamp;
    const progress = Math.min((timestamp - start) / duration, 1);
    const current = Math.floor(progress * end);
    el.textContent = current.toLocaleString() + suffix;
    if (progress < 1) requestAnimationFrame(step);
  };
  requestAnimationFrame(step);
}

const counterObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      animateCounter(entry.target);
      counterObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.5 });

document.querySelectorAll('.stat-number').forEach(el => counterObserver.observe(el));

// ============================================================
// BLOG SEARCH (debounced)
// ============================================================
const searchInput = document.getElementById('blog-search');
if (searchInput) {
  let searchTimer;
  searchInput.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
      const q = searchInput.value.trim();
      const url = new URL(window.location.href);
      if (q) url.searchParams.set('q', q);
      else url.searchParams.delete('q');
      url.searchParams.delete('page');
      window.location.href = url.toString();
    }, 600);
  });
}

// ============================================================
// IMAGE PREVIEW (untuk form upload)
// ============================================================
document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
  const preview = document.getElementById(input.dataset.preview);
  if (!preview) return;
  
  input.addEventListener('change', () => {
    const file = input.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        if (preview.tagName === 'IMG') {
          preview.src = e.target.result;
          preview.style.display = 'block';
        }
      };
      reader.readAsDataURL(file);
    }
  });
});

// ============================================================
// TOOLTIP
// ============================================================
document.querySelectorAll('[data-tooltip]').forEach(el => {
  const tip = document.createElement('div');
  tip.className = 'tooltip';
  tip.textContent = el.dataset.tooltip;
  el.style.position = 'relative';
  
  el.addEventListener('mouseenter', () => {
    el.appendChild(tip);
  });
  
  el.addEventListener('mouseleave', () => {
    if (tip.parentNode === el) el.removeChild(tip);
  });
});
