<?php
/* Shared nav — used by every public page (index.php + the 9 new pages, 404.php, blog/preview.php).
   Requires $LANG (set by config/i18n.php) to already be in scope. */
$_navPath = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/');
if ($_navPath === '') $_navPath = '/';

$_navLinks = [
    ['href' => '/',              'label' => t('nav_home'),          'match' => '/'],
    ['href' => '/consultorios',  'label' => t('nav_consultorios'),  'match' => '/consultorios'],
    ['href' => '/clinicas',      'label' => t('nav_clinicas'),      'match' => '/clinicas'],
    ['href' => '/hospitais',     'label' => t('nav_hospitais'),     'match' => '/hospitais'],
    ['href' => '/apure-custos',  'label' => t('nav_apure_custos'),  'match' => '/apure-custos'],
    ['href' => '/sobre',         'label' => t('nav_sobre'),         'match' => '/sobre'],
    ['href' => '/contato',       'label' => t('nav_contato'),       'match' => '/contato'],
    ['href' => '/blog/',         'label' => t('nav_blog'),          'match' => '/blog', 'prefix' => true],
];

function navIsActive(string $current, array $link): bool
{
    if (!empty($link['prefix'])) {
        return str_starts_with($current, $link['match']);
    }
    return $current === $link['match'];
}
?>
<!-- NAV -->
<nav class="nav">
  <div class="nav-inner">
    <a href="/" aria-label="TechSallus – voltar ao site">
      <img src="/assets/img/logo.png" alt="Techsallus" class="nav-logo"/>
    </a>
    <div class="nav-links">
      <?php foreach ($_navLinks as $_link): ?>
        <a href="<?= htmlspecialchars($_link['href']) ?>"<?= navIsActive($_navPath, $_link) ? ' class="active"' : '' ?>><?= $_link['label'] ?></a>
      <?php endforeach; ?>
    </div>
    <div class="nav-right">
      <div class="lang-switcher" id="lang-switcher">
        <button class="lang-btn" id="lang-btn" aria-haspopup="listbox" aria-expanded="false">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
          <span class="lang-current"><?= t('lang_label') ?></span>
          <svg class="lang-chevron" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <ul class="lang-dropdown" role="listbox" aria-label="Selecionar idioma">
          <li><a href="/setlang.php?lang=pt"<?= $LANG === 'pt' ? ' class="lang-active"' : '' ?>><?= t('lang_pt') ?></a></li>
          <li><a href="/setlang.php?lang=en"<?= $LANG === 'en' ? ' class="lang-active"' : '' ?>><?= t('lang_en') ?></a></li>
          <li><a href="/setlang.php?lang=es"<?= $LANG === 'es' ? ' class="lang-active"' : '' ?>><?= t('lang_es') ?></a></li>
        </ul>
      </div>
      <a href="/contato" class="nav-cta"><?= t('nav_top_cta') ?></a>
    </div>
    <button class="nav-hamburger" id="nav-hamburger" aria-label="Menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobile-menu" role="dialog" aria-modal="true" aria-label="Menu de navegação">
  <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Fechar menu">&#x2715;</button>
  <nav>
    <?php foreach ($_navLinks as $_link): ?>
      <a href="<?= htmlspecialchars($_link['href']) ?>"><?= $_link['label'] ?></a>
    <?php endforeach; ?>
    <a href="/contato" style="color:#ff7300"><?= t('nav_top_cta') ?></a>
  </nav>
  <div class="mobile-lang">
    <a href="/setlang.php?lang=pt"<?= $LANG === 'pt' ? ' class="lang-active"' : '' ?>><?= t('lang_pt') ?></a>
    <span class="mobile-lang-sep">·</span>
    <a href="/setlang.php?lang=en"<?= $LANG === 'en' ? ' class="lang-active"' : '' ?>><?= t('lang_en') ?></a>
    <span class="mobile-lang-sep">·</span>
    <a href="/setlang.php?lang=es"<?= $LANG === 'es' ? ' class="lang-active"' : '' ?>><?= t('lang_es') ?></a>
  </div>
</div>
