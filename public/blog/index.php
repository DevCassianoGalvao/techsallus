<?php
/* ─────────────────────────────────────────────────────────────
   TechSallus Blog — listagem de artigos
   ───────────────────────────────────────────────────────────── */
require_once __DIR__ . '/../../config/i18n.php';

$ARTICLES = [
  [
    'slug'       => 'como-reduzir-glosas-faturamento-hospitalar',
    'title'      => 'Como reduzir glosas no faturamento hospitalar e recuperar receita',
    'excerpt'    => 'Glosas são um dos maiores vilões das finanças hospitalares. Entenda as causas mais comuns e veja estratégias práticas para reduzir rejeições e aumentar o índice de aprovação das suas faturas junto aos convênios.',
    'cat'        => 'Faturamento',
    'cat_slug'   => 'faturamento',
    'author'     => 'Equipe TechSallus',
    'date'       => '12 mai. 2025',
    'read'       => '8 min',
    'img_bg'     => '#e8f0f9',
    'img_accent' => '#094a86',
  ],
  [
    'slug'       => 'agendamento-online-clinicas-vantagens',
    'title'      => 'Agendamento online para clínicas: vantagens reais e como implementar',
    'excerpt'    => 'O agendamento online deixou de ser diferencial e se tornou expectativa do paciente. Veja como clínicas de diferentes portes estão adotando essa funcionalidade e os resultados que já conseguiram.',
    'cat'        => 'Agendamento',
    'cat_slug'   => 'agendamento',
    'author'     => 'Equipe TechSallus',
    'date'       => '05 mai. 2025',
    'read'       => '6 min',
    'img_bg'     => '#fff4eb',
    'img_accent' => '#ff7300',
  ],
  [
    'slug'       => 'prontuario-eletronico-obrigatorio-cfm',
    'title'      => 'Prontuário eletrônico: o que a Resolução CFM N.1639-2002 exige na prática',
    'excerpt'    => 'A regulamentação do CFM sobre prontuário eletrônico tem mais de 20 anos, mas ainda gera dúvidas. Explicamos o que é obrigatório, o que é recomendado e como o TechSallus já vem configurado para atender todos os requisitos.',
    'cat'        => 'Prontuário',
    'cat_slug'   => 'prontuario',
    'author'     => 'Equipe TechSallus',
    'date'       => '28 abr. 2025',
    'read'       => '10 min',
    'img_bg'     => '#eef4ff',
    'img_accent' => '#094a86',
  ],
  [
    'slug'       => 'indicadores-gestao-hospitalar-essenciais',
    'title'      => 'Os 10 indicadores de gestão hospitalar que todo gestor deveria acompanhar',
    'excerpt'    => 'Taxa de ocupação, giro de leitos, custo médio por atendimento, índice de glosas — conheça os KPIs que revelam a saúde financeira e operacional da sua instituição e saiba como calculá-los.',
    'cat'        => 'Gestão',
    'cat_slug'   => 'gestao',
    'author'     => 'Equipe TechSallus',
    'date'       => '20 abr. 2025',
    'read'       => '12 min',
    'img_bg'     => '#f0faf4',
    'img_accent' => '#1a8a4a',
  ],
  [
    'slug'       => 'tiss-padrao-intercambio-informacoes-saude',
    'title'      => 'TISS na prática: guia completo para clínicas e hospitais',
    'excerpt'    => 'O padrão TISS (Troca de Informações em Saúde Suplementar) é obrigatório para todos que atendem planos de saúde. Entenda cada componente, os prazos de envio e como evitar erros que causam glosas.',
    'cat'        => 'Faturamento',
    'cat_slug'   => 'faturamento',
    'author'     => 'Equipe TechSallus',
    'date'       => '14 abr. 2025',
    'read'       => '9 min',
    'img_bg'     => '#e8f0f9',
    'img_accent' => '#094a86',
  ],
  [
    'slug'       => 'lgpd-saude-dados-pacientes-conformidade',
    'title'      => 'LGPD na saúde: o que muda na gestão de dados dos seus pacientes',
    'excerpt'    => 'A Lei Geral de Proteção de Dados tem impacto direto em clínicas e hospitais. Veja quais dados são considerados sensíveis na área da saúde, quais são suas obrigações como controlador e como o TechSallus ajuda a manter conformidade.',
    'cat'        => 'Tecnologia',
    'cat_slug'   => 'tecnologia',
    'author'     => 'Equipe TechSallus',
    'date'       => '07 abr. 2025',
    'read'       => '11 min',
    'img_bg'     => '#f5f0ff',
    'img_accent' => '#6b4fa0',
  ],
];

$CATEGORIES = [
  ['label' => 'Todos',       'slug' => 'todos'],
  ['label' => 'Agendamento', 'slug' => 'agendamento'],
  ['label' => 'Faturamento', 'slug' => 'faturamento'],
  ['label' => 'Prontuário',  'slug' => 'prontuario'],
  ['label' => 'Gestão',      'slug' => 'gestao'],
  ['label' => 'Tecnologia',  'slug' => 'tecnologia'],
];
?>
<!DOCTYPE html>
<html lang="<?= $LANG ?>">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Blog — TechSallus · Gestão Hospitalar</title>
  <meta name="description" content="Artigos, guias e boas práticas sobre gestão hospitalar, faturamento TISS, prontuário eletrônico e tecnologia na saúde."/>

  <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg"/>
  <link rel="shortcut icon" href="/assets/img/favicon.svg"/>

  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>

  <link rel="stylesheet" href="/assets/css/main.css"/>
  <link rel="stylesheet" href="/assets/css/blog.css"/>
</head>
<body>

<!-- NAV -->
<nav class="nav">
  <div class="nav-inner">
    <a href="/" aria-label="TechSallus – voltar ao site">
      <img src="/assets/img/logo.png" alt="Techsallus" class="nav-logo"/>
    </a>
    <div class="nav-links">
      <a href="/"><?= t('nav_home') ?></a>
      <a href="/#sobre"><?= t('nav_system') ?></a>
      <a href="/#modulos"><?= t('nav_modules') ?></a>
      <a href="/#planos"><?= t('nav_plans') ?></a>
      <a href="/#suporte"><?= t('nav_support') ?></a>
      <a href="/blog/" class="active"><?= t('nav_blog') ?></a>
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
      <a href="/#contato" class="nav-cta"><?= t('nav_cta') ?></a>
    </div>
    <button class="nav-hamburger" id="nav-hamburger" aria-label="Abrir menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobile-menu" role="dialog" aria-modal="true" aria-label="Menu de navegação">
  <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Fechar menu">&#x2715;</button>
  <nav>
    <a href="/"><?= t('nav_home') ?></a>
    <a href="/#sobre"><?= t('nav_system') ?></a>
    <a href="/#modulos"><?= t('nav_modules') ?></a>
    <a href="/#planos"><?= t('nav_plans') ?></a>
    <a href="/#suporte"><?= t('nav_support') ?></a>
    <a href="/blog/"><?= t('nav_blog') ?></a>
    <a href="/#contato" style="color:#ff7300"><?= t('nav_demo') ?></a>
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

<!-- BLOG HERO -->
<section class="blog-hero">
  <div class="container">
    <span class="section-label">Blog</span>
    <h1>Gestão hospitalar na prática</h1>
    <p>Artigos, guias e boas práticas para quem administra clínicas, hospitais e laboratórios.</p>
  </div>
</section>

<!-- CATEGORY FILTER + GRID -->
<section class="blog-listing">
  <div class="container">

    <div class="cat-filter" role="group" aria-label="Filtrar por categoria">
      <?php foreach ($CATEGORIES as $c): ?>
        <button
          class="cat-pill<?= $c['slug'] === 'todos' ? ' active' : '' ?>"
          data-cat="<?= htmlspecialchars($c['slug']) ?>"
          type="button"
        ><?= htmlspecialchars($c['label']) ?></button>
      <?php endforeach; ?>
    </div>

    <div class="blog-grid">
      <?php foreach ($ARTICLES as $a): ?>
        <article class="article-card" data-cat="<?= htmlspecialchars($a['cat_slug']) ?>">

          <a href="/blog/artigo.php?slug=<?= htmlspecialchars($a['slug']) ?>" class="article-card-img" tabindex="-1" aria-hidden="true" style="background:<?= $a['img_bg'] ?>; display:flex; align-items:center; justify-content:center; height:200px; text-decoration:none;">
            <svg width="56" height="56" viewBox="0 0 56 56" fill="none" aria-hidden="true">
              <rect x="8" y="6" width="40" height="44" rx="4" fill="<?= $a['img_accent'] ?>" opacity="0.15"/>
              <rect x="14" y="14" width="28" height="3" rx="1.5" fill="<?= $a['img_accent'] ?>" opacity="0.5"/>
              <rect x="14" y="22" width="22" height="3" rx="1.5" fill="<?= $a['img_accent'] ?>" opacity="0.4"/>
              <rect x="14" y="30" width="26" height="3" rx="1.5" fill="<?= $a['img_accent'] ?>" opacity="0.3"/>
              <rect x="14" y="38" width="18" height="3" rx="1.5" fill="<?= $a['img_accent'] ?>" opacity="0.25"/>
            </svg>
          </a>

          <div class="article-card-body">
            <span class="article-cat"><?= htmlspecialchars($a['cat']) ?></span>
            <h2><a href="/blog/artigo.php?slug=<?= htmlspecialchars($a['slug']) ?>"><?= htmlspecialchars($a['title']) ?></a></h2>
            <p><?= htmlspecialchars($a['excerpt']) ?></p>
            <div class="article-meta">
              <span><?= htmlspecialchars($a['author']) ?></span>
              <span class="article-meta-dot"></span>
              <span><?= htmlspecialchars($a['date']) ?></span>
              <span class="article-meta-dot"></span>
              <span><?= htmlspecialchars($a['read']) ?> de leitura</span>
            </div>
            <a href="/blog/artigo.php?slug=<?= htmlspecialchars($a['slug']) ?>" class="article-read-link">
              Ler artigo
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                <path d="M2 7h10M8 3l4 4-4 4" stroke="#094a86" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <div id="no-results" class="no-results" style="display:none">
      Nenhum artigo encontrado nessa categoria.
    </div>

  </div>
</section>

<!-- CTA BANNER -->
<section class="blog-cta-section">
  <div class="container">
    <div class="blog-cta-inner">
      <div>
        <h2 class="blog-cta-title">Quer ver o TechSallus funcionando?</h2>
        <p class="blog-cta-sub">Agende uma demonstração gratuita e entenda como o sistema resolve os desafios do seu dia a dia.</p>
      </div>
      <a href="/#contato" class="btn-primary">Quero uma demonstração gratuita</a>
    </div>
  </div>
</section>

</main>

<!-- FOOTER -->
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <img src="/assets/img/logo.png" alt="Techsallus" class="footer-logo"/>
        <p class="footer-tagline">Sistema de gestão hospitalar desde 1994</p>
      </div>
      <div>
        <div class="footer-col-title">Sistema</div>
        <div class="footer-links">
          <a href="/">Sistema</a>
          <a href="/#modulos">Módulos</a>
          <a href="/#planos">Planos</a>
          <a href="/#suporte">Suporte</a>
          <a href="/blog/">Blog</a>
        </div>
      </div>
      <div>
        <div class="footer-col-title">Contato</div>
        <div class="footer-contact">
          <div class="footer-contact-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 7l10 7 10-7"/></svg>
            <a href="mailto:faleconosco@techsallus.com.br">faleconosco@techsallus.com.br</a>
          </div>
          <div class="footer-contact-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
            <span>Rua Ewerton Visco, 290 · Ed. Boulevard Side, Salas 1601 · Salvador, Bahia</span>
          </div>
          <div class="footer-contact-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
            <span>Segunda a sexta, 8h às 18h</span>
          </div>
        </div>
      </div>
      <div>
        <div class="footer-col-title">Presente em</div>
        <div class="footer-states">
          <?php foreach (['São Paulo','Rio de Janeiro','Bahia','Espírito Santo','Rondônia','Maranhão','Sergipe','Alagoas'] as $s): ?>
            <div class="footer-state"><div class="footer-state-dot"></div><span class="footer-state-name"><?= htmlspecialchars($s) ?></span></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <span class="footer-copy">© 2025 Techsallus. Todos os direitos reservados.</span>
      <a href="/admin/" class="footer-access">Acesso Restrito</a>
    </div>
  </div>
</footer>

<a href="https://wa.me/557181299624?text=Ol%C3%A1%2C%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es%20sobre%20o%20sistema%20de%20voc%C3%AAs"
   target="_blank" rel="noopener noreferrer" class="whatsapp-float" aria-label="Fale conosco pelo WhatsApp">
  <svg viewBox="0 0 24 24" fill="white" width="26" height="26" aria-hidden="true">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
    <path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.974-1.304A9.963 9.963 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z" fill="none" stroke="white" stroke-width="1.5"/>
  </svg>
</a>

<script src="/assets/js/main.js"></script>
<script src="/assets/js/blog.js"></script>
</body>
</html>
