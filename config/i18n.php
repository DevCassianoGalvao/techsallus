<?php
/* ─────────────────────────────────────────────────────────────
   TechSallus — i18n helper
   Usage: <?= t('nav_system') ?>
   ───────────────────────────────────────────────────────────── */

$SUPPORTED_LANGS = ['pt', 'en', 'es'];

function detectLang(): string {
    global $SUPPORTED_LANGS;
    $lang = $_COOKIE['lang'] ?? '';
    return in_array($lang, $SUPPORTED_LANGS, true) ? $lang : 'pt';
}

$LANG = detectLang();

$TRANSLATIONS = [
  'pt' => [
    'lang_label'   => 'PT',
    'lang_pt'      => 'Português',
    'lang_en'      => 'English',
    'lang_es'      => 'Español',
    'nav_home'     => 'Início',
    'nav_system'   => 'Sistema',
    'nav_modules'  => 'Módulos',
    'nav_plans'    => 'Planos',
    'nav_support'  => 'Suporte',
    'nav_blog'     => 'Blog',
    'nav_cta'      => 'Quero conhecer o sistema',
    'nav_demo'     => 'Demonstração gratuita',
    'breadcrumb_home' => 'Início',
  ],
  'en' => [
    'lang_label'   => 'EN',
    'lang_pt'      => 'Português',
    'lang_en'      => 'English',
    'lang_es'      => 'Español',
    'nav_home'     => 'Home',
    'nav_system'   => 'System',
    'nav_modules'  => 'Modules',
    'nav_plans'    => 'Plans',
    'nav_support'  => 'Support',
    'nav_blog'     => 'Blog',
    'nav_cta'      => 'See the system',
    'nav_demo'     => 'Free demo',
    'breadcrumb_home' => 'Home',
  ],
  'es' => [
    'lang_label'   => 'ES',
    'lang_pt'      => 'Português',
    'lang_en'      => 'English',
    'lang_es'      => 'Español',
    'nav_home'     => 'Inicio',
    'nav_system'   => 'Sistema',
    'nav_modules'  => 'Módulos',
    'nav_plans'    => 'Planes',
    'nav_support'  => 'Soporte',
    'nav_blog'     => 'Blog',
    'nav_cta'      => 'Conocer el sistema',
    'nav_demo'     => 'Demo gratuita',
    'breadcrumb_home' => 'Inicio',
  ],
];

function t(string $key): string {
    global $TRANSLATIONS, $LANG;
    return htmlspecialchars($TRANSLATIONS[$LANG][$key] ?? $TRANSLATIONS['pt'][$key] ?? $key);
}
