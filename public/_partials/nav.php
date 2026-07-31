<?php
/* Shared nav — used by every public page. Requires $LANG (from config/i18n.php) in scope. */
require_once __DIR__ . '/icons.php';

$_navPath = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/');
if ($_navPath === '') $_navPath = '/';

$_topLinks = [
    ['href' => '/',            'label' => t('nav_home'),         'match' => '/'],
    ['href' => '/resultados',  'label' => t('nav_resultados'),   'match' => '/resultados'],
    ['href' => '/tecnologia',  'label' => t('nav_tecnologia'),   'match' => '/tecnologia'],
    ['href' => '/sobre',       'label' => t('nav_sobre'),        'match' => '/sobre'],
    ['href' => '/faq',         'label' => t('nav_faq_link'),     'match' => '/faq'],
];
$_solLinks = [
    ['href' => '/consultorios',  'label' => t('nav_consultorios')],
    ['href' => '/clinicas',      'label' => t('nav_clinicas')],
    ['href' => '/hospitais',     'label' => t('nav_hospitais')],
    ['href' => '/apure-custos',  'label' => t('nav_apure_custos')],
];
?>
<header>
  <div class="navbar wrap" style="padding:9px 10px 9px 22px;max-width:1120px">
    <a href="/" class="brand"><img src="/assets/img/techsallus-logo.png" alt="TechSallus"/></a>
    <nav class="navlinks">
      <a href="/"<?= $_navPath === '/' ? ' class="active"' : '' ?>><?= t('nav_home') ?></a>
      <div class="has-drop">
        <button type="button"><?= t('nav_solucoes') ?><svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></button>
        <div class="dropdown">
          <?php foreach ($_solLinks as $_l): ?>
            <a href="<?= htmlspecialchars($_l['href']) ?>"><?= $_l['label'] ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php foreach (array_slice($_topLinks, 1) as $_l): ?>
        <a href="<?= htmlspecialchars($_l['href']) ?>"<?= $_navPath === $_l['match'] ? ' class="active"' : '' ?>><?= $_l['label'] ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="nav-cta">
      <div class="lang-switcher" id="lang-switcher">
        <button class="lang-btn" id="lang-btn" aria-haspopup="listbox" aria-expanded="false">
          <span><?= t('lang_label') ?></span>
          <svg viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <div class="lang-dropdown" role="listbox" aria-label="Selecionar idioma">
          <a href="/setlang.php?lang=pt"<?= $LANG === 'pt' ? ' class="lang-active"' : '' ?>><?= t('lang_pt') ?></a>
          <a href="/setlang.php?lang=en"<?= $LANG === 'en' ? ' class="lang-active"' : '' ?>><?= t('lang_en') ?></a>
          <a href="/setlang.php?lang=es"<?= $LANG === 'es' ? ' class="lang-active"' : '' ?>><?= t('lang_es') ?></a>
        </div>
      </div>
      <button class="burger" id="burgerBtn" aria-label="Menu"><svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
      <a href="/contato" class="btn btn-primary<?= $_navPath === '/contato' ? ' active' : '' ?>"><span class="full"><?= t('nav_top_cta') ?></span></a>
    </div>
  </div>
  <div class="mobile-panel" id="mobilePanel">
    <a href="/"><?= t('nav_home') ?></a>
    <div class="mp-group"><?= t('nav_solucoes') ?></div>
    <?php foreach ($_solLinks as $_l): ?>
      <a href="<?= htmlspecialchars($_l['href']) ?>"><?= $_l['label'] ?></a>
    <?php endforeach; ?>
    <?php foreach (array_slice($_topLinks, 1) as $_l): ?>
      <a href="<?= htmlspecialchars($_l['href']) ?>"><?= $_l['label'] ?></a>
    <?php endforeach; ?>
    <a href="/contato"><?= t('nav_top_cta') ?></a>
    <div class="mp-lang">
      <a href="/setlang.php?lang=pt"<?= $LANG === 'pt' ? ' class="lang-active"' : '' ?>><?= t('lang_pt') ?></a>
      <a href="/setlang.php?lang=en"<?= $LANG === 'en' ? ' class="lang-active"' : '' ?>><?= t('lang_en') ?></a>
      <a href="/setlang.php?lang=es"<?= $LANG === 'es' ? ' class="lang-active"' : '' ?>><?= t('lang_es') ?></a>
    </div>
  </div>
</header>
