<?php
/* ─────────────────────────────────────────────────────────────
   public/sitemap.php — Dynamic XML sitemap
   Accessible at: /sitemap.xml (via .htaccess rewrite)
   ───────────────────────────────────────────────────────────── */
$rootDir = dirname(__DIR__);
if (!file_exists($rootDir . '/core/Env.php')) {
    $rootDir = __DIR__;
}
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
Env::load($rootDir . '/.env');

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = rtrim(Env::get('BASE_URL', 'https://techsallus.com.br'), '/');

try {
    $posts = DB::fetchAll(
        "SELECT slug, atualizado_em FROM posts WHERE status = 'publicado' ORDER BY published_at DESC"
    );
} catch (Exception $e) {
    $posts = [];
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

  <!-- Páginas estáticas -->
  <url>
    <loc><?= $baseUrl ?>/</loc>
    <changefreq>monthly</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc><?= $baseUrl ?>/resultados</loc>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  <url>
    <loc><?= $baseUrl ?>/consultorios</loc>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  <url>
    <loc><?= $baseUrl ?>/clinicas</loc>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  <url>
    <loc><?= $baseUrl ?>/hospitais</loc>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  <url>
    <loc><?= $baseUrl ?>/apure-custos</loc>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  <url>
    <loc><?= $baseUrl ?>/tecnologia</loc>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
  <url>
    <loc><?= $baseUrl ?>/sobre</loc>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
  <url>
    <loc><?= $baseUrl ?>/contato</loc>
    <changefreq>monthly</changefreq>
    <priority>0.7</priority>
  </url>
  <url>
    <loc><?= $baseUrl ?>/faq</loc>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
  <url>
    <loc><?= $baseUrl ?>/blog/</loc>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>

  <!-- Artigos do blog -->
  <?php foreach ($posts as $post): ?>
  <url>
    <loc><?= $baseUrl ?>/blog/<?= htmlspecialchars($post['slug']) ?></loc>
    <lastmod><?= date('Y-m-d', strtotime($post['atualizado_em'])) ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
  <?php endforeach; ?>

</urlset>
