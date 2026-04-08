/* ============================================================
   MAYA FARM — main.js v2
   Swiper, Nav, Animations, Lightbox, Forms
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

  /* ── Nav scroll ── */
  const nav = document.getElementById('mainNav');
  if (nav) {
    const checkScroll = () => nav.classList.toggle('scrolled', window.scrollY > 50);
    checkScroll();
    window.addEventListener('scroll', checkScroll, { passive: true });
  }

  /* ── Hamburger ── */
  const hamburger = document.getElementById('hamburger');
  const mobileNav = document.getElementById('mobileNav');
  if (hamburger && mobileNav) {
    hamburger.addEventListener('click', () => {
      hamburger.classList.toggle('open');
      mobileNav.classList.toggle('open');
    });
    // close on link click
    mobileNav.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => {
        hamburger.classList.remove('open');
        mobileNav.classList.remove('open');
      });
    });
  }

  /* ── Active nav link ── */
  const current = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.nav-links a, .mobile-nav a').forEach(link => {
    const href = link.getAttribute('href');
    if (href === current || (current === '' && href === 'index.html')) {
      link.classList.add('active');
    }
  });

  /* ── Intersection Observer — Fade ── */
  const fadeEls = document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right');
  const fadeObserver = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        fadeObserver.unobserve(e.target);
      }
    });
  }, { threshold: 0.1 });
  fadeEls.forEach(el => fadeObserver.observe(el));

  /* ── Animated counters ── */
  const counters = document.querySelectorAll('[data-count]');
  const countObserver = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        animateCounter(e.target);
        countObserver.unobserve(e.target);
      }
    });
  }, { threshold: 0.5 });
  counters.forEach(c => countObserver.observe(c));

  function animateCounter(el) {
    const target = parseInt(el.getAttribute('data-count'), 10);
    const suffix = el.getAttribute('data-suffix') || '';
    const duration = 1800;
    const start = performance.now();
    function step(now) {
      const p = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.floor(eased * target) + suffix;
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  /* ── Hero parallax ── */
  const heroBg = document.querySelector('.hero-bg');
  if (heroBg) {
    window.addEventListener('scroll', () => {
      heroBg.style.transform = `translateY(${window.scrollY * 0.28}px)`;
    }, { passive: true });
  }

  /* ══════════════════════════════════
     UNIVERSAL SWIPER FACTORY
     Works for both gallery-preview and gallery-full
  ══════════════════════════════════ */
  function initSwiper(options) {
    const { trackEl, dotsEl, prevBtn, nextBtn, slidesPerView, gap } = options;
    if (!trackEl) return;

    const slides = Array.from(trackEl.children);
    let current = 0;
    let startX = 0, isDragging = false;

    const totalSlides = slides.length;
    const maxIndex = Math.max(0, totalSlides - slidesPerView);

    function goTo(index) {
      current = Math.max(0, Math.min(index, maxIndex));
      const slideWidth = trackEl.parentElement.offsetWidth;
      const singleW = (slideWidth - gap * (slidesPerView - 1)) / slidesPerView;
      const offset = current * (singleW + gap);
      trackEl.style.transform = `translateX(-${offset}px)`;

      // update dots
      if (dotsEl) {
        dotsEl.querySelectorAll('.swiper-dot').forEach((d, i) => {
          d.classList.toggle('active', i === current);
        });
      }
    }

    // build dots
    if (dotsEl) {
      const dotCount = maxIndex + 1;
      dotsEl.innerHTML = '';
      for (let i = 0; i <= maxIndex; i++) {
        const dot = document.createElement('button');
        dot.className = 'swiper-dot' + (i === 0 ? ' active' : '');
        dot.setAttribute('aria-label', `Slide ${i + 1}`);
        dot.addEventListener('click', () => goTo(i));
        dotsEl.appendChild(dot);
      }
    }

    if (prevBtn) prevBtn.addEventListener('click', () => goTo(current - 1));
    if (nextBtn) nextBtn.addEventListener('click', () => goTo(current + 1));

    // Touch / swipe support
    const wrap = trackEl.parentElement;
    wrap.addEventListener('touchstart', e => { startX = e.touches[0].clientX; isDragging = true; }, { passive: true });
    wrap.addEventListener('touchmove', e => {
      if (!isDragging) return;
      const diff = startX - e.touches[0].clientX;
      if (Math.abs(diff) > 40) {
        goTo(diff > 0 ? current + 1 : current - 1);
        isDragging = false;
      }
    }, { passive: true });
    wrap.addEventListener('touchend', () => { isDragging = false; });

    // Mouse drag
    wrap.addEventListener('mousedown', e => { startX = e.clientX; isDragging = true; });
    window.addEventListener('mousemove', e => {
      if (!isDragging) return;
      const diff = startX - e.clientX;
      if (Math.abs(diff) > 60) {
        goTo(diff > 0 ? current + 1 : current - 1);
        isDragging = false;
      }
    });
    window.addEventListener('mouseup', () => { isDragging = false; });

    // Recalculate on resize
    window.addEventListener('resize', () => goTo(current), { passive: true });

    goTo(0);
    return { goTo };
  }

  /* ── Home page gallery swiper ── */
  const homeTrack = document.getElementById('homeSwiperTrack');
  if (homeTrack) {
    const isMobile = () => window.innerWidth <= 768;
    const isTablet = () => window.innerWidth <= 1024 && window.innerWidth > 768;
    const getSPV = () => isMobile() ? 1 : isTablet() ? 2 : 3;

    initSwiper({
      trackEl: homeTrack,
      dotsEl: document.getElementById('homeSwiperDots'),
      prevBtn: document.getElementById('homePrev'),
      nextBtn: document.getElementById('homeNext'),
      slidesPerView: getSPV(),
      gap: 8
    });
  }

  /* ── Gallery page swiper ── */
  const galleryTrack = document.getElementById('gallerySwiperTrack');
  if (galleryTrack) {
    const isMobile = () => window.innerWidth <= 768;
    const isTablet = () => window.innerWidth <= 1024 && window.innerWidth > 768;
    const getSPV = () => isMobile() ? 1 : isTablet() ? 2 : 3;

    initSwiper({
      trackEl: galleryTrack,
      dotsEl: document.getElementById('gallerySwiperDots'),
      prevBtn: document.getElementById('galleryPrev'),
      nextBtn: document.getElementById('galleryNext'),
      slidesPerView: getSPV(),
      gap: 8
    });
  }

  /* ── Gallery filter (show/hide slides) ── */
  const filterBtns = document.querySelectorAll('.filter-btn');
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const cat = btn.getAttribute('data-filter');
      const allSlides = document.querySelectorAll('.gallery-full-slide');
      allSlides.forEach(slide => {
        const match = cat === 'all' || slide.getAttribute('data-category') === cat;
        slide.style.display = match ? 'block' : 'none';
      });
    });
  });

  /* ── Lightbox ── */
  const lightbox = document.getElementById('lightbox');
  const lbImg    = document.getElementById('lightbox-img');
  const lbClose  = document.getElementById('lightbox-close');

  if (lightbox) {
    document.querySelectorAll('[data-src]').forEach(item => {
      item.addEventListener('click', () => {
        lbImg.src = item.getAttribute('data-src');
        lightbox.classList.add('open');
        document.body.style.overflow = 'hidden';
      });
    });
    if (lbClose) lbClose.addEventListener('click', closeLB);
    lightbox.addEventListener('click', e => { if (e.target === lightbox) closeLB(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLB(); });
    function closeLB() {
      lightbox.classList.remove('open');
      document.body.style.overflow = '';
    }
  }

  /* ── Contact form ── */
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', e => {
      e.preventDefault();
      const btn = contactForm.querySelector('button[type="submit"]');
      const success = document.getElementById('formSuccess');
      btn.textContent = 'Sending...';
      btn.disabled = true;
      setTimeout(() => {
        if (success) success.classList.add('show');
        contactForm.reset();
        btn.textContent = 'Send Message';
        btn.disabled = false;
        setTimeout(() => { if (success) success.classList.remove('show'); }, 6000);
      }, 1200);
    });
  }

});
