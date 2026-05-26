/* ═══════════════════════════════════════════════
   MAIN.JS — Techsallus
   Navbar, menu mobile, scroll-spy, GSAP,
   UTM capture, form submit
   ═══════════════════════════════════════════════ */

// ── NAVBAR SCROLL ─────────────────────────────
const navbar = document.getElementById('navbar');
if (navbar) {
  window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 20);
  }, { passive: true });
}

// ── MENU HAMBURGER ────────────────────────────
const hamburger  = document.getElementById('nav-hamburger');
const mobileMenu = document.getElementById('mobile-menu');
const mobileClose = document.getElementById('mobile-menu-close');

function abrirMenu() {
  mobileMenu.classList.add('open');
  hamburger.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function fecharMenu() {
  mobileMenu.classList.remove('open');
  hamburger.classList.remove('open');
  document.body.style.overflow = '';
}

if (hamburger)   hamburger.addEventListener('click', abrirMenu);
if (mobileClose) mobileClose.addEventListener('click', fecharMenu);

// Fechar ao clicar fora
if (mobileMenu) {
  mobileMenu.addEventListener('click', function (e) {
    if (e.target === this) fecharMenu();
  });
}

// Fechar links internos do menu mobile
if (mobileMenu) {
  mobileMenu.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', fecharMenu);
  });
}

// Fechar com ESC
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') fecharMenu();
});

// ── SCROLL-SPY ────────────────────────────────
const sections = document.querySelectorAll('section[id], div[id]');
const navLinks  = document.querySelectorAll('.nav-links a');

if (sections.length && navLinks.length) {
  const spyObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const id = entry.target.id;
        navLinks.forEach(link => {
          link.classList.remove('active');
          const href = link.getAttribute('href');
          if (href === `#${id}` || href === `/#${id}`) {
            link.classList.add('active');
          }
        });
      }
    });
  }, { threshold: 0.3, rootMargin: '-10% 0px -60% 0px' });

  sections.forEach(s => spyObserver.observe(s));
}

// ── CAPTURA DE UTM ────────────────────────────
(function captureUTM() {
  const params = new URLSearchParams(window.location.search);
  const keys   = ['utm_source', 'utm_medium', 'utm_campaign'];

  keys.forEach(key => {
    const val = params.get(key);
    if (val) {
      try { sessionStorage.setItem(key, val); } catch (e) {}
    }

    const el = document.getElementById(key);
    if (el) {
      const stored = (() => {
        try { return sessionStorage.getItem(key); } catch (e) { return null; }
      })();
      el.value = val || stored || '';
    }
  });
})();

// ── SUBMIT DO FORMULÁRIO ──────────────────────
const formDemo = document.getElementById('form-demo');
if (formDemo) {
  formDemo.addEventListener('submit', async function (e) {
    e.preventDefault();

    const btn  = this.querySelector('button[type="submit"]');
    const orig = btn.textContent;
    btn.disabled    = true;
    btn.textContent = 'Enviando…';

    try {
      const res  = await fetch('/api/leads.php', {
        method: 'POST',
        body:   new FormData(this),
      });
      const data = await res.json();

      if (data.ok) {
        formDemo.innerHTML = `
          <div style="text-align:center;padding:48px 24px">
            <div style="font-size:52px;margin-bottom:16px">✅</div>
            <h3 style="font-family:'Bricolage Grotesque',sans-serif;font-size:24px;color:#0d1f35;margin-bottom:10px;font-weight:800">
              Solicitação recebida!
            </h3>
            <p style="color:#4a6080;font-size:15px;line-height:1.6;max-width:360px;margin:0 auto">
              Nossa equipe entrará em contato em breve para agendar a demonstração gratuita.
            </p>
          </div>
        `;
      } else {
        const msg = data.erros ? data.erros.join('\n') : (data.erro || 'Erro desconhecido.');
        alert('Por favor, corrija os seguintes campos:\n\n' + msg);
        btn.disabled    = false;
        btn.textContent = orig;
      }
    } catch (err) {
      alert('Erro de conexão. Verifique sua internet e tente novamente.');
      btn.disabled    = false;
      btn.textContent = orig;
    }
  });
}

// ── GSAP ANIMAÇÕES ────────────────────────────
(function initGSAP() {
  if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  gsap.registerPlugin(ScrollTrigger);

  // Hero — entrada imediata
  const heroTl = gsap.timeline({ delay: 0.1 });
  heroTl
    .from('.hero-label',      { opacity: 0, y: 20, duration: 0.6, ease: 'power2.out' })
    .from('.hero-headline',   { opacity: 0, y: 24, duration: 0.6, ease: 'power2.out' }, '-=0.4')
    .from('.hero-sub',        { opacity: 0, y: 18, duration: 0.5, ease: 'power2.out' }, '-=0.35')
    .from('.hero-ctas',       { opacity: 0, y: 14, duration: 0.5, ease: 'power2.out' }, '-=0.3')
    .from('.hero-pills .pill',{ opacity: 0, y: 10, duration: 0.4, stagger: 0.08 },     '-=0.25')
    .from('.dashboard-frame', { opacity: 0, x: 40, duration: 0.8, ease: 'power2.out' },'-=0.5');

  // Sobre
  gsap.from('.sobre h2, .sobre-text', {
    scrollTrigger: { trigger: '.sobre', start: 'top 80%' },
    opacity: 0, y: 28, duration: 0.7, stagger: 0.15
  });

  gsap.from('.stat-card', {
    scrollTrigger: { trigger: '.stat-cards', start: 'top 85%' },
    opacity: 0, y: 20, duration: 0.5, stagger: 0.12
  });

  // Contadores animados
  document.querySelectorAll('.stat-card .stat-num').forEach(el => {
    const raw    = el.textContent.trim();
    const prefix = raw.startsWith('+') ? '+' : '';
    const target = parseInt(raw.replace(/\D/g, ''), 10);
    if (!target) return;

    ScrollTrigger.create({
      trigger: el,
      start:   'top 85%',
      once:    true,
      onEnter: () => {
        gsap.fromTo(
          { val: 0 },
          {
            val:      target,
            duration: 1.6,
            ease:     'power2.out',
            onUpdate: function () {
              el.textContent = prefix + Math.round(this.targets()[0].val);
            },
          }
        );
      },
    });
  });

  // Módulos
  gsap.from('.modulo-card', {
    scrollTrigger: { trigger: '.modulos-grid', start: 'top 80%' },
    opacity: 0, y: 28, duration: 0.5,
    stagger: { amount: 0.7, grid: [3, 3], from: 'start' }
  });

  // Por que Techsallus
  gsap.from('.diferencial-item', {
    scrollTrigger: { trigger: '.por-que', start: 'top 75%' },
    opacity: 0, x: -24, duration: 0.5, stagger: 0.1
  });

  // Suporte
  gsap.from('.suporte-text', {
    scrollTrigger: { trigger: '.suporte', start: 'top 80%' },
    opacity: 0, x: -28, duration: 0.7
  });

  gsap.from('.suporte-ilustracao', {
    scrollTrigger: { trigger: '.suporte', start: 'top 80%' },
    opacity: 0, x: 28, duration: 0.7
  });

  // Mapa
  if (document.getElementById('mapa-brasil')) {
    gsap.from('#mapa-brasil', {
      scrollTrigger: { trigger: '.onde-estamos', start: 'top 75%' },
      opacity: 0, scale: 0.96, duration: 0.8, ease: 'power2.out'
    });
    gsap.from('.legend-item', {
      scrollTrigger: { trigger: '.map-legend', start: 'top 90%' },
      opacity: 0, y: 10, duration: 0.4, stagger: 0.07
    });
  }

  // Formulário
  gsap.from('.form-container', {
    scrollTrigger: { trigger: '#demo', start: 'top 80%' },
    opacity: 0, y: 28, duration: 0.7
  });

  gsap.from('.trust-col', {
    scrollTrigger: { trigger: '#demo', start: 'top 80%' },
    opacity: 0, x: 24, duration: 0.7, delay: 0.2
  });

  // Planos
  gsap.from('.plano-card', {
    scrollTrigger: { trigger: '.planos-grid', start: 'top 80%' },
    opacity: 0, y: 28, duration: 0.6, stagger: 0.15
  });

  // Refresh após fontes carregarem
  window.addEventListener('load', () => ScrollTrigger.refresh());
})();
