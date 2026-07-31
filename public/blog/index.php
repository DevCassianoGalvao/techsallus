<?php
/* ─────────────────────────────────────────────────────────────
   public/blog/index.php — Blog público — listagem com paginação
   ───────────────────────────────────────────────────────────── */
$rootDir = dirname(dirname(__DIR__));
if (!file_exists($rootDir . '/core/Env.php')) {
    $rootDir = dirname(__DIR__);
}
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Settings.php';
require_once $rootDir . '/core/Security.php';
Env::load($rootDir . '/.env');
Security::headers();
$whatsappUrl = Settings::whatsappUrl();
$_baseUrl = rtrim(Env::get('BASE_URL', 'https://techsallus.com.br'), '/');

/* ── Parâmetros ──────────────────────────────────────────────── */
$busca     = trim($_GET['busca'] ?? '');
$pagina    = max(1, (int)($_GET['pagina'] ?? 1));
$porPagina = $busca ? 9 : 100; // sem busca: carrega tudo para filtro JS
$offset    = ($pagina - 1) * $porPagina;

/* ── Query dinâmica ──────────────────────────────────────────── */
$where  = ["p.status = 'publicado'"];
$params = [];

if ($busca) {
    $where[]  = "(p.titulo LIKE ? OR p.resumo LIKE ? OR p.tags LIKE ?)";
    $termo    = "%{$busca}%";
    $params   = array_merge($params, [$termo, $termo, $termo]);
}

$whereSQL = 'WHERE ' . implode(' AND ', $where);

/* ── Dados do banco ──────────────────────────────────────────── */
try {
    $total = (int)(DB::fetchOne(
        "SELECT COUNT(*) AS total FROM posts p LEFT JOIN categorias c ON c.id = p.categoria_id {$whereSQL}",
        $params
    )['total'] ?? 0);

    $totalPaginas = (int)ceil($total / $porPagina);

    $posts = DB::fetchAll(
        "SELECT p.id, p.titulo, p.slug, p.resumo, p.imagem_capa, p.tags, p.published_at,
                c.nome AS categoria_nome, c.slug AS categoria_slug
         FROM posts p
         LEFT JOIN categorias c ON c.id = p.categoria_id
         {$whereSQL}
         ORDER BY p.published_at DESC
         LIMIT {$porPagina} OFFSET {$offset}",
        $params
    );

    $categorias = DB::fetchAll(
        "SELECT c.id, c.nome, c.slug, COUNT(p.id) AS total
         FROM categorias c
         LEFT JOIN posts p ON p.categoria_id = c.id AND p.status = 'publicado'
         GROUP BY c.id
         HAVING total > 0
         ORDER BY c.nome"
    );
} catch (Exception $e) {
    $posts = $categorias = [];
    $total = $totalPaginas = 0;
}

/* ── SEO ─────────────────────────────────────────────────────── */
$metaTitle = "Blog | Gestão hospitalar e tecnologia para saúde | Techsallus";
$metaDesc  = "Artigos, guias e boas práticas sobre gestão hospitalar, faturamento TISS, prontuário eletrônico e tecnologia na saúde.";

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
  <title><?= htmlspecialchars($metaTitle) ?></title>
  <meta name="description" content="<?= htmlspecialchars($metaDesc) ?>"/>
  <meta property="og:title"       content="<?= htmlspecialchars($metaTitle) ?>"/>
  <meta property="og:description" content="<?= htmlspecialchars($metaDesc) ?>"/>
  <meta property="og:type"        content="website"/>
  <link rel="canonical" href="<?= htmlspecialchars($_baseUrl) ?>/blog/"/>
  <link rel="icon" type="image/png" href="/assets/img/favicon.png"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/assets/css/main.css"/>
  <link rel="stylesheet" href="/assets/css/blog.css">
  <?= getScripts('head') ?>
</head>
<body>

<!-- NAV -->
<nav class="nav">
  <div class="nav-inner">
    <a href="/" aria-label="TechSallus – voltar ao site">
      <img src="/assets/img/logo.png" alt="Techsallus" class="nav-logo"/>
    </a>
    <div class="nav-links">
      <a href="/">Início</a>
      <a href="/#sobre">Sistema</a>
      <a href="/#modulos">Módulos</a>
      <a href="/#planos">Planos</a>
      <a href="/#suporte">Suporte</a>
      <a href="/blog/" class="active">Blog</a>
    </div>
    <div class="nav-right">
      <div class="lang-switcher" id="lang-switcher">
        <button class="lang-btn" id="lang-btn" aria-haspopup="listbox" aria-expanded="false">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
          <span class="lang-current">PT</span>
          <svg class="lang-chevron" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <ul class="lang-dropdown" role="listbox" aria-label="Selecionar idioma">
          <li><a href="#" class="lang-active">Português</a></li>
          <li><a href="#">English</a></li>
          <li><a href="#">Español</a></li>
        </ul>
      </div>
      <a href="/#demo" class="nav-cta">Quero conhecer o sistema</a>
    </div>
    <button class="nav-hamburger" id="nav-hamburger" aria-label="Abrir menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobile-menu" role="dialog" aria-modal="true" aria-label="Menu de navegação">
  <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Fechar menu">&#x2715;</button>
  <nav>
    <a href="/">Início</a>
    <a href="/#sobre">Sistema</a>
    <a href="/#modulos">Módulos</a>
    <a href="/#planos">Planos</a>
    <a href="/#suporte">Suporte</a>
    <a href="/blog/">Blog</a>
    <a href="/#demo" style="color:#ff7300">Quero uma demonstração</a>
  </nav>
</div>

<main>

<!-- BLOG HERO -->
<section class="blog-hero">
  <div class="container">
    <span class="section-label">Blog Techsallus</span>
    <h1>Gestão hospitalar na prática</h1>
    <p>Artigos, guias e boas práticas para quem administra clínicas, hospitais e laboratórios.</p>

    <form class="blog-search" method="GET" action="/blog/">
      <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Buscar artigos...">
      <button type="submit" aria-label="Buscar">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      </button>
    </form>
  </div>
</section>

<!-- CATEGORY FILTER + GRID -->
<section class="blog-listing">
  <div class="container">

    <?php if ($busca): ?>
      <p class="search-result-info">
        <?= $total ?> resultado<?= $total != 1 ? 's' : '' ?> para
        "<strong><?= htmlspecialchars($busca) ?></strong>"
        <a href="/blog/">Limpar busca</a>
      </p>
    <?php else: ?>

    <div class="cat-filter" role="group" aria-label="Filtrar por categoria">
      <button class="cat-pill active" data-cat="todos" type="button">Todos</button>
      <?php foreach ($categorias as $cat): ?>
        <button
          class="cat-pill"
          data-cat="<?= htmlspecialchars($cat['slug']) ?>"
          type="button"
        ><?= htmlspecialchars($cat['nome']) ?></button>
      <?php endforeach; ?>
    </div>

    <?php endif; ?>

    <?php if (empty($posts)): ?>
      <div class="no-results" style="text-align:center;padding:64px 0">
        <div style="font-size:48px;margin-bottom:16px">📭</div>
        <h2>Nenhum artigo encontrado</h2>
        <p>Tente outros termos ou <a href="/blog/">veja todos os artigos</a>.</p>
      </div>
    <?php else: ?>

      <div class="blog-grid">
        <?php foreach ($posts as $post): ?>
          <article class="article-card" data-cat="<?= htmlspecialchars($post['categoria_slug'] ?? 'sem-categoria') ?>">

            <a href="/blog/<?= htmlspecialchars($post['slug']) ?>" class="article-card-img" tabindex="-1" aria-hidden="true">
              <?php if ($post['imagem_capa']): ?>
                <img src="<?= htmlspecialchars($post['imagem_capa']) ?>"
                     alt="<?= htmlspecialchars($post['titulo']) ?>"
                     loading="lazy" width="800" height="400">
              <?php else: ?>
                <svg width="56" height="56" viewBox="0 0 56 56" fill="none" aria-hidden="true">
                  <rect x="8" y="6" width="40" height="44" rx="4" fill="#094a86" opacity="0.15"/>
                  <rect x="14" y="14" width="28" height="3" rx="1.5" fill="#094a86" opacity="0.5"/>
                  <rect x="14" y="22" width="22" height="3" rx="1.5" fill="#094a86" opacity="0.4"/>
                  <rect x="14" y="30" width="26" height="3" rx="1.5" fill="#094a86" opacity="0.3"/>
                  <rect x="14" y="38" width="18" height="3" rx="1.5" fill="#094a86" opacity="0.25"/>
                </svg>
              <?php endif; ?>
            </a>

            <div class="article-card-body">
              <?php if ($post['categoria_nome']): ?>
                <span class="article-cat"><?= htmlspecialchars($post['categoria_nome']) ?></span>
              <?php endif; ?>
              <h2><a href="/blog/<?= htmlspecialchars($post['slug']) ?>"><?= htmlspecialchars($post['titulo']) ?></a></h2>
              <?php if ($post['resumo']): ?>
                <p><?= htmlspecialchars($post['resumo']) ?></p>
              <?php endif; ?>
              <div class="article-meta">
                <span>Equipe TechSallus</span>
                <span class="article-meta-dot"></span>
                <span><?= $post['published_at'] ? date('d/m/Y', strtotime($post['published_at'])) : '' ?></span>
              </div>
              <a href="/blog/<?= htmlspecialchars($post['slug']) ?>" class="article-read-link">
                Ler artigo
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                  <path d="M2 7h10M8 3l4 4-4 4" stroke="#094a86" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <div id="no-results" class="no-results" style="display:none;text-align:center;padding:48px 0">
        Nenhum artigo encontrado nessa categoria.
      </div>

      <!-- Paginação (só quando busca ativa) -->
      <?php if ($busca && $totalPaginas > 1): ?>
        <nav class="blog-pagination" aria-label="Paginação">
          <?php if ($pagina > 1): ?>
            <a href="?pagina=<?= $pagina - 1 ?><?= $busca ? '&busca='.urlencode($busca) : '' ?>" class="page-btn">← Anterior</a>
          <?php endif; ?>
          <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <a href="?pagina=<?= $i ?><?= $busca ? '&busca='.urlencode($busca) : '' ?>"
               class="page-btn <?= $i === $pagina ? 'active' : '' ?>"><?= $i ?></a>
          <?php endfor; ?>
          <?php if ($pagina < $totalPaginas): ?>
            <a href="?pagina=<?= $pagina + 1 ?><?= $busca ? '&busca='.urlencode($busca) : '' ?>" class="page-btn">Próxima →</a>
          <?php endif; ?>
        </nav>
      <?php endif; ?>

    <?php endif; ?>
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
      <a href="/#demo" class="btn-primary">Quero uma demonstração gratuita</a>
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
      <span class="footer-copy">© <?= date('Y') ?> Techsallus. Todos os direitos reservados.</span>
      <a href="/admin/" class="footer-access">Acesso Restrito</a>
    </div>
  </div>
</footer>

<a href="<?= htmlspecialchars($whatsappUrl) ?>"
   target="_blank" rel="noopener noreferrer" class="whatsapp-float" aria-label="Fale conosco pelo WhatsApp">
  <svg viewBox="0 0 24 24" fill="white" width="26" height="26" aria-hidden="true">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
    <path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.974-1.304A9.963 9.963 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z" fill="none" stroke="white" stroke-width="1.5"/>
  </svg>
</a>

<?= getScripts('body') ?>
<?= getScripts('footer') ?>
<script src="/assets/js/main.js"></script>
<script src="/assets/js/blog.js"></script>
</body>
</html>
