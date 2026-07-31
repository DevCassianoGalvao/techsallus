
(function () {
  'use strict';

  function appUrl(path) {
    if (/^https?:\/\//.test(path)) return path;
    var script = document.querySelector('script[src*="/assets/js/main.js"]');
    var src = script ? script.getAttribute('src') : '';
    var base = src ? src.replace(/\/assets\/js\/main\.js.*$/, '') : '';
    return base + '/' + String(path).replace(/^\/+/, '');
  }

  var utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
  var urlParams = new URLSearchParams(window.location.search);
  utmKeys.forEach(function (key) {
    var value = urlParams.get(key);
    if (value) sessionStorage.setItem('techsallus_' + key, value);
  });

  /* ── Nav: burger / mobile panel ─────────────────────────────── */
  function initNav() {
    var burgerBtn = document.getElementById('burgerBtn');
    var mobilePanel = document.getElementById('mobilePanel');
    if (burgerBtn && mobilePanel) {
      burgerBtn.addEventListener('click', function () { mobilePanel.classList.toggle('open'); });
      mobilePanel.querySelectorAll('a').forEach(function (a) {
        a.addEventListener('click', function () { mobilePanel.classList.remove('open'); });
      });
    }
  }

  /* ── Lang switcher ───────────────────────────────────────────── */
  var langSwitcher = document.getElementById('lang-switcher');
  var langBtn = document.getElementById('lang-btn');
  if (langSwitcher && langBtn) {
    langBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      var open = langSwitcher.classList.toggle('open');
      langBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    document.addEventListener('click', function () {
      langSwitcher.classList.remove('open');
      langBtn.setAttribute('aria-expanded', 'false');
    });
  }

  /* ── GSAP scroll reveals ─────────────────────────────────────── */
  function initReveals() {
    if (typeof gsap === 'undefined') {
      document.querySelectorAll('.reveal').forEach(function (el) { el.style.opacity = 1; el.style.transform = 'none'; });
      return;
    }
    gsap.registerPlugin(ScrollTrigger);
    var hero = document.querySelector('#hero, .subhero');
    if (hero) {
      var items = hero.querySelectorAll('.reveal');
      gsap.timeline({ defaults: { duration: .9, ease: 'power3.out' } }).to(items, { opacity: 1, y: 0, stagger: .12 });
    }
    document.querySelectorAll('.section, footer, #cta, .band-dark').forEach(function (sec) {
      var secItems = Array.prototype.slice.call(sec.querySelectorAll('.reveal')).filter(function (el) { return !hero || !hero.contains(el); });
      if (!secItems.length) return;
      ScrollTrigger.batch(secItems, {
        start: 'top 88%',
        onEnter: function (batch) { gsap.to(batch, { opacity: 1, y: 0, duration: .8, ease: 'power3.out', stagger: .08 }); },
        once: true
      });
    });
    var dashBar = document.getElementById('dashBar');
    if (dashBar) {
      ScrollTrigger.create({ trigger: dashBar, start: 'top 90%', once: true, onEnter: function () { gsap.to(dashBar, { width: '92%', duration: 1.2, ease: 'power2.out' }); } });
    }
  }

  /* ── FAQ accordion ───────────────────────────────────────────── */
  function initFAQ() {
    document.querySelectorAll('.faq-item').forEach(function (item) {
      var q = item.querySelector('.faq-q');
      var a = item.querySelector('.faq-a');
      if (!q || !a) return;
      q.addEventListener('click', function () {
        var open = item.classList.contains('open');
        document.querySelectorAll('.faq-item.open').forEach(function (o) {
          o.classList.remove('open');
          o.querySelector('.faq-a').style.maxHeight = null;
        });
        if (!open) { item.classList.add('open'); a.style.maxHeight = a.scrollHeight + 'px'; }
      });
    });
  }

  /* ── Contato: priority pills (multi-select) ──────────────────── */
  function syncPriorityInput() {
    var input = document.getElementById('priorityValue');
    if (!input) return;
    var selected = Array.prototype.slice.call(document.querySelectorAll('.priority-pill.active'))
      .map(function (p) { return p.dataset.value || p.textContent; });
    input.value = selected.join(', ');
  }
  function initPriorityPills() {
    var pills = document.querySelectorAll('.priority-pill');
    pills.forEach(function (p) {
      p.addEventListener('click', function () {
        p.classList.toggle('active');
        syncPriorityInput();
        p.closest('.field').classList.remove('invalid');
      });
    });
  }
  window.selectPriority = function (value) {
    document.querySelectorAll('.priority-pill').forEach(function (p) {
      p.classList.toggle('active', (p.dataset.value || p.textContent) === value);
    });
    syncPriorityInput();
    var form = document.getElementById('contactForm');
    if (form) form.scrollIntoView({ block: 'center' });
  };

  /* ── Contato: real submit wired to api/leads.php ─────────────── */
  function initContactForm() {
    var form = document.getElementById('contactForm');
    if (!form) return;

    utmKeys.forEach(function (key) {
      var current = urlParams.get(key);
      var field = form.querySelector('[name="' + key + '"]');
      if (field) field.value = current || sessionStorage.getItem('techsallus_' + key) || '';
    });

    var lang = document.documentElement.lang || 'pt';
    var msg = {
      pt: { generic: 'Erro ao enviar. Tente novamente em instantes.', sending: 'Enviando...' },
      en: { generic: 'Something went wrong. Please try again in a moment.', sending: 'Sending...' },
      es: { generic: 'Error al enviar. Intente nuevamente en unos instantes.', sending: 'Enviando...' }
    };
    var i18n = msg[lang] || msg.pt;

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var valid = true;
      form.querySelectorAll('[required]').forEach(function (el) {
        var field = el.closest('.field');
        var ok = el.value.trim().length > 0;
        if (el.type === 'email' && ok) ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(el.value);
        field.classList.toggle('invalid', !ok);
        if (!ok) valid = false;
      });
      if (!valid) return;

      var submitBtn = form.querySelector('button[type="submit"]');
      var fd = new FormData(form);
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.dataset.originalText = submitBtn.textContent;
        submitBtn.textContent = i18n.sending;
      }

      fetch(appUrl('/api/leads.php'), { method: 'POST', body: fd })
        .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
        .then(function (r) {
          if (!r.ok || !r.data.ok) {
            throw new Error(r.data.erro || (r.data.erros ? r.data.erros.join(', ') : i18n.generic));
          }
          form.style.display = 'none';
          var success = document.getElementById('formSuccess');
          if (success) success.classList.add('show');
        })
        .catch(function (err) {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = submitBtn.dataset.originalText || submitBtn.textContent;
          }
          alert(err.message || i18n.generic);
        });
    });

    form.querySelectorAll('[required]').forEach(function (el) {
      el.addEventListener('input', function () { el.closest('.field').classList.remove('invalid'); });
      el.addEventListener('change', function () { el.closest('.field').classList.remove('invalid'); });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initNav();
    initFAQ();
    initPriorityPills();
    initContactForm();
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      initReveals();
    } else {
      document.querySelectorAll('.reveal').forEach(function (el) { el.style.opacity = 1; el.style.transform = 'none'; });
    }
  });

})();
