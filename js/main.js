/* ============================================================
   MAYA FARM — Global JavaScript
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

  /* ── Navigation Scroll Behaviour ── */
  const nav = document.querySelector('.nav');
  window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 60);
  });

  /* ── Hamburger Menu ── */
  const hamburger = document.querySelector('.hamburger');
  const navLinks  = document.querySelector('.nav-links');
  if (hamburger) {
    hamburger.addEventListener('click', () => {
      hamburger.classList.toggle('open');
      navLinks.classList.toggle('open');
    });
  }

  /* ── Intersection Observer — Fade Animations ── */
  const fadeTargets = document.querySelectorAll(
    '.fade-in, .fade-in-left, .fade-in-right'
  );

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12 }
  );

  fadeTargets.forEach((el) => observer.observe(el));

  /* ── Animated Counters ── */
  const counters = document.querySelectorAll('[data-count]');

  const countObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          countObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.5 }
  );

  counters.forEach((c) => countObserver.observe(c));

  function animateCounter(el) {
    const target = parseInt(el.getAttribute('data-count'), 10);
    const suffix = el.getAttribute('data-suffix') || '';
    const duration = 1800;
    const start = performance.now();

    function step(now) {
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(eased * target) + suffix;
      if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  /* ── Active Nav Link ── */
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.nav-links a').forEach((link) => {
    const href = link.getAttribute('href');
    if (href === currentPage || (currentPage === '' && href === 'index.html')) {
      link.classList.add('active');
    }
  });

  /* ── Gallery Lightbox ── */
  const lightbox   = document.getElementById('lightbox');
  const lbImg      = document.getElementById('lightbox-img');
  const lbClose    = document.getElementById('lightbox-close');

  if (lightbox) {
    document.querySelectorAll('.gallery-item[data-src]').forEach((item) => {
      item.addEventListener('click', () => {
        lbImg.src = item.getAttribute('data-src');
        lightbox.classList.add('open');
        document.body.style.overflow = 'hidden';
      });
    });

    lbClose.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', (e) => {
      if (e.target === lightbox) closeLightbox();
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeLightbox();
    });

    function closeLightbox() {
      lightbox.classList.remove('open');
      document.body.style.overflow = '';
    }
  }

  /* ── Gallery Filter Buttons ── */
  const filterBtns = document.querySelectorAll('.filter-btn');
  const galleryItems = document.querySelectorAll('.gallery-item[data-category]');

  filterBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      filterBtns.forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');
      const cat = btn.getAttribute('data-filter');
      galleryItems.forEach((item) => {
        const match = cat === 'all' || item.getAttribute('data-category') === cat;
        item.style.opacity = match ? '1' : '0.2';
        item.style.pointerEvents = match ? 'auto' : 'none';
        item.style.transition = 'opacity 0.4s ease';
      });
    });
  });

  /* ── Contact Form Submission ── */
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const success = document.getElementById('formSuccess');
      const btn = contactForm.querySelector('button[type="submit"]');
      btn.textContent = 'Sending...';
      btn.disabled = true;

      setTimeout(() => {
        success.classList.add('show');
        contactForm.reset();
        btn.textContent = 'Send Message';
        btn.disabled = false;

        setTimeout(() => success.classList.remove('show'), 5000);
      }, 1200);
    });
  }

  /* ── Smooth parallax for hero bg ── */
  const heroBg = document.querySelector('.hero-bg');
  if (heroBg) {
    window.addEventListener('scroll', () => {
      const y = window.scrollY;
      heroBg.style.transform = `translateY(${y * 0.3}px)`;
    }, { passive: true });
  }
});
