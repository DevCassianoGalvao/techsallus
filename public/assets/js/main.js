
(function () {
  'use strict';

  function appUrl(path) {
    if (/^https?:\/\//.test(path)) return path;
    var script = document.querySelector('script[src*="/assets/js/main.js"]');
    var src = script ? script.getAttribute('src') : '';
    var base = src ? src.replace(/\/assets\/js\/main\.js.*$/, '') : '';
    return base + '/' + String(path).replace(/^\/+/, '');
  }

  const utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
  const urlParams = new URLSearchParams(window.location.search);
  utmKeys.forEach(function (key) {
    const value = urlParams.get(key);
    if (value) sessionStorage.setItem('techsallus_' + key, value);
  });

  
  function scrollTo(id) {
    const el = document.getElementById(id);
    if (!el) return;
    window.scrollTo({ top: el.getBoundingClientRect().top + window.pageYOffset - 68, behavior: 'smooth' });
  }

  
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
          
          const active = (raw === 'sistema' && window.scrollY < 80) ? 'inicio' : raw;
          setActive(active);
        }
      });
    }, { rootMargin: '-50% 0px -45% 0px', threshold: 0 });
    sections.forEach(s => spy.observe(s));
    
    if (window.scrollY < 80) setActive('inicio');
  }

  
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

  
  document.querySelectorAll('[data-scroll]').forEach(el => {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      scrollTo(this.dataset.scroll);
    });
  });

  
  const leadForm = document.getElementById('lead-form');
  if (leadForm) {
    utmKeys.forEach(function (key) {
      const current = urlParams.get(key);
      const field = document.getElementById(key);
      if (field) field.value = current || sessionStorage.getItem('techsallus_' + key) || '';
    });

    const lang = document.documentElement.lang || 'pt';
    const msg = {
      pt: {
        city: 'Selecione a cidade',
        loadingCities: 'Carregando cidades...',
        selectStateFirst: 'Selecione o estado primeiro',
        typeCity: 'Digite a cidade manualmente',
        required: 'Campo obrigatório',
        role: 'Selecione seu cargo',
        type: 'Selecione o tipo',
        invalidEmail: 'E-mail inválido',
        state: 'Selecione o estado',
        option: 'Selecione uma opção',
        successTitle: 'Recebemos sua solicitação!',
        successText: 'Nossa equipe entrará em contato em breve para agendar sua demonstração gratuita. Fique de olho no e-mail e WhatsApp.'
      },
      en: {
        city: 'Select the city',
        loadingCities: 'Loading cities...',
        selectStateFirst: 'Select the state first',
        typeCity: 'Enter the city manually',
        required: 'Required field',
        role: 'Select your role',
        type: 'Select the type',
        invalidEmail: 'Invalid email',
        state: 'Select the state',
        option: 'Select an option',
        successTitle: 'We received your request!',
        successText: 'Our team will contact you soon to schedule your free demo. Keep an eye on your email and WhatsApp.'
      },
      es: {
        city: 'Seleccione la ciudad',
        loadingCities: 'Cargando ciudades...',
        selectStateFirst: 'Seleccione primero el estado',
        typeCity: 'Ingrese la ciudad manualmente',
        required: 'Campo obligatorio',
        role: 'Seleccione su cargo',
        type: 'Seleccione el tipo',
        invalidEmail: 'E-mail inválido',
        state: 'Seleccione el estado',
        option: 'Seleccione una opción',
        successTitle: 'Recibimos su solicitud!',
        successText: 'Nuestro equipo se pondrá en contacto pronto para agendar su demo gratuita. Revise su e-mail y WhatsApp.'
      }
    };
    const i18n = msg[lang] || msg.pt;
    const estadoField = document.getElementById('estado');
    const cidadeField = document.getElementById('cidade');
    const cidadesCache = {};

    function setCidadeOptions(options, placeholder) {
      if (!cidadeField) return;
      cidadeField.innerHTML = '';
      const first = document.createElement('option');
      first.value = '';
      first.textContent = placeholder || i18n.city;
      cidadeField.appendChild(first);
      options.forEach(function (nome) {
        const opt = document.createElement('option');
        opt.value = nome;
        opt.textContent = nome;
        cidadeField.appendChild(opt);
      });
    }

    if (estadoField && cidadeField) {
      estadoField.addEventListener('change', async function () {
        const uf = estadoField.value;
        cidadeField.disabled = true;
        setCidadeOptions([], uf ? i18n.loadingCities : i18n.selectStateFirst);
        if (!uf) return;

        try {
          if (!cidadesCache[uf]) {
            const res = await fetch('https://servicodados.ibge.gov.br/api/v1/localidades/estados/' + encodeURIComponent(uf) + '/municipios?orderBy=nome');
            if (!res.ok) throw new Error('IBGE indisponivel');
            const data = await res.json();
            cidadesCache[uf] = data.map(function (item) { return item.nome; });
          }
          setCidadeOptions(cidadesCache[uf], i18n.city);
          cidadeField.disabled = false;
        } catch (err) {
          setCidadeOptions([], i18n.typeCity);
          cidadeField.disabled = false;
        }
      });
    }

    const showError = (id, msg) => {
      const field = document.getElementById(id);
      if (!field) return;
      field.classList.add('error');
      let err = field.parentNode.querySelector('.error-msg');
      if (!err) { err = document.createElement('span'); err.className = 'error-msg'; field.parentNode.appendChild(err); }
      err.textContent = msg;
    };
    const clearError = (id) => {
      const field = document.getElementById(id);
      if (!field) return;
      field.classList.remove('error');
      const err = field.parentNode.querySelector('.error-msg');
      if (err) err.textContent = '';
    };

    ['nome', 'cargo', 'tipo_instituicao', 'instituicao', 'email', 'whatsapp', 'cidade', 'estado', 'porte'].forEach(id => {
      const f = document.getElementById(id);
      if (f) f.addEventListener('input', () => clearError(id));
      if (f) f.addEventListener('change', () => clearError(id));
    });

    leadForm.addEventListener('submit', async function (e) {
      e.preventDefault();
      let valid = true;
      const submitBtn = leadForm.querySelector('.btn-submit');

      const nome  = document.getElementById('nome').value.trim();
      const cargo = document.getElementById('cargo').value.trim();
      const tipo  = document.getElementById('tipo_instituicao').value.trim();
      const inst  = document.getElementById('instituicao').value.trim();
      const email = document.getElementById('email').value.trim();
      const wa    = document.getElementById('whatsapp').value.trim();
      const cidade = document.getElementById('cidade').value.trim();
      const estado = document.getElementById('estado').value.trim();
      const porte  = document.getElementById('porte').value.trim();

      if (!nome)  { showError('nome', i18n.required); valid = false; }
      if (!cargo) { showError('cargo', i18n.role); valid = false; }
      if (!tipo)  { showError('tipo_instituicao', i18n.type); valid = false; }
      if (!inst)  { showError('instituicao', i18n.required); valid = false; }
      if (!email || !/\S+@\S+\.\S+/.test(email)) { showError('email', i18n.invalidEmail); valid = false; }
      if (!wa)    { showError('whatsapp', i18n.required); valid = false; }
      if (!cidade) { showError('cidade', i18n.required); valid = false; }
      if (!estado) { showError('estado', i18n.state); valid = false; }
      if (!porte)  { showError('porte', i18n.option); valid = false; }

      if (!valid) return;
      const fd = new FormData(leadForm);
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.dataset.originalText = submitBtn.textContent;
        submitBtn.textContent = lang === 'en' ? 'Sending...' : (lang === 'es' ? 'Enviando...' : 'Enviando...');
      }

      try {
        const res = await fetch(appUrl('/api/leads.php'), { method: 'POST', body: fd });
        const data = await res.json().catch(function () { return {}; });
        if (!res.ok || !data.ok) {
          throw new Error(data.erro || (data.erros ? data.erros.join(', ') : 'Erro ao enviar formulario.'));
        }
      } catch (err) {
        showError('email', err.message || 'Erro ao enviar formulario.');
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = submitBtn.dataset.originalText || submitBtn.textContent;
        }
        return;
      }

const wrap = document.getElementById('form-section-inner');
      wrap.classList.add('form-layout-success');
      wrap.innerHTML = `
        <div class="form-success">
          <div class="form-success-icon">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
              <path d="M6 14l5 5 11-10" stroke="#094a86" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h2>${i18n.successTitle}</h2>
          <p>${i18n.successText}</p>
        </div>`;
    });
  }


  const contatoForm = document.getElementById('contato-form');
  if (contatoForm) {
    utmKeys.forEach(function (key) {
      const current = urlParams.get(key);
      const field = document.getElementById(key);
      if (field) field.value = current || sessionStorage.getItem('techsallus_' + key) || '';
    });

    const clang = document.documentElement.lang || 'pt';
    const cmsg = {
      pt: { required: 'Campo obrigatório', invalidEmail: 'E-mail inválido', option: 'Selecione uma opção', sending: 'Enviando...', successTitle: 'Recebemos sua mensagem!', successText: 'Nossa equipe entrará em contato para entender a operação e indicar o caminho mais adequado para a sua realidade.', genericError: 'Erro ao enviar. Tente novamente em instantes.' },
      en: { required: 'Required field', invalidEmail: 'Invalid email', option: 'Select an option', sending: 'Sending...', successTitle: 'We received your message!', successText: 'Our team will get in touch to understand your operation and point out the best path for your reality.', genericError: 'Something went wrong. Please try again in a moment.' },
      es: { required: 'Campo obligatorio', invalidEmail: 'E-mail inválido', option: 'Seleccione una opción', sending: 'Enviando...', successTitle: '¡Recibimos su mensaje!', successText: 'Nuestro equipo se pondrá en contacto para entender la operación e indicar el camino más adecuado para su realidad.', genericError: 'Error al enviar. Intente nuevamente en unos instantes.' }
    };
    const ci18n = cmsg[clang] || cmsg.pt;

    const cShowError = (id, msg) => {
      const field = document.getElementById(id);
      if (!field) return;
      field.classList.add('error');
      let err = field.parentNode.querySelector('.error-msg');
      if (!err) { err = document.createElement('span'); err.className = 'error-msg'; field.parentNode.appendChild(err); }
      err.textContent = msg;
    };
    const cClearError = (id) => {
      const field = document.getElementById(id);
      if (!field) return;
      field.classList.remove('error');
      const err = field.parentNode.querySelector('.error-msg');
      if (err) err.textContent = '';
    };

    ['nome', 'instituicao', 'perfil_operacao', 'principal_desafio', 'email', 'whatsapp'].forEach(id => {
      const f = document.getElementById(id);
      if (f) f.addEventListener('input', () => cClearError(id));
      if (f) f.addEventListener('change', () => cClearError(id));
    });

    contatoForm.addEventListener('submit', async function (e) {
      e.preventDefault();
      let valid = true;
      const submitBtn = contatoForm.querySelector('.btn-submit');

      const nome = document.getElementById('nome').value.trim();
      const inst = document.getElementById('instituicao').value.trim();
      const perfil = document.getElementById('perfil_operacao').value.trim();
      const desafio = document.getElementById('principal_desafio').value.trim();
      const email = document.getElementById('email').value.trim();
      const wa = document.getElementById('whatsapp').value.trim();

      if (!nome) { cShowError('nome', ci18n.required); valid = false; }
      if (!inst) { cShowError('instituicao', ci18n.required); valid = false; }
      if (!perfil) { cShowError('perfil_operacao', ci18n.option); valid = false; }
      if (!desafio) { cShowError('principal_desafio', ci18n.option); valid = false; }
      if (!email || !/\S+@\S+\.\S+/.test(email)) { cShowError('email', ci18n.invalidEmail); valid = false; }
      if (!wa) { cShowError('whatsapp', ci18n.required); valid = false; }

      if (!valid) return;
      const fd = new FormData(contatoForm);
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.dataset.originalText = submitBtn.textContent;
        submitBtn.textContent = ci18n.sending;
      }

      try {
        const res = await fetch(appUrl('/api/leads.php'), { method: 'POST', body: fd });
        const data = await res.json().catch(function () { return {}; });
        if (!res.ok || !data.ok) {
          throw new Error(data.erro || (data.erros ? data.erros.join(', ') : ci18n.genericError));
        }
      } catch (err) {
        cShowError('email', err.message || ci18n.genericError);
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = submitBtn.dataset.originalText || submitBtn.textContent;
        }
        return;
      }

      const wrap = document.getElementById('form-section-inner');
      wrap.classList.add('form-layout-success');
      wrap.innerHTML = `
        <div class="form-success">
          <div class="form-success-icon">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
              <path d="M6 14l5 5 11-10" stroke="#094a86" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h2>${ci18n.successTitle}</h2>
          <p>${ci18n.successText}</p>
        </div>`;
    });
  }


  function initGSAP() {
    return;
  }

  
  if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    if (document.readyState === 'complete') {
      initGSAP();
    } else {
      window.addEventListener('load', initGSAP);
    }
  }

})();
