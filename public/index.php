<?php
/* Auto-detect config path: dev (public/../config) vs prod flat (techsallus/config) */
$_cfg = file_exists(__DIR__ . '/../config/i18n.php')
    ? __DIR__ . '/../config/i18n.php'
    : __DIR__ . '/config/i18n.php';
require_once $_cfg;

/* Prefix all absolute href/src with BASE when running in a subfolder */
if (BASE !== '') {
    $__b = BASE;
    ob_start(function ($h) use ($__b) {
        return str_replace(
            ['href="/', 'src="/', "href='/", "src='/"],
            ['href="'.$__b.'/', 'src="'.$__b.'/', "href='".$__b."/", "src='".$__b."/"],
            $h
        );
    });
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG ?>">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>TechSallus — Sistema de Gestão Hospitalar</title>
  <meta name="description" content="Plataforma modular de gestão hospitalar que integra agendamento, atendimento, prontuário, faturamento, estoque e financeiro em um único sistema."/>

  <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg"/>
  <link rel="shortcut icon" href="/assets/img/favicon.svg"/>

  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>

  <link rel="stylesheet" href="/assets/css/main.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"/>
</head>
<body>

<!-- ══════════════════════════════════════════════════════════════
     NAVIGATION
     ══════════════════════════════════════════════════════════════ -->
<nav class="nav">
  <div class="nav-inner">
    <a href="#" data-scroll="sistema" aria-label="TechSallus – ir ao topo">
      <img src="/assets/img/logo.png" alt="Techsallus" class="nav-logo"/>
    </a>

    <div class="nav-links">
      <a href="#sistema"  data-section="inicio"   data-scroll="sistema"><?= t('nav_home') ?></a>
      <a href="#sobre"    data-section="sistema"  data-scroll="sobre"><?= t('nav_system') ?></a>
      <a href="#modulos"  data-section="modulos"  data-scroll="modulos"><?= t('nav_modules') ?></a>
      <a href="#planos"   data-section="planos"   data-scroll="planos"><?= t('nav_plans') ?></a>
      <a href="#suporte"  data-section="suporte"  data-scroll="suporte"><?= t('nav_support') ?></a>
      <a href="/blog/"><?= t('nav_blog') ?></a>
    </div>

    <div class="nav-right">
      <div class="lang-switcher" id="lang-switcher">
        <button class="lang-btn" id="lang-btn" aria-haspopup="listbox" aria-expanded="false">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
          <span class="lang-current"><?= t('lang_label') ?></span>
          <svg class="lang-chevron" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <ul class="lang-dropdown" role="listbox" aria-label="Selecionar idioma">
          <li><a href="/setlang.php?lang=pt"<?= $LANG==='pt' ? ' class="lang-active"' : '' ?>><?= t('lang_pt') ?></a></li>
          <li><a href="/setlang.php?lang=en"<?= $LANG==='en' ? ' class="lang-active"' : '' ?>><?= t('lang_en') ?></a></li>
          <li><a href="/setlang.php?lang=es"<?= $LANG==='es' ? ' class="lang-active"' : '' ?>><?= t('lang_es') ?></a></li>
        </ul>
      </div>
      <a href="#contato" class="nav-cta" data-scroll="contato"><?= t('nav_cta') ?></a>
    </div>

    <button class="nav-hamburger" id="nav-hamburger" aria-label="Abrir menu">
      <span></span>
      <span></span>
      <span></span>
    </button>
  </div>
</nav>

<!-- ══════════════════════════════════════════════════════════════
     MOBILE MENU OVERLAY
     ══════════════════════════════════════════════════════════════ -->
<div class="mobile-menu" id="mobile-menu" role="dialog" aria-modal="true" aria-label="Menu de navegação">
  <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Fechar menu">&#x2715;</button>
  <nav>
    <a href="#sobre" data-scroll="sobre"><?= t('nav_system') ?></a>
    <a href="#modulos" data-scroll="modulos"><?= t('nav_modules') ?></a>
    <a href="#planos"  data-scroll="planos"><?= t('nav_plans') ?></a>
    <a href="#suporte" data-scroll="suporte"><?= t('nav_support') ?></a>
    <a href="/blog/"><?= t('nav_blog') ?></a>
    <a href="#contato" data-scroll="contato" style="color:#ff7300"><?= t('nav_demo') ?></a>
  </nav>
  <div class="mobile-lang">
    <a href="/setlang.php?lang=pt"<?= $LANG==='pt' ? ' class="lang-active"' : '' ?>>PT</a>
    <span class="mobile-lang-sep">|</span>
    <a href="/setlang.php?lang=en"<?= $LANG==='en' ? ' class="lang-active"' : '' ?>>EN</a>
    <span class="mobile-lang-sep">|</span>
    <a href="/setlang.php?lang=es"<?= $LANG==='es' ? ' class="lang-active"' : '' ?>>ES</a>
  </div>
</div>

<main>

<!-- ══════════════════════════════════════════════════════════════
     HERO — #sistema
     ══════════════════════════════════════════════════════════════ -->
<section id="sistema" class="hero">
  <!-- Line-grid texture -->
  <svg class="hero-grid-bg" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" aria-hidden="true">
    <defs>
      <pattern id="linegrid" width="40" height="40" patternUnits="userSpaceOnUse">
        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.13)" stroke-width="0.8"/>
      </pattern>
    </defs>
    <rect width="100%" height="100%" fill="url(#linegrid)"/>
  </svg>

  <div class="container">
    <div class="hero-inner">
      <!-- Left copy -->
      <div class="hero-copy">
        <div class="hero-label">
          <div class="hero-label-bar"></div>
          <span>Sistema de gestão hospitalar</span>
        </div>

        <h1 class="hero-headline">
          O sistema completo para a sua clínica ou hospital funcionar melhor
          <em> a partir de hoje</em>
        </h1>

        <p class="hero-sub">
          O TechSallus é uma plataforma modular de gestão hospitalar que integra agendamento, atendimento, prontuário, faturamento, estoque e financeiro em um único sistema — sem precisar contratar vários fornecedores.
        </p>

        <div class="hero-ctas">
          <a href="#contato" class="btn-primary" data-scroll="contato">Quero conhecer o sistema</a>
          <a href="#planos"  class="btn-outline-white" data-scroll="planos">Ver planos e preços</a>
        </div>

        <div class="hero-pills">
          <div class="hero-pill"><span class="hero-pill-star">✦</span><span>Tudo integrado em uma plataforma</span></div>
          <div class="hero-pill"><span class="hero-pill-star">✦</span><span>Sem instalação local</span></div>
          <div class="hero-pill"><span class="hero-pill-star">✦</span><span>Suporte técnico humanizado</span></div>
          <div class="hero-pill"><span class="hero-pill-star">✦</span><span>Configurado para a sua realidade</span></div>
        </div>
      </div>

      <!-- Right — dashboard mockup -->
      <div class="hero-visual">
        <div class="dashboard-frame">
          <div class="dashboard-inner">
            <!-- Topbar -->
            <div class="db-topbar">
              <div class="db-dots">
                <span class="db-dot orange"></span>
                <span class="db-dot dim"></span>
                <span class="db-dot dim"></span>
              </div>
              <div class="db-tabs">
                <span class="db-tab active">Agenda</span>
                <span class="db-tab">Atendimento</span>
                <span class="db-tab">Financeiro</span>
              </div>
              <div class="db-avatar-wrap">
                <div class="db-avatar">DM</div>
              </div>
            </div>
            <!-- Stat cards -->
            <div class="db-stats">
              <div class="db-stat-card">
                <div class="db-stat-label">Agendamentos</div>
                <div class="db-stat-value">1.247</div>
                <div class="db-stat-tag">▲ +12% mês</div>
              </div>
              <div class="db-stat-card">
                <div class="db-stat-label">Atendimentos</div>
                <div class="db-stat-value">863</div>
                <div class="db-stat-tag">▲ +8% mês</div>
              </div>
              <div class="db-stat-card">
                <div class="db-stat-label">Faturamento</div>
                <div class="db-stat-value">R$ 92k</div>
                <div class="db-stat-tag">▲ +15% mês</div>
              </div>
            </div>
            <!-- Bar chart -->
            <div class="db-chart">
              <div class="db-chart-head">
                <span class="db-chart-title">Atendimentos por semana</span>
                <span class="db-chart-link">Ver relatório →</span>
              </div>
              <div class="db-bars">
                <div class="db-bar" style="height:42%"></div>
                <div class="db-bar" style="height:68%"></div>
                <div class="db-bar" style="height:55%"></div>
                <div class="db-bar" style="height:84%"></div>
                <div class="db-bar" style="height:61%"></div>
                <div class="db-bar highlight" style="height:95%"></div>
                <div class="db-bar" style="height:74%"></div>
              </div>
              <div class="db-days">
                <span class="db-day">S</span><span class="db-day">T</span>
                <span class="db-day">Q</span><span class="db-day">Q</span>
                <span class="db-day">S</span><span class="db-day">S</span>
                <span class="db-day">D</span>
              </div>
            </div>
            <!-- Appointments -->
            <div class="db-appts">
              <div class="db-appts-title">Próximos agendamentos</div>
              <div class="db-appt-row">
                <div class="db-appt-left">
                  <div class="db-appt-avatar">M</div>
                  <span class="db-appt-name">Maria Santos</span>
                </div>
                <div class="db-appt-right">
                  <span class="db-appt-time">09:00</span>
                  <span class="db-appt-type">Consulta</span>
                </div>
              </div>
              <div class="db-appt-row">
                <div class="db-appt-left">
                  <div class="db-appt-avatar">J</div>
                  <span class="db-appt-name">João Oliveira</span>
                </div>
                <div class="db-appt-right">
                  <span class="db-appt-time">09:45</span>
                  <span class="db-appt-type">Retorno</span>
                </div>
              </div>
              <div class="db-appt-row">
                <div class="db-appt-left">
                  <div class="db-appt-avatar">A</div>
                  <span class="db-appt-name">Ana Costa</span>
                </div>
                <div class="db-appt-right">
                  <span class="db-appt-time">10:30</span>
                  <span class="db-appt-type">Exame</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     SOBRE — #sobre
     ══════════════════════════════════════════════════════════════ -->
<section id="sobre" class="sobre">
  <div class="container">
    <span class="section-label">Sobre</span>
    <h2 class="sobre-heading">Um sistema criado para quem vive a rotina da saúde</h2>

    <div class="sobre-layout">
      <div class="sobre-text">
        <p>
          Desde 1994, a Techsallus desenvolve e aprimora o TechSallus — um sistema de gestão hospitalar construído com base em décadas de experiência dentro de clínicas, hospitais e laboratórios reais. Não somos uma startup que nunca pisou numa UPA. Conhecemos o fluxo do Pronto-Atendimento, a lógica do Centro Cirúrgico, as regras do faturamento TISS e as exigências dos convênios. Por isso, o sistema faz sentido na prática. O TechSallus é modular: você ativa os módulos que precisa hoje e expande conforme a sua operação cresce.
        </p>
      </div>

      <div class="stat-cards">
        <div class="stat-card">
          <div class="stat-num" data-target="30" data-prefix="+">+30</div>
          <div class="stat-label">anos</div>
          <div class="stat-sub">desenvolvendo e evoluindo o sistema</div>
        </div>
        <div class="stat-card">
          <div class="stat-num" data-target="9">9</div>
          <div class="stat-label">módulos</div>
          <div class="stat-sub">integrados nativamente</div>
        </div>
        <div class="stat-card">
          <div class="stat-num" data-target="7">7</div>
          <div class="stat-label">estados</div>
          <div class="stat-sub">com clientes ativos no Brasil</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     MÓDULOS — #modulos
     ══════════════════════════════════════════════════════════════ -->
<section id="modulos" class="modulos">
  <div class="container">
    <span class="section-label">Módulos</span>
    <h2 class="modulos-heading">Tudo que a sua instituição precisa,<br>em um único sistema</h2>

    <div class="modulos-grid">

      <!-- Agendamento -->
      <div class="modulo-card">
        <div class="modulo-icon">
          <svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
            <rect x="3" y="5" width="22" height="20" rx="3" stroke="#ff7300" stroke-width="1.8"/>
            <path d="M3 11h22" stroke="#ff7300" stroke-width="1.8"/>
            <path d="M9 3v4M19 3v4" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round"/>
            <rect x="8" y="15" width="4" height="4" rx="1" fill="#ff7300" opacity="0.7"/>
            <rect x="16" y="15" width="4" height="4" rx="1" fill="#ff7300" opacity="0.4"/>
          </svg>
        </div>
        <div class="modulo-name">Agendamento</div>
        <p class="modulo-desc">Agenda múltipla por profissional, convênio, serviço e local. Multi-agendamento de até 10 procedimentos simultâneos.</p>
      </div>

      <!-- Atendimento -->
      <div class="modulo-card">
        <div class="modulo-icon">
          <svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
            <path d="M14 4C9 4 5 8 5 13s4 9 9 9 9-4 9-9-4-9-9-9z" stroke="#ff7300" stroke-width="1.8"/>
            <path d="M14 9v4l3 3" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round"/>
            <circle cx="14" cy="13" r="1.5" fill="#ff7300"/>
          </svg>
        </div>
        <div class="modulo-name">Atendimento</div>
        <p class="modulo-desc">Guias no padrão TISS, ABRAMGE e SUS. Ambientes: Ambulatório, UTI, Pronto-Atendimento, Centro Cirúrgico.</p>
      </div>

      <!-- Business Intelligence -->
      <div class="modulo-card">
        <div class="modulo-icon">
          <svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
            <path d="M4 22l6-8 5 4 5-10 4 4" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="10" cy="14" r="2" fill="#ff7300" opacity="0.7"/>
            <circle cx="15" cy="18" r="2" fill="#ff7300" opacity="0.5"/>
            <circle cx="20" cy="12" r="2" fill="#ff7300" opacity="0.4"/>
          </svg>
        </div>
        <div class="modulo-name">Business Intelligence</div>
        <p class="modulo-desc">Análise OLAP com cubos multidimensionais. Dados em tempo real para decisões estratégicas.</p>
      </div>

      <!-- Prontuário Eletrônico -->
      <div class="modulo-card">
        <div class="modulo-icon">
          <svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
            <rect x="6" y="3" width="16" height="22" rx="2" stroke="#ff7300" stroke-width="1.8"/>
            <path d="M10 9h8M10 13h8M10 17h5" stroke="#ff7300" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="10" cy="9" r="1" fill="#ff7300"/>
          </svg>
        </div>
        <div class="modulo-name">Prontuário Eletrônico</div>
        <p class="modulo-desc">Padronizado pela Resolução CFM N.1639-2002. Especialidades automatizadas. Laudos com áudio.</p>
      </div>

      <!-- Estoque -->
      <div class="modulo-card">
        <div class="modulo-icon">
          <svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
            <path d="M4 22V12l10-7 10 7v10H4z" stroke="#ff7300" stroke-width="1.8" stroke-linejoin="round"/>
            <rect x="10" y="16" width="8" height="6" rx="1" stroke="#ff7300" stroke-width="1.5"/>
            <path d="M14 13v-2M12 12h4" stroke="#ff7300" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="modulo-name">Estoque</div>
        <p class="modulo-desc">Controle de materiais e medicamentos. Farmácia Satélite, integração com BRASÍNDICE e SIMPRO.</p>
      </div>

      <!-- Faturamento -->
      <div class="modulo-card">
        <div class="modulo-icon">
          <svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
            <rect x="4" y="6" width="20" height="16" rx="2" stroke="#ff7300" stroke-width="1.8"/>
            <path d="M4 11h20" stroke="#ff7300" stroke-width="1.5"/>
            <path d="M9 16h4M9 19h6" stroke="#ff7300" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="20" cy="17.5" r="3" fill="#ff7300" opacity="0.25"/>
            <path d="M19 17.5h2M20 16.5v2" stroke="#ff7300" stroke-width="1.2" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="modulo-name">Faturamento</div>
        <p class="modulo-desc">Geração automática de faturas TISS, ABRAMGE e SUS. Controle de glosas integrado ao financeiro.</p>
      </div>

      <!-- Financeiro -->
      <div class="modulo-card">
        <div class="modulo-icon">
          <svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
            <circle cx="14" cy="14" r="10" stroke="#ff7300" stroke-width="1.8"/>
            <path d="M14 8v2M14 18v2M10.5 11.5a2 2 0 013.5 0 2 2 0 003.5 0" stroke="#ff7300" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M10.5 16.5a2 2 0 003.5 0 2 2 0 003.5 0" stroke="#ff7300" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="modulo-name">Financeiro</div>
        <p class="modulo-desc">Contas a pagar e a receber, conciliação bancária, fluxo de caixa previsto e realizado.</p>
      </div>

      <!-- Regras de Negócio -->
      <div class="modulo-card">
        <div class="modulo-icon">
          <svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
            <circle cx="14" cy="14" r="4" stroke="#ff7300" stroke-width="1.8"/>
            <path d="M14 4v3M14 21v3M4 14h3M21 14h3" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M6.9 6.9l2.1 2.1M18.9 18.9l2.1 2.1M6.9 21.1l2.1-2.1M18.9 9.1l2.1-2.1" stroke="#ff7300" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="modulo-name">Regras de Negócio</div>
        <p class="modulo-desc">Configuração de convênios, profissionais e tabelas de preços. Sistema multiempresa.</p>
      </div>

      <!-- Segurança -->
      <div class="modulo-card">
        <div class="modulo-icon">
          <svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
            <path d="M14 3L5 7v7c0 5 4 9 9 10 5-1 9-5 9-10V7L14 3z" stroke="#ff7300" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M10 14l3 3 5-5" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="modulo-name">Segurança</div>
        <p class="modulo-desc">Controle de acesso granular por usuário, auditoria interna completa de todas as operações.</p>
      </div>

    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     POR QUÊ TECHSALLUS — #porque
     ══════════════════════════════════════════════════════════════ -->
<section id="porque" class="porque">
  <div class="container">
    <span class="section-label">Por que Techsallus</span>
    <h2 class="porque-heading">Por que clínicas e hospitais em todo o Brasil escolhem o TechSallus?</h2>

    <div class="diferenciais-grid">

      <div class="diferencial-item">
        <svg class="check-icon" width="22" height="22" viewBox="0 0 22 22" fill="none" aria-hidden="true">
          <circle cx="11" cy="11" r="11" fill="rgba(255,115,0,0.2)"/>
          <path d="M6.5 11l3 3 6-6" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div>
          <div class="diferencial-title">Sistema modular</div>
          <p class="diferencial-desc">Você paga só pelo que usa e ativa novos módulos quando precisar.</p>
        </div>
      </div>

      <div class="diferencial-item">
        <svg class="check-icon" width="22" height="22" viewBox="0 0 22 22" fill="none" aria-hidden="true">
          <circle cx="11" cy="11" r="11" fill="rgba(255,115,0,0.2)"/>
          <path d="M6.5 11l3 3 6-6" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div>
          <div class="diferencial-title">Configurado para a sua realidade</div>
          <p class="diferencial-desc">Sem engessamento, o sistema se adapta ao seu fluxo de trabalho.</p>
        </div>
      </div>

      <div class="diferencial-item">
        <svg class="check-icon" width="22" height="22" viewBox="0 0 22 22" fill="none" aria-hidden="true">
          <circle cx="11" cy="11" r="11" fill="rgba(255,115,0,0.2)"/>
          <path d="M6.5 11l3 3 6-6" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div>
          <div class="diferencial-title">Suporte humano, não um chatbot</div>
          <p class="diferencial-desc">Equipe técnica especializada, segunda a sexta, das 8h às 18h.</p>
        </div>
      </div>

      <div class="diferencial-item">
        <svg class="check-icon" width="22" height="22" viewBox="0 0 22 22" fill="none" aria-hidden="true">
          <circle cx="11" cy="11" r="11" fill="rgba(255,115,0,0.2)"/>
          <path d="M6.5 11l3 3 6-6" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div>
          <div class="diferencial-title">30 anos de maturidade</div>
          <p class="diferencial-desc">Não é versão beta. É um sistema que já passou por todas as mudanças do setor.</p>
        </div>
      </div>

      <div class="diferencial-item">
        <svg class="check-icon" width="22" height="22" viewBox="0 0 22 22" fill="none" aria-hidden="true">
          <circle cx="11" cy="11" r="11" fill="rgba(255,115,0,0.2)"/>
          <path d="M6.5 11l3 3 6-6" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div>
          <div class="diferencial-title">Presente em todo o Brasil</div>
          <p class="diferencial-desc">São Paulo, Rio de Janeiro, Bahia e mais 4 estados com equipe de suporte local.</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     SUPORTE — #suporte
     ══════════════════════════════════════════════════════════════ -->
<section id="suporte" class="suporte">
  <div class="container">
    <div class="suporte-inner">

      <!-- Left text -->
      <div class="suporte-text">
        <span class="section-label">Suporte</span>
        <h2 class="suporte-heading">Suporte técnico que entende da operação</h2>
        <p class="suporte-desc">
          Nossa equipe de suporte é formada por profissionais especializados em processos hospitalares. Quando você liga, não precisa explicar o que é uma guia TISS ou um procedimento ABRAMGE — a gente já sabe.
        </p>

        <ul class="suporte-list">
          <li class="suporte-item">
            <div class="suporte-dot"></div>
            <span class="suporte-item-text">Atendimento: segunda a sexta, das 8h às 12h e das 14h às 18h</span>
          </li>
          <li class="suporte-item">
            <div class="suporte-dot"></div>
            <span class="suporte-item-text">Abertura de chamados online com acompanhamento em tempo real</span>
          </li>
          <li class="suporte-item">
            <div class="suporte-dot"></div>
            <span class="suporte-item-text">Visitas presenciais para clientes em estados atendidos</span>
          </li>
        </ul>

        <a href="https://suporte.portalsallus.com.br/" target="_blank" rel="noopener noreferrer" class="btn-orange">
          Abrir chamado
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M3 8h10M9 4l4 4-4 4" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
      </div>

      <!-- Right illustration -->
      <div class="suporte-ilustracao">
        <svg width="420" height="420" viewBox="0 0 420 420" fill="none" aria-hidden="true">
          <circle cx="210" cy="210" r="178" fill="#e8f0f9"/>
          <!-- Centre person -->
          <circle cx="210" cy="163" r="50" fill="#094a86"/>
          <path d="M184 163 a26 26 0 0 1 52 0" stroke="white" stroke-width="3.5" fill="none" stroke-linecap="round"/>
          <rect x="180" y="159" width="11" height="18" rx="5.5" fill="#ff7300"/>
          <rect x="229" y="159" width="11" height="18" rx="5.5" fill="#ff7300"/>
          <path d="M232 176 q6 0 6 8 l-4 4" stroke="#ff7300" stroke-width="2.5" stroke-linecap="round"/>
          <path d="M170 212 q0 18 40 22 q40-4 40-22" fill="#094a86"/>
          <!-- Dashed connection lines -->
          <line x1="108" y1="122" x2="165" y2="152" stroke="#c8d9ee" stroke-width="1.5" stroke-dasharray="5 4"/>
          <line x1="312" y1="122" x2="255" y2="152" stroke="#c8d9ee" stroke-width="1.5" stroke-dasharray="5 4"/>
          <line x1="94"  y1="210" x2="165" y2="197" stroke="#c8d9ee" stroke-width="1.5" stroke-dasharray="5 4"/>
          <line x1="326" y1="210" x2="255" y2="197" stroke="#c8d9ee" stroke-width="1.5" stroke-dasharray="5 4"/>
          <line x1="128" y1="304" x2="178" y2="248" stroke="#c8d9ee" stroke-width="1.5" stroke-dasharray="5 4"/>
          <line x1="292" y1="304" x2="242" y2="248" stroke="#c8d9ee" stroke-width="1.5" stroke-dasharray="5 4"/>
          <!-- Top-left card: document -->
          <rect x="62" y="86" width="62" height="56" rx="10" fill="white" stroke="#e2eaf3" stroke-width="1.5"/>
          <line x1="76" y1="104" x2="112" y2="104" stroke="#094a86" stroke-width="2" stroke-linecap="round"/>
          <line x1="76" y1="114" x2="104" y2="114" stroke="#094a86" stroke-width="2" stroke-linecap="round" opacity="0.5"/>
          <line x1="76" y1="124" x2="108" y2="124" stroke="#094a86" stroke-width="2" stroke-linecap="round" opacity="0.3"/>
          <!-- Top-right card: clock -->
          <rect x="296" y="86" width="62" height="56" rx="10" fill="white" stroke="#e2eaf3" stroke-width="1.5"/>
          <circle cx="327" cy="114" r="17" stroke="#094a86" stroke-width="1.8" opacity="0.7"/>
          <line x1="327" y1="106" x2="327" y2="114" stroke="#ff7300" stroke-width="2.2" stroke-linecap="round"/>
          <line x1="327" y1="114" x2="334" y2="118" stroke="#ff7300" stroke-width="2.2" stroke-linecap="round"/>
          <!-- Mid-left card: checkmark -->
          <rect x="46" y="180" width="62" height="56" rx="10" fill="white" stroke="#e2eaf3" stroke-width="1.5"/>
          <circle cx="77" cy="208" r="15" fill="rgba(9,74,134,0.1)"/>
          <path d="M69 208l6 6 11-11" stroke="#094a86" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
          <!-- Mid-right card: bar chart -->
          <rect x="312" y="180" width="62" height="56" rx="10" fill="white" stroke="#e2eaf3" stroke-width="1.5"/>
          <rect x="322" y="217" width="8" height="10" rx="2" fill="#094a86" opacity="0.4"/>
          <rect x="334" y="207" width="8" height="20" rx="2" fill="#094a86" opacity="0.6"/>
          <rect x="346" y="200" width="8" height="27" rx="2" fill="#ff7300"/>
          <rect x="358" y="210" width="8" height="17" rx="2" fill="#094a86" opacity="0.5"/>
          <!-- Bottom-left card: star -->
          <rect x="98" y="272" width="62" height="56" rx="10" fill="white" stroke="#e2eaf3" stroke-width="1.5"/>
          <path d="M129 284l3 8.5h9l-7.5 5.5 2.8 8.5L129 301l-7.3 5.5 2.8-8.5-7.5-5.5h9z" fill="#ff7300" opacity="0.9"/>
          <!-- Bottom-right card: shield -->
          <rect x="260" y="272" width="62" height="56" rx="10" fill="white" stroke="#e2eaf3" stroke-width="1.5"/>
          <path d="M291 282l-17 7v10c0 8.5 7.5 15 17 16.5 9.5-1.5 17-8 17-16.5v-10z" stroke="#094a86" stroke-width="1.8" fill="none" stroke-linejoin="round"/>
          <path d="M283 300l5.5 5.5 9.5-9.5" stroke="#ff7300" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
          <!-- Accent dots -->
          <circle cx="210" cy="360" r="10" fill="#ff7300" opacity="0.12"/>
          <circle cx="184" cy="372" r="6" fill="#094a86" opacity="0.1"/>
          <circle cx="236" cy="372" r="6" fill="#094a86" opacity="0.1"/>
        </svg>
      </div>

    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     ONDE ESTAMOS — #onde
     ══════════════════════════════════════════════════════════════ -->
<section id="onde" class="onde">
  <div class="container">
    <div class="onde-header">
      <span class="section-label" style="display:block;text-align:center">Onde estamos</span>
      <h2 class="onde-heading">Atendemos clínicas e hospitais em todo o Brasil</h2>
      <p class="onde-sub">
        O TechSallus está presente em São Paulo, Rio de Janeiro, Espírito Santo, Rondônia, Maranhão, Sergipe, Alagoas e Bahia. Se você está em outro estado, fale com a gente — estamos em expansão.
      </p>
    </div>

    <div id="map-loading" style="display:flex;align-items:center;justify-content:center;gap:12px;height:600px;font-family:'DM Sans',sans-serif;font-size:14px;color:#4a6080;">
      <div style="width:22px;height:22px;border:2px solid #c8daf0;border-top-color:#094a86;border-radius:50%;animation:spin .8s linear infinite;flex-shrink:0;"></div>
      Carregando mapa...
    </div>
    <div id="mapa-brasil"></div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     FORMULÁRIO DE CONTATO — #contato
     ══════════════════════════════════════════════════════════════ -->
<section id="contato" class="form-section">
  <div class="container">
    <div id="form-section-inner" class="form-layout">

      <!-- Left: form -->
      <div class="form-container">
        <span class="form-section-label">Demonstração gratuita</span>
        <h2 class="form-heading">Quer ver o TechSallus funcionando na prática?</h2>
        <p class="form-sub">
          Agende uma demonstração gratuita e veja como o sistema se encaixa no fluxo da sua clínica ou hospital. Em 30 minutos, você entende por que mais de 7 estados já confiam no TechSallus.
        </p>

        <form id="lead-form" novalidate>
          <div class="form-field">
            <label for="nome">Nome completo</label>
            <input type="text" id="nome" name="nome" placeholder="Seu nome" autocomplete="name"/>
          </div>
          <div class="form-field">
            <label for="instituicao">Instituição</label>
            <input type="text" id="instituicao" name="instituicao" placeholder="Clínica, hospital, laboratório..."/>
          </div>
          <div class="form-field">
            <label for="cargo">Cargo</label>
            <input type="text" id="cargo" name="cargo" placeholder="Diretor, Gerente, Médico..."/>
          </div>
          <div class="form-field">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="email@clinica.com.br" autocomplete="email"/>
          </div>
          <div class="form-field">
            <label for="whatsapp">WhatsApp</label>
            <input type="tel" id="whatsapp" name="whatsapp" placeholder="(11) 99999-9999" autocomplete="tel"/>
          </div>
          <div class="form-field">
            <label for="prof">Quantos profissionais de saúde atuam na sua instituição?</label>
            <select id="prof" name="prof">
              <option value="" disabled selected>Selecione</option>
              <option value="1-2">1 – 2</option>
              <option value="3-9">3 – 9</option>
              <option value="10-30">10 – 30</option>
              <option value="31-100">31 – 100</option>
              <option value="100+">100+</option>
            </select>
          </div>
          <button type="submit" class="btn-submit">Quero ver uma demonstração gratuita</button>
        </form>
      </div>

      <!-- Right: trust panel -->
      <div class="trust-col">
        <div class="trust-grid">
          <div class="trust-stat">
            <div class="trust-num">+30 anos</div>
            <div class="trust-sub">de mercado</div>
          </div>
          <div class="trust-stat">
            <div class="trust-num">7 estados</div>
            <div class="trust-sub">com clientes ativos</div>
          </div>
          <div class="trust-stat">
            <div class="trust-num">9 módulos</div>
            <div class="trust-sub">integrados</div>
          </div>
          <div class="trust-stat">
            <div class="trust-num">100% cloud</div>
            <div class="trust-sub">sem instalação local</div>
          </div>
        </div>

        <div class="trust-checks">
          <div class="trust-check">
            <div class="trust-check-icon">
              <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                <path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <span>Sem compromisso — cancele quando quiser</span>
          </div>
          <div class="trust-check">
            <div class="trust-check-icon">
              <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                <path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <span>Demonstração gratuita, sem cartão de crédito</span>
          </div>
          <div class="trust-check">
            <div class="trust-check-icon">
              <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                <path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <span>Dados protegidos por criptografia HTTPS</span>
          </div>
          <div class="trust-check">
            <div class="trust-check-icon">
              <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                <path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <span>Conforme LGPD e normas do CFM</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     PLANOS — #planos
     ══════════════════════════════════════════════════════════════ -->
<section id="planos" class="planos">
  <div class="container">
    <div class="planos-header">
      <span class="section-label" style="display:block;text-align:center">Planos</span>
      <h2 class="planos-heading">Escolha o plano certo para o porte da sua instituição</h2>
      <p class="planos-sub">Sem taxa de implantação escondida, sem fidelidade obrigatória no primeiro ciclo.</p>
    </div>

    <div class="planos-grid">

      <!-- Básico -->
      <div class="plano-card">
        <div class="plano-name">Básico</div>
        <div class="plano-desc">Ideal para consultórios e clínicas com até 2 profissionais</div>
        <div class="plano-price-wrap">
          <span class="plano-price">R$ 300</span>
          <span class="plano-period">/mês</span>
        </div>
        <div class="plano-features">
          <div class="plano-feature">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="8" fill="rgba(9,74,134,0.1)"/>
              <path d="M4.5 8l2.5 2.5 4.5-4" stroke="#094a86" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="plano-feature-text">Agenda Múltipla</span>
          </div>
          <div class="plano-feature">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="8" fill="rgba(9,74,134,0.1)"/>
              <path d="M4.5 8l2.5 2.5 4.5-4" stroke="#094a86" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="plano-feature-text">Padrão TISS</span>
          </div>
          <div class="plano-feature">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="8" fill="rgba(9,74,134,0.1)"/>
              <path d="M4.5 8l2.5 2.5 4.5-4" stroke="#094a86" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="plano-feature-text">Controle Financeiro</span>
          </div>
          <div class="plano-feature">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="8" fill="rgba(9,74,134,0.1)"/>
              <path d="M4.5 8l2.5 2.5 4.5-4" stroke="#094a86" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="plano-feature-text">100 disparos de SMS/mês</span>
          </div>
        </div>
        <a href="#contato" class="btn-plano-outline" data-scroll="contato">Começar agora</a>
      </div>

      <!-- Padrão (featured) -->
      <div class="plano-card featured">
        <div class="plano-badge">Mais contratado</div>
        <div class="plano-name">Padrão</div>
        <div class="plano-desc">Para clínicas com equipe de 3 a 9 profissionais</div>
        <div class="plano-price-wrap">
          <span class="plano-price">R$ 500</span>
          <span class="plano-period">/mês</span>
        </div>
        <div class="plano-features">
          <div class="plano-feature">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="8" fill="rgba(255,115,0,0.25)"/>
              <path d="M4.5 8l2.5 2.5 4.5-4" stroke="#ff7300" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="plano-feature-text">Tudo do Básico</span>
          </div>
          <div class="plano-feature">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="8" fill="rgba(255,115,0,0.25)"/>
              <path d="M4.5 8l2.5 2.5 4.5-4" stroke="#ff7300" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="plano-feature-text">Gestão de Glosa</span>
          </div>
          <div class="plano-feature">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="8" fill="rgba(255,115,0,0.25)"/>
              <path d="M4.5 8l2.5 2.5 4.5-4" stroke="#ff7300" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="plano-feature-text">Relatório Personalizado</span>
          </div>
          <div class="plano-feature">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="8" fill="rgba(255,115,0,0.25)"/>
              <path d="M4.5 8l2.5 2.5 4.5-4" stroke="#ff7300" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="plano-feature-text">Confirmação Automática</span>
          </div>
          <div class="plano-feature">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="8" fill="rgba(255,115,0,0.25)"/>
              <path d="M4.5 8l2.5 2.5 4.5-4" stroke="#ff7300" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="plano-feature-text">Agendamento Online</span>
          </div>
          <div class="plano-feature">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="8" fill="rgba(255,115,0,0.25)"/>
              <path d="M4.5 8l2.5 2.5 4.5-4" stroke="#ff7300" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="plano-feature-text">300 disparos SMS/mês</span>
          </div>
        </div>
        <a href="#contato" class="btn-plano-primary" data-scroll="contato">Quero este plano</a>
      </div>

      <!-- Premium -->
      <div class="plano-card">
        <div class="plano-name">Premium</div>
        <div class="plano-desc">Para hospitais e instituições com 10 ou mais profissionais</div>
        <div class="plano-price-wrap">
          <div class="plano-price-consult">Sob consulta</div>
          <div class="plano-price-consult-sub">Feito para a sua realidade</div>
        </div>
        <div class="plano-features">
          <div class="plano-feature">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="8" fill="rgba(9,74,134,0.1)"/>
              <path d="M4.5 8l2.5 2.5 4.5-4" stroke="#094a86" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="plano-feature-text">Tudo do Padrão</span>
          </div>
          <div class="plano-feature">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="8" fill="rgba(9,74,134,0.1)"/>
              <path d="M4.5 8l2.5 2.5 4.5-4" stroke="#094a86" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="plano-feature-text">Usuários ilimitados</span>
          </div>
          <div class="plano-feature">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="8" fill="rgba(9,74,134,0.1)"/>
              <path d="M4.5 8l2.5 2.5 4.5-4" stroke="#094a86" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="plano-feature-text">Pesquisa de satisfação</span>
          </div>
          <div class="plano-feature">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="8" fill="rgba(9,74,134,0.1)"/>
              <path d="M4.5 8l2.5 2.5 4.5-4" stroke="#094a86" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="plano-feature-text">500 disparos SMS</span>
          </div>
          <div class="plano-feature">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="8" fill="rgba(9,74,134,0.1)"/>
              <path d="M4.5 8l2.5 2.5 4.5-4" stroke="#094a86" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="plano-feature-text">Gestor dedicado</span>
          </div>
        </div>
        <a href="#contato" class="btn-plano-outline" data-scroll="contato">Falar com um especialista</a>
      </div>

    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     FOOTER — #blog
     ══════════════════════════════════════════════════════════════ -->
<footer id="blog" class="site-footer">
  <div class="container">
    <div class="footer-grid">

      <!-- Col 1: Brand -->
      <div>
        <img src="/assets/img/logo.png" alt="Techsallus" class="footer-logo"/>
        <p class="footer-tagline">Sistema de gestão hospitalar desde 1994</p>
      </div>

      <!-- Col 2: Sistema links -->
      <div>
        <div class="footer-col-title">Sistema</div>
        <div class="footer-links">
          <a href="#sobre" data-scroll="sobre">Sistema</a>
          <a href="#modulos" data-scroll="modulos">Módulos</a>
          <a href="#planos"  data-scroll="planos">Planos</a>
          <a href="#suporte" data-scroll="suporte">Suporte</a>
          <a href="/blog/">Blog</a>
        </div>
      </div>

      <!-- Col 3: Contact (no WhatsApp) -->
      <div>
        <div class="footer-col-title">Contato</div>
        <div class="footer-contact">
          <!-- E-mail -->
          <div class="footer-contact-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <rect x="2" y="4" width="20" height="16" rx="2"/>
              <path d="M2 7l10 7 10-7"/>
            </svg>
            <a href="mailto:faleconosco@techsallus.com.br">faleconosco@techsallus.com.br</a>
          </div>
          <!-- Address -->
          <div class="footer-contact-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
              <circle cx="12" cy="9" r="2.5"/>
            </svg>
            <span>Rua Ewerton Visco, 290 · Ed. Boulevard Side, Salas 1601 · Salvador, Bahia</span>
          </div>
          <!-- Hours -->
          <div class="footer-contact-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="12" cy="12" r="9"/>
              <path d="M12 7v5l3 3"/>
            </svg>
            <span>Segunda a sexta, 8h às 18h</span>
          </div>
        </div>
      </div>

      <!-- Col 4: States -->
      <div>
        <div class="footer-col-title">Presente em</div>
        <div class="footer-states">
          <div class="footer-state"><div class="footer-state-dot"></div><span class="footer-state-name">São Paulo</span></div>
          <div class="footer-state"><div class="footer-state-dot"></div><span class="footer-state-name">Rio de Janeiro</span></div>
          <div class="footer-state"><div class="footer-state-dot"></div><span class="footer-state-name">Bahia</span></div>
          <div class="footer-state"><div class="footer-state-dot"></div><span class="footer-state-name">Espírito Santo</span></div>
          <div class="footer-state"><div class="footer-state-dot"></div><span class="footer-state-name">Rondônia</span></div>
          <div class="footer-state"><div class="footer-state-dot"></div><span class="footer-state-name">Maranhão</span></div>
          <div class="footer-state"><div class="footer-state-dot"></div><span class="footer-state-name">Sergipe</span></div>
          <div class="footer-state"><div class="footer-state-dot"></div><span class="footer-state-name">Alagoas</span></div>
        </div>
      </div>

    </div>

    <!-- Bottom bar -->
    <div class="footer-bottom">
      <span class="footer-copy">© 2025 Techsallus. Todos os direitos reservados.</span>
    </div>
  </div>
</footer>

</main>

<!-- ══════════════════════════════════════════════════════════════
     WHATSAPP FLOAT
     ══════════════════════════════════════════════════════════════ -->
<a href="https://wa.me/557181299624?text=Ol%C3%A1%2C%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es%20sobre%20o%20sistema%20de%20voc%C3%AAs"
   target="_blank"
   rel="noopener noreferrer"
   class="whatsapp-float"
   aria-label="Fale conosco pelo WhatsApp">
  <svg viewBox="0 0 24 24" fill="white" width="26" height="26" aria-hidden="true">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
    <path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.974-1.304A9.963 9.963 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z" fill="none" stroke="white" stroke-width="1.5"/>
  </svg>
</a>

<!-- ══════════════════════════════════════════════════════════════
     SCRIPTS
     ══════════════════════════════════════════════════════════════ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script src="/assets/js/main.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
(function () {
  const ATIVOS = new Set(['SP','RJ','ES','RO','MA','SE','AL','BA']);

  const ESTADOS = {
    '11':{ uf:'RO', latlng:[-10.9,-61.9] },
    '12':{ uf:'AC', latlng:[-9.0, -70.8] },
    '13':{ uf:'AM', latlng:[-3.4, -65.8] },
    '14':{ uf:'RR', latlng:[2.0,  -61.4] },
    '15':{ uf:'PA', latlng:[-3.4, -52.3] },
    '16':{ uf:'AP', latlng:[1.4,  -51.8] },
    '17':{ uf:'TO', latlng:[-10.2,-48.3] },
    '22':{ uf:'PI', latlng:[-7.7, -42.7] },
    '23':{ uf:'CE', latlng:[-5.2, -39.3] },
    '24':{ uf:'RN', latlng:[-5.8, -36.6] },
    '25':{ uf:'PB', latlng:[-7.1, -36.8] },
    '26':{ uf:'PE', latlng:[-8.3, -37.9] },
    '31':{ uf:'MG', latlng:[-18.5,-44.6] },
    '41':{ uf:'PR', latlng:[-24.6,-51.5] },
    '42':{ uf:'SC', latlng:[-27.2,-50.2] },
    '43':{ uf:'RS', latlng:[-30.0,-53.2] },
    '50':{ uf:'MS', latlng:[-20.5,-54.8] },
    '51':{ uf:'MT', latlng:[-12.6,-56.1] },
    '52':{ uf:'GO', latlng:[-15.9,-49.8] },
    '53':{ uf:'DF', latlng:[-15.8,-47.9] },
    '21':{ uf:'MA', latlng:[-2.2,  -44.302] },
    '27':{ uf:'AL', latlng:[-9.0,  -36.8]   },
    '28':{ uf:'SE', latlng:[-10.2, -37.4]   },
    '29':{ uf:'BA', latlng:[-12.2, -41.7]   },
    '32':{ uf:'ES', latlng:[-19.1, -40.7]   },
    '33':{ uf:'RJ', latlng:[-21.7, -42.7]   },
    '35':{ uf:'SP', latlng:[-21.6, -48.9]   },
    '11x':{ uf:'RO', latlng:[-8.0, -63.900] },
  };

  const CAPITAIS = {
    SP:[-23.548,-46.633], RJ:[-22.906,-43.172],
    ES:[-20.319,-40.337], RO:[-8.761, -63.900],
    MA:[-2.529, -44.302], SE:[-10.947,-37.073],
    AL:[-9.665, -35.735], BA:[-12.971,-38.501],
  };

  const IBGE_UF = {
    '11':'RO','12':'AC','13':'AM','14':'RR','15':'PA','16':'AP','17':'TO',
    '21':'MA','22':'PI','23':'CE','24':'RN','25':'PB','26':'PE','27':'AL',
    '28':'SE','29':'BA','31':'MG','32':'ES','33':'RJ','35':'SP',
    '41':'PR','42':'SC','43':'RS','50':'MS','51':'MT','52':'GO','53':'DF'
  };

  const map = L.map('mapa-brasil', {
    zoomControl:false, scrollWheelZoom:false,
    doubleClickZoom:false, touchZoom:false,
    dragging:false, keyboard:false, attributionControl:false,
  });

  fetch('https://servicodados.ibge.gov.br/api/v3/malhas/paises/BR?resolucao=1&formato=application/vnd.geo+json')
    .then(r => { if(!r.ok) throw new Error(r.status); return r.json(); })
    .then(geo => {
      document.getElementById('map-loading').style.display = 'none';
      document.getElementById('mapa-brasil').style.display = 'block';

      const layer = L.geoJSON(geo, {
        style: f => {
          const cod  = String(f.properties?.codarea || '');
          const uf   = IBGE_UF[cod];
          const ativo = uf && ATIVOS.has(uf);
          return {
            fillColor:   ativo ? '#094a86' : '#c8daf0',
            fillOpacity: 1,
            color:       '#ffffff',
            weight:      1.5,
          };
        },
        onEachFeature: (f, lyr) => {
          const uf = IBGE_UF[String(f.properties?.codarea||'')];
          if (ATIVOS.has(uf)) {
            lyr.on('mouseover', function(){ this.setStyle({fillColor:'#073d70'}); });
            lyr.on('mouseout',  function(){ this.setStyle({fillColor:'#094a86'}); });
          }
        }
      }).addTo(map);

      const isMobile = window.innerWidth <= 768;
      map.fitBounds(layer.getBounds(), { padding: isMobile ? [40,20] : [24,24] });

      Object.values(ESTADOS).forEach(info => {
        const ativo = ATIVOS.has(info.uf);
        const icon = L.divIcon({
          className: '',
          html: `<span class="sigla-label ${ativo ? 'sigla-ativo' : 'sigla-inativo'}">${info.uf}</span>`,
          iconSize:   [28, 16],
          iconAnchor: [14, 8],
        });
        L.marker(info.latlng, { icon, interactive:false, keyboard:false }).addTo(map);
      });

      Object.entries(CAPITAIS).forEach(([uf, ll]) => {
        L.circleMarker(ll, {
          radius:14, fillColor:'#ff7300', color:'transparent',
          fillOpacity:0.22, interactive:false,
        }).addTo(map);
        L.circleMarker(ll, {
          radius:6, fillColor:'#ff7300', color:'#ffffff',
          weight:2.5, fillOpacity:1, interactive:false,
        }).addTo(map);
      });
    })
    .catch(err => {
      document.getElementById('map-loading').innerHTML =
        '<span style="color:#a32d2d;font-family:\'DM Sans\',sans-serif">Erro ao carregar o mapa.</span>';
    });
})();
</script>

</body>
</html>
