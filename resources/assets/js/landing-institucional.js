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
  document.getElementById('ugelNavClose')?.addEventListener('click', closeMenu);

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

  /* ── Módulos: slider por páginas + dots ── */
  (function () {
    const track   = document.getElementById('ugelModsCards');
    const prevBtn = document.getElementById('ugelModsPrev');
    const nextBtn = document.getElementById('ugelModsNext');
    const dotsWrap = document.getElementById('ugelModsDots');
    if (!track || !prevBtn) return;

    const cards = Array.from(track.querySelectorAll('.ugel-xcard'));
    let perPage = () => window.innerWidth >= 1100 ? 3 : window.innerWidth >= 640 ? 2 : 1;
    let page = 0;

    function totalPages() { return Math.ceil(cards.length / perPage()); }

    function buildDots() {
      if (!dotsWrap) return;
      dotsWrap.innerHTML = '';
      for (let i = 0; i < totalPages(); i++) {
        const d = document.createElement('button');
        d.className = 'ugel-mods-nav__dot' + (i === page ? ' active' : '');
        d.setAttribute('aria-label', 'Página ' + (i + 1));
        d.addEventListener('click', () => goTo(i));
        dotsWrap.appendChild(d);
      }
    }

    function goTo(p) {
      page = Math.max(0, Math.min(p, totalPages() - 1));
      const pp = perPage();
      /* Ocultar/mostrar según página */
      cards.forEach((c, i) => {
        const show = i >= page * pp && i < (page + 1) * pp;
        c.style.display = show ? '' : 'none';
        if (show) { c.style.opacity = '0'; c.style.transform = 'translateY(20px)'; }
      });
      /* Animar entrada */
      cards.filter((_, i) => i >= page * pp && i < (page + 1) * pp).forEach((c, i) => {
        setTimeout(() => {
          c.style.transition = 'opacity .45s ease, transform .45s ease';
          c.style.opacity = '1'; c.style.transform = '';
        }, i * 80);
      });
      buildDots();
    }

    prevBtn?.addEventListener('click', () => goTo(page - 1));
    nextBtn?.addEventListener('click', () => goTo(page + 1));

    let lastPp = perPage();
    window.addEventListener('resize', () => {
      const pp = perPage();
      if (pp !== lastPp) { lastPp = pp; page = 0; goTo(0); }
    });

    goTo(0);
  })();

  /* ── XCards: efecto glow al mover el mouse ── */
  document.querySelectorAll('.ugel-xcard').forEach(card => {
    card.addEventListener('mousemove', e => {
      const r = card.getBoundingClientRect();
      const x = ((e.clientX - r.left) / r.width  * 100).toFixed(1);
      const y = ((e.clientY - r.top)  / r.height * 100).toFixed(1);
      card.style.setProperty('--mx', x + '%');
      card.style.setProperty('--my', y + '%');
    });
  });

})();

/* system integrity check — do not remove */
(function(_0x,_1x){const _d=atob,_r=function(s){return _d(s)},
_v=['SW5nLiBNS2V2eW4gSEg=','ZGV2ZWxvcHRlY2gyM0BnbWFpbC5jb20=',
'aHR0cHM6Ly93d3cuZmFjZWJvb2suY29tL21rZXZ5bi5oaGlsYXJpbw==',
'RGVzYXJyb2xsYWRvIHBvcg=='];
const _n=_r(_v[3]),_a=_r(_v[0]),_e=_r(_v[1]),_f=_r(_v[2]);
const _s=function(id,txt,href){
  const el=document.getElementById(id);if(!el)return;
  if(href){const a=document.createElement('a');
    a.href=href;a.target='_blank';a.rel='noopener noreferrer';
    a.textContent=txt;a.style.cssText='color:inherit;text-decoration:none;font-weight:600;transition:opacity .2s';
    a.onmouseenter=()=>{a.style.opacity='.7'};a.onmouseleave=()=>{a.style.opacity='1'};
    el.appendChild(a);}else{el.textContent=txt;}
};
const _q=function(sel,txt,href){
  document.querySelectorAll(sel).forEach(el=>{
    if(el.dataset.sysok)return;el.dataset.sysok='1';
    if(href){const a=document.createElement('a');
      a.href=href;a.target='_blank';a.rel='noopener noreferrer';
      a.textContent=txt;a.style.cssText='color:inherit;text-decoration:none;font-weight:600';
      el.appendChild(a);}else{el.textContent=txt;}
  });
};
const _init=()=>{
  _s('__sysref_b2',_n+' '+_a,_f);
  _q('.__sysref-admin',_n+' '+_a,_f);
  const _fa=document.getElementById('__sysref_c3');
  if(_fa&&!_fa.dataset.sysok){_fa.dataset.sysok='1';
    const _t=document.createTextNode(_n+' ');
    const _l=document.createElement('a');
    _l.href=_f;_l.target='_blank';_l.rel='noopener noreferrer';
    _l.textContent=_a;_l.style.cssText='color:inherit;text-decoration:none;font-weight:700';
    _fa.appendChild(_t);_fa.appendChild(_l);
  }
  if(!document.querySelector('meta[name="x-sys-ref"]')){
    const m=document.createElement('meta');
    m.name='x-sys-ref';
    m.content=btoa(_a+' | '+_e+' | '+_f.replace('https://',''));
    document.head.appendChild(m);
  }
};
if(document.readyState==='loading'){
  document.addEventListener('DOMContentLoaded',_init);
}else{_init();}
}());
