<?php
/* ─────────────────────────────────────────────────────────────
   public/blog/artigo.php — Artigo individual
   Roteado via .htaccess: /blog/meu-slug → ?slug=meu-slug
   ───────────────────────────────────────────────────────────── */
$rootDir = dirname(__DIR__);
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Settings.php';
require_once $rootDir . '/core/Security.php';
Env::load($rootDir . '/.env');
Security::headers();
require_once $rootDir . '/config/i18n.php';
$whatsappUrl = Settings::whatsappUrl();
$_baseUrl = rtrim(Env::get('BASE_URL', 'https://techsallus.com.br'), '/');

$slug = trim($_GET['slug'] ?? '');

if (empty($slug)) {
    header('Location: ' . Security::url('/blog/'));
    exit;
}

/* ── Artigo ──────────────────────────────────────────────────── */
try {
    $post = DB::fetchOne(
        "SELECT p.*, c.nome AS categoria_nome, c.slug AS categoria_slug
         FROM posts p
         LEFT JOIN categorias c ON c.id = p.categoria_id
         WHERE p.slug = ? AND p.status = 'publicado'
         LIMIT 1",
        [$slug]
    );
} catch (Exception $e) {
    $post = null;
}

if (!$post) {
    http_response_code(404);
    include __DIR__ . '/../404.php';
    exit;
}

/* ── Artigos relacionados ────────────────────────────────────── */
try {
    $relacionados = DB::fetchAll(
        "SELECT p.id, p.titulo, p.slug, p.resumo, p.imagem_capa, p.published_at, c.nome AS categoria_nome, c.slug AS categoria_slug
         FROM posts p
         LEFT JOIN categorias c ON c.id = p.categoria_id
         WHERE p.status = 'publicado' AND p.id != ? AND p.categoria_id = ?
         ORDER BY p.published_at DESC LIMIT 3",
        [$post['id'], $post['categoria_id']]
    );

    if (count($relacionados) < 3) {
        $idsExcluir   = array_merge([$post['id']], array_column($relacionados, 'id'));
        $placeholders = implode(',', array_fill(0, count($idsExcluir), '?'));
        $extras = DB::fetchAll(
            "SELECT p.id, p.titulo, p.slug, p.resumo, p.imagem_capa, p.published_at, c.nome AS categoria_nome, c.slug AS categoria_slug
             FROM posts p
             LEFT JOIN categorias c ON c.id = p.categoria_id
             WHERE p.status = 'publicado' AND p.id NOT IN ({$placeholders})
             ORDER BY p.published_at DESC
             LIMIT " . (3 - count($relacionados)),
            $idsExcluir
        );
        $relacionados = array_merge($relacionados, $extras);
    }
} catch (Exception $e) {
    $relacionados = [];
}

/* ── Índice do artigo (H2s extraídos do conteúdo) ───────────── */
preg_match_all('/<h2[^>]*>(.*?)<\/h2>/is', $post['conteudo'] ?? '', $matches);
$indice = $matches[1] ?? [];

/* ── Tempo de leitura ────────────────────────────────────────── */
$leitura = max(1, (int)(str_word_count(strip_tags($post['conteudo'] ?? '')) / 200));

/* ── Schema JSON-LD ──────────────────────────────────────────── */
$schema = json_encode([
    '@context'      => 'https://schema.org',
    '@type'         => 'Article',
    'headline'      => $post['titulo'],
    'description'   => $post['resumo'],
    'datePublished' => $post['published_at'],
    'dateModified'  => $post['atualizado_em'] ?? $post['published_at'],
    'author'        => ['@type' => 'Organization', 'name' => 'Techsallus'],
    'publisher'     => [
        '@type' => 'Organization',
        'name'  => 'Techsallus',
        'logo'  => ['@type' => 'ImageObject', 'url' => $_baseUrl . '/assets/img/logo.svg'],
    ],
    'image' => $post['imagem_capa'] ? $_baseUrl . $post['imagem_capa'] : null,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

/* ── Scripts injetados ───────────────────────────────────────── */
function getScripts(string $pos): string {
    try {
        return implode("\n", array_column(
            DB::fetchAll("SELECT conteudo FROM scripts_injecao WHERE posicao=? AND ativo=1", [$pos]),
            'conteudo'
        ));
    } catch (Exception $e) { return ''; }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($post['meta_title'] ?: $post['titulo'] . ' — TechSallus Blog') ?></title>
  <meta name="description" content="<?= htmlspecialchars($post['meta_desc'] ?: $post['resumo']) ?>"/>
  <meta property="og:title"       content="<?= htmlspecialchars($post['titulo']) ?>"/>
  <meta property="og:description" content="<?= htmlspecialchars($post['resumo']) ?>"/>
  <meta property="og:type"        content="article"/>
  <?php if ($post['imagem_capa']): ?>
  <meta property="og:image" content="<?= htmlspecialchars($_baseUrl) ?><?= htmlspecialchars($post['imagem_capa']) ?>"/>
  <?php endif; ?>
  <link rel="canonical" href="<?= htmlspecialchars($_baseUrl) ?>/blog/<?= htmlspecialchars($post['slug']) ?>"/>
  <link rel="icon" type="image/png" href="/assets/img/favicon.png"/>
  <script type="application/ld+json"><?= $schema ?></script>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/assets/css/main.css"/>
  <link rel="stylesheet" href="/assets/css/blog.css"/>
  <?= getScripts('head') ?>
</head>
<body>

<?php include dirname(__DIR__) . '/_partials/nav.php'; ?>

<main>

<!-- ARTICLE HERO -->
<section class="article-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Navegação estrutural">
      <a href="/">Início</a>
      <span class="breadcrumb-sep" aria-hidden="true">›</span>
      <a href="/blog/">Blog</a>
      <?php if ($post['categoria_nome']): ?>
        <span class="breadcrumb-sep" aria-hidden="true">›</span>
        <span><?= htmlspecialchars($post['categoria_nome']) ?></span>
      <?php endif; ?>
    </nav>
    <?php if ($post['categoria_nome']): ?>
      <span class="article-cat-badge"><?= htmlspecialchars($post['categoria_nome']) ?></span>
    <?php endif; ?>
    <h1><?= htmlspecialchars($post['titulo']) ?></h1>
    <div class="article-hero-meta">
      <span>Equipe TechSallus</span>
      <span class="article-hero-meta-dot" aria-hidden="true"></span>
      <?php if ($post['published_at']): ?>
        <span><?= date('d \d\e F \d\e Y', strtotime($post['published_at'])) ?></span>
        <span class="article-hero-meta-dot" aria-hidden="true"></span>
      <?php endif; ?>
      <span><?= $leitura ?> min de leitura</span>
    </div>
  </div>
</section>

<!-- ARTICLE BODY -->
<div class="article-body-wrap">
  <div class="container">

    <!-- Cover -->
    <?php if ($post['imagem_capa']): ?>
      <div style="width:100%;border-radius:12px;margin-bottom:48px;overflow:hidden;">
        <img src="<?= htmlspecialchars($post['imagem_capa']) ?>"
             alt="<?= htmlspecialchars($post['titulo']) ?>"
             width="1200" height="500" loading="eager"
             style="width:100%;height:360px;object-fit:cover;display:block;">
      </div>
    <?php else: ?>
      <div style="width:100%;height:360px;background:#e8f0f9;border-radius:12px;margin-bottom:48px;display:flex;align-items:center;justify-content:center;">
        <svg width="80" height="80" viewBox="0 0 80 80" fill="none" aria-hidden="true">
          <rect x="10" y="8" width="60" height="64" rx="6" fill="#094a86" opacity="0.12"/>
          <rect x="20" y="20" width="40" height="5" rx="2.5" fill="#094a86" opacity="0.5"/>
          <rect x="20" y="32" width="32" height="5" rx="2.5" fill="#094a86" opacity="0.4"/>
          <rect x="20" y="44" width="36" height="5" rx="2.5" fill="#094a86" opacity="0.3"/>
          <rect x="20" y="56" width="24" height="5" rx="2.5" fill="#094a86" opacity="0.2"/>
        </svg>
      </div>
    <?php endif; ?>

    <div class="article-layout">

      <!-- Content -->
      <div class="article-content">
        <div class="article-content-body">
          <?= $post['conteudo'] ?>
        </div>
      </div>

      <!-- Sidebar -->
      <aside class="article-sidebar">

        <?php if (!empty($indice)): ?>
        <div class="sidebar-toc">
          <div class="sidebar-toc-title">Neste artigo</div>
          <ol>
            <?php foreach ($indice as $i => $titulo): ?>
              <li><a href="#secao-<?= $i ?>"><?= strip_tags($titulo) ?></a></li>
            <?php endforeach; ?>
          </ol>
        </div>
        <?php endif; ?>

        <div class="sidebar-cta-card">
          <h3>Conheça o TechSallus</h3>
          <p>Veja como o sistema resolve na prática os desafios que você acabou de ler.</p>
          <a href="/#demo" class="btn-sidebar-cta sidebar-cta-btn">Agendar demonstração</a>
        </div>

      </aside>
    </div>
  </div>
</div>

<!-- RELATED ARTICLES -->
<?php if (!empty($relacionados)): ?>
<section class="related-section">
  <div class="container">
    <h2>Artigos relacionados</h2>
    <div class="related-grid">
      <?php foreach ($relacionados as $rel): ?>
        <article class="article-card" data-cat="<?= htmlspecialchars($rel['categoria_slug'] ?? '') ?>">
          <a href="/blog/<?= htmlspecialchars($rel['slug']) ?>" class="article-card-img" tabindex="-1" aria-hidden="true">
            <?php if ($rel['imagem_capa']): ?>
              <img src="<?= htmlspecialchars($rel['imagem_capa']) ?>"
                   alt="<?= htmlspecialchars($rel['titulo']) ?>"
                   loading="lazy" width="400" height="200">
            <?php else: ?>
              <svg width="40" height="40" viewBox="0 0 56 56" fill="none" aria-hidden="true">
                <rect x="8" y="6" width="40" height="44" rx="4" fill="#094a86" opacity="0.15"/>
                <rect x="14" y="14" width="28" height="3" rx="1.5" fill="#094a86" opacity="0.5"/>
                <rect x="14" y="22" width="22" height="3" rx="1.5" fill="#094a86" opacity="0.4"/>
                <rect x="14" y="30" width="26" height="3" rx="1.5" fill="#094a86" opacity="0.3"/>
              </svg>
            <?php endif; ?>
          </a>
          <div class="article-card-body">
            <?php if ($rel['categoria_nome']): ?>
              <span class="article-cat"><?= htmlspecialchars($rel['categoria_nome']) ?></span>
            <?php endif; ?>
            <h2><a href="/blog/<?= htmlspecialchars($rel['slug']) ?>"><?= htmlspecialchars($rel['titulo']) ?></a></h2>
            <div class="article-meta">
              <span><?= $rel['published_at'] ? date('d/m/Y', strtotime($rel['published_at'])) : '' ?></span>
            </div>
            <a href="/blog/<?= htmlspecialchars($rel['slug']) ?>" class="article-read-link">
              Ler artigo
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                <path d="M2 7h10M8 3l4 4-4 4" stroke="#094a86" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

</main>

<!-- FOOTER -->
<?php include dirname(__DIR__) . '/_partials/footer.php'; ?>

<?= getScripts('body') ?>
<?= getScripts('footer') ?>
<script src="/assets/js/main.js?v=20260731g"></script>
<script>
/* Adicionar IDs nos H2s para o índice */
document.querySelectorAll('.article-content-body h2').forEach(function(el, i) {
  el.id = 'secao-' + i;
});

/* Highlight do índice conforme scroll */
var links  = document.querySelectorAll('.sidebar-toc a');
var secoes = document.querySelectorAll('.article-content-body h2');

if (links.length && secoes.length) {
  var obs = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        links.forEach(function(l) { l.classList.remove('active'); });
        var ativo = document.querySelector('.sidebar-toc a[href="#' + entry.target.id + '"]');
        if (ativo) ativo.classList.add('active');
      }
    });
  }, { rootMargin: '-20% 0px -70% 0px' });
  secoes.forEach(function(s) { obs.observe(s); });
}
</script>
</body>
</html>
