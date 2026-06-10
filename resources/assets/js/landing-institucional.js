/* PULSO UGEL — Landing Premium v6 */
(function () {
  'use strict';

  /* ── Navbar: scroll + burger + overlay ── */
  const nav     = document.querySelector('.ugel-nav');
  const burger  = document.querySelector('.ugel-burger');
  const links   = document.querySelector('.ugel-nav__links');
  const overlay = document.querySelector('.ugel-overlay');

  if (nav) {
    const onScroll = () => nav.classList.toggle('scrolled', window.scrollY > 40);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  function closeMenu() {
    burger?.classList.remove('open');
    links?.classList.remove('open');
    overlay?.classList.remove('active');
    document.body.style.overflow = '';
  }
  function openMenu() {
    burger?.classList.add('open');
    links?.classList.add('open');
    overlay?.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  burger?.addEventListener('click', () =>
    links?.classList.contains('open') ? closeMenu() : openMenu()
  );
  overlay?.addEventListener('click', closeMenu);

  /* ── Smooth scroll (offset topbar + nav) ── */
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (!target) return;
      e.preventDefault();
      closeMenu();
      const offset = (document.querySelector('.ugel-topbar')?.offsetHeight || 0)
                   + (nav?.offsetHeight || 0) + 8;
      window.scrollTo({ top: target.getBoundingClientRect().top + scrollY - offset, behavior: 'smooth' });
    });
  });

  /* ── Active link via IntersectionObserver ── */
  const navLinks = document.querySelectorAll('.ugel-nav__link[href^="#"]');
  const sections = Array.from(navLinks)
    .map(a => document.querySelector(a.getAttribute('href')))
    .filter(Boolean);

  const sectionIo = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      navLinks.forEach(a => {
        a.classList.toggle('active', a.getAttribute('href') === '#' + e.target.id);
      });
    });
  }, { rootMargin: '-40% 0px -55% 0px' });
  sections.forEach(s => sectionIo.observe(s));

  /* ── Entry animation: stagger nav links ── */
  navLinks.forEach((a, i) => {
    a.style.cssText += `opacity:0;transform:translateY(-10px);
      transition:opacity .4s ease ${i * 60 + 100}ms,transform .4s ease ${i * 60 + 100}ms`;
    requestAnimationFrame(() => {
      a.style.opacity = '1'; a.style.transform = 'translateY(0)';
    });
  });

  /* ── Hero entry (slide from sides) ── */
  const heroL = document.querySelector('.ugel-hero__left');
  const heroR = document.querySelector('.ugel-hero__right');
  if (heroL) {
    heroL.style.cssText += 'opacity:0;transform:translateX(-28px);transition:opacity .8s ease .1s,transform .8s ease .1s';
    setTimeout(() => { heroL.style.opacity = '1'; heroL.style.transform = 'none'; }, 80);
  }
  if (heroR) {
    heroR.style.cssText += 'opacity:0;transform:translateX(28px);transition:opacity .8s ease .25s,transform .8s ease .25s';
    setTimeout(() => { heroR.style.opacity = '1'; heroR.style.transform = 'none'; }, 80);
  }

  /* ── Hero Swiper ── */
  if (window.Swiper && document.querySelector('#heroSwiper')) {
    const sw = new Swiper('#heroSwiper', {
      loop: true,
      autoplay: { delay: 5000, disableOnInteraction: false },
      pagination: { el: '.ugel-carousel-dots', clickable: true },
      speed: 600,
    });
    document.getElementById('heroPrev')?.addEventListener('click', () => sw.slidePrev());
    document.getElementById('heroNext')?.addEventListener('click', () => sw.slideNext());

    const wrapper = document.querySelector('.ugel-carousel');
    wrapper?.addEventListener('mouseenter', () => sw.autoplay.stop());
    wrapper?.addEventListener('mouseleave', () => sw.autoplay.start());
  }

  /* ── Stats counter ── */
  const statsBar = document.querySelector('.ugel-hero__stats');
  if (statsBar) {
    const counter = (el) => {
      const target = +el.dataset.target;
      const isFloat = target % 1 !== 0;
      let start = 0;
      const step = target / 55;
      const tick = () => {
        start = Math.min(start + step, target);
        el.textContent = isFloat
          ? start.toFixed(1)
          : Math.floor(start).toLocaleString('es-PE') + (el.dataset.suffix || '');
        if (start < target) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    };
    const statsIo = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (!e.isIntersecting) return;
        e.target.querySelectorAll('[data-target]').forEach(counter);
        statsIo.disconnect();
      });
    }, { threshold: 0, rootMargin: '0px 0px -10px 0px' });
    statsIo.observe(statsBar);
  }

  /* ── Scroll reveal cards ── */
  const cardIo = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      e.target.classList.add('ugel-anim-in');
      cardIo.unobserve(e.target);
    });
  }, { threshold: 0, rootMargin: '0px 0px -24px 0px' });

  const animGroups = [
    '.ugel-module', '.ugel-norm', '.ugel-inst',
    '.ugel-contact-card', '.ugel-check', '.ugel-mock-kpi'
  ];
  animGroups.forEach(sel => {
    document.querySelectorAll(sel).forEach((el, i) => {
      el.classList.add('ugel-anim-pending');
      el.style.setProperty('--delay', `${i * 55}ms`);
      cardIo.observe(el);
    });
  });

  /* ── Generic reveal (.ugel-reveal) ── */
  const revealIo = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) { e.target.classList.add('visible'); revealIo.unobserve(e.target); }
    });
  }, { threshold: 0, rootMargin: '0px 0px -20px 0px' });
  document.querySelectorAll('.ugel-reveal').forEach(el => revealIo.observe(el));

  navLinks.forEach(a => a.addEventListener('click', closeMenu));

})();
