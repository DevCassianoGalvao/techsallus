/* ─────────────────────────────────────────────────────────────
   TechSallus — main.js
   Nav · Hamburger · Scroll-spy · Brazil Map · Form · GSAP
   ───────────────────────────────────────────────────────────── */
(function () {
  'use strict';

  /* ── Smooth scroll helper ─────────────────────────────────── */
  function scrollTo(id) {
    const el = document.getElementById(id);
    if (!el) return;
    window.scrollTo({ top: el.getBoundingClientRect().top + window.pageYOffset - 68, behavior: 'smooth' });
  }

  /* ═══════════════════════════════════════════════════════════
     HAMBURGER MENU
     ═══════════════════════════════════════════════════════════ */
  const hamburger = document.getElementById('nav-hamburger');
  const mobileMenu = document.getElementById('mobile-menu');
  const mobileClose = document.getElementById('mobile-menu-close');

  function openMenu() {
    mobileMenu.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeMenu() {
    mobileMenu.classList.remove('open');
    document.body.style.overflow = '';
  }

  if (hamburger) hamburger.addEventListener('click', openMenu);
  if (mobileClose) mobileClose.addEventListener('click', closeMenu);

  if (mobileMenu) {
    mobileMenu.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', function () {
        closeMenu();
        const href = this.getAttribute('href');
        if (href && href.startsWith('#')) {
          setTimeout(() => scrollTo(href.slice(1)), 10);
        }
      });
    });
  }

  /* ═══════════════════════════════════════════════════════════
     SCROLL-SPY (IntersectionObserver)
     ═══════════════════════════════════════════════════════════ */
  const sections = document.querySelectorAll('section[id], footer[id]');
  const navLinks = document.querySelectorAll('.nav-links a[data-section]');

  if (sections.length && navLinks.length) {
    const sectionMap = { 'sobre': 'sistema', 'onde': 'suporte', 'contato': 'suporte' };
    function setActive(id) {
      navLinks.forEach(a => a.classList.toggle('active', a.dataset.section === id));
    }
    const spy = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const raw = sectionMap[entry.target.id] || entry.target.id;
          /* "Início" activates only at very top; once #sistema scrolls in, switch to "sistema" */
          const active = (raw === 'sistema' && window.scrollY < 80) ? 'inicio' : raw;
          setActive(active);
        }
      });
    }, { rootMargin: '-50% 0px -45% 0px', threshold: 0 });
    sections.forEach(s => spy.observe(s));
    /* Activate "inicio" by default on page load */
    if (window.scrollY < 80) setActive('inicio');
  }

  /* ═══════════════════════════════════════════════════════════
     LANGUAGE SWITCHER
     ═══════════════════════════════════════════════════════════ */
  const langSwitcher = document.getElementById('lang-switcher');
  const langBtn      = document.getElementById('lang-btn');
  if (langSwitcher && langBtn) {
    langBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      const open = langSwitcher.classList.toggle('open');
      langBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    document.addEventListener('click', function () {
      langSwitcher.classList.remove('open');
      langBtn.setAttribute('aria-expanded', 'false');
    });
  }

  /* ═══════════════════════════════════════════════════════════
     NAV CTA + ANCHOR LINKS
     ═══════════════════════════════════════════════════════════ */
  document.querySelectorAll('[data-scroll]').forEach(el => {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      scrollTo(this.dataset.scroll);
    });
  });

  /* ═══════════════════════════════════════════════════════════
     UTM CAPTURE
     ═══════════════════════════════════════════════════════════ */
  (function () {
    const params = new URLSearchParams(window.location.search);
    ['utm_source', 'utm_medium', 'utm_campaign'].forEach(key => {
      const el = document.getElementById(key);
      if (el && params.get(key)) el.value = params.get(key);
    });
  })();

  /* ═══════════════════════════════════════════════════════════
     LEAD FORM — AJAX submit → /api/leads.php
     ═══════════════════════════════════════════════════════════ */
  const formDemo = document.getElementById('form-demo');
  if (formDemo) {
    formDemo.addEventListener('submit', async function (e) {
      e.preventDefault();

      const btn = this.querySelector('button[type="submit"]');
      const textoOriginal = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Enviando...';

      try {
        const res  = await fetch('/api/leads.php', {
          method: 'POST',
          body:   new FormData(this),
        });
        const data = await res.json();

        if (data.ok) {
          formDemo.innerHTML = `
            <div style="text-align:center;padding:48px 24px">
              <div style="font-size:48px;margin-bottom:16px">✅</div>
              <h3 style="font-family:'Bricolage Grotesque',sans-serif;font-size:22px;color:#0d1f35;margin-bottom:8px">
                Solicitação recebida!
              </h3>
              <p style="color:#4a6080;font-size:15px">
                Nossa equipe entrará em contato em breve para agendar a demonstração.
              </p>
            </div>
          `;
        } else {
          const erros = data.erros ? data.erros.join('<br>') : data.erro;
          alert('Erro: ' + erros);
          btn.disabled = false;
          btn.textContent = textoOriginal;
        }
      } catch (err) {
        alert('Erro de conexão. Tente novamente.');
        btn.disabled = false;
        btn.textContent = textoOriginal;
      }
    });
  }

  /* ═══════════════════════════════════════════════════════════
     GSAP ANIMATIONS
     ═══════════════════════════════════════════════════════════ */
  function initGSAP() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
    gsap.registerPlugin(ScrollTrigger);

    /* Hero — immediate entrance */
    gsap.timeline()
      .from('.hero-label',      { opacity: 0, y: 20, duration: 0.6, ease: 'power2.out' })
      .from('.hero-headline',   { opacity: 0, y: 24, duration: 0.6, ease: 'power2.out' }, '-=0.4')
      .from('.hero-sub',        { opacity: 0, y: 20, duration: 0.6, ease: 'power2.out' }, '-=0.4')
      .from('.hero-ctas',       { opacity: 0, y: 16, duration: 0.5, ease: 'power2.out' }, '-=0.3')
      .from('.hero-pills',      { opacity: 0, y: 12, duration: 0.5, ease: 'power2.out' }, '-=0.3')
      .from('.dashboard-frame', { opacity: 0, x: 40, duration: 0.8, ease: 'power2.out' }, '-=0.6');

    /* Sobre */
    gsap.from('.sobre-text', {
      scrollTrigger: { trigger: '.sobre', start: 'top 80%' },
      opacity: 0, y: 32, duration: 0.7, ease: 'power2.out'
    });
    gsap.from('.stat-card', {
      scrollTrigger: { trigger: '.stat-cards', start: 'top 85%' },
      opacity: 0, y: 24, duration: 0.6, stagger: 0.15, ease: 'power2.out'
    });

    /* Módulos */
    gsap.from('.modulo-card', {
      scrollTrigger: { trigger: '.modulos-grid', start: 'top 80%' },
      opacity: 0, y: 30, duration: 0.5, ease: 'power2.out',
      stagger: { amount: 0.8, grid: [3, 3], from: 'start' }
    });

    /* PorQuê */
    gsap.from('.diferencial-item', {
      scrollTrigger: { trigger: '.porque', start: 'top 75%' },
      opacity: 0, x: -24, duration: 0.5, stagger: 0.12, ease: 'power2.out'
    });

    /* Suporte */
    gsap.from('.suporte-text', {
      scrollTrigger: { trigger: '.suporte', start: 'top 80%' },
      opacity: 0, x: -32, duration: 0.7, ease: 'power2.out'
    });
    gsap.from('.suporte-ilustracao', {
      scrollTrigger: { trigger: '.suporte', start: 'top 80%' },
      opacity: 0, x: 32, duration: 0.7, ease: 'power2.out'
    });

    /* Mapa */
    gsap.from('#mapa-brasil, #map-loading', {
      scrollTrigger: { trigger: '.onde', start: 'top 75%' },
      opacity: 0, scale: 0.97, duration: 0.8, ease: 'power2.out'
    });

    /* Formulário */
    gsap.from('.form-container', {
      scrollTrigger: { trigger: '.form-section', start: 'top 80%' },
      opacity: 0, y: 32, duration: 0.7, ease: 'power2.out'
    });
    gsap.from('.trust-col', {
      scrollTrigger: { trigger: '.form-section', start: 'top 80%' },
      opacity: 0, x: 24, duration: 0.7, delay: 0.2, ease: 'power2.out'
    });

    /* Planos */
    gsap.from('.plano-card', {
      scrollTrigger: { trigger: '.planos', start: 'top 80%' },
      opacity: 0, y: 28, duration: 0.6, stagger: 0.15, ease: 'power2.out'
    });

    /* Animated counters */
    document.querySelectorAll('.stat-num[data-target]').forEach(el => {
      const target = parseInt(el.dataset.target, 10);
      const prefix = el.dataset.prefix || '';
      ScrollTrigger.create({
        trigger: el,
        start: 'top 85%',
        once: true,
        onEnter: () => {
          gsap.fromTo({ val: 0 }, {
            val: target,
            duration: 1.5,
            ease: 'power2.out',
            onUpdate: function () {
              el.textContent = prefix + Math.round(this.targets()[0].val);
            }
          });
        }
      });
    });

    window.addEventListener('load', () => ScrollTrigger.refresh());
  }

  /* Gate animations on prefers-reduced-motion */
  if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    if (document.readyState === 'complete') {
      initGSAP();
    } else {
      window.addEventListener('load', initGSAP);
    }
  }

})();
