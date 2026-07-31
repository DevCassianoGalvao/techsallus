<?php
define('ADMIN_PAGE', true);

require_once __DIR__ . '/../core/Env.php';
require_once __DIR__ . '/../core/DB.php';
require_once __DIR__ . '/../core/Auth.php';

Env::load(__DIR__ . '/../.env');
Auth::start();
Auth::require();

$pageTitle = 'Blog';
$activeNav = 'blog';
$adminUser = Auth::user();

$busca = trim($_GET['busca'] ?? '');
$status = trim($_GET['status'] ?? '');

$where = ['1=1'];
$params = [];

if ($busca !== '') {
    $term = "%{$busca}%";
    $where[] = '(p.titulo LIKE ? OR p.slug LIKE ? OR p.resumo LIKE ? OR c.nome LIKE ?)';
    $params = array_merge($params, [$term, $term, $term, $term]);
}

if (in_array($status, ['rascunho', 'publicado'], true)) {
    $where[] = 'p.status = ?';
    $params[] = $status;
}

$whereSQL = implode(' AND ', $where);

$posts = DB::fetchAll(
    "SELECT p.id, p.titulo, p.slug, p.resumo, p.status, p.imagem_capa,
            p.published_at, p.criado_em, p.atualizado_em,
            c.nome AS categoria_nome
     FROM posts p
     LEFT JOIN categorias c ON c.id = p.categoria_id
     WHERE {$whereSQL}
     ORDER BY COALESCE(p.published_at, p.criado_em) DESC, p.id DESC",
    $params
);

$totais = DB::fetchOne(
    "SELECT
        COUNT(*) AS total,
        SUM(status = 'publicado') AS publicados,
        SUM(status = 'rascunho') AS rascunhos
     FROM posts"
) ?: ['total' => 0, 'publicados' => 0, 'rascunhos' => 0];

$topbarExtra = '<a href="/admin/blog-editor.php" class="btn-orange" style="font-size:14px;height:40px;padding:0 20px">+ Novo artigo</a>';
$extraScripts = <<<'HTML'
<script>
function excluirPost(id, btn) {
  if (!confirm('Excluir este artigo? Esta acao nao pode ser desfeita.')) return;

  const fd = new FormData();
  fd.append('action', 'excluir');
  fd.append('id', id);
  const csrf = document.querySelector('meta[name="csrf-token"]');
  if (csrf) fd.append('csrf_token', csrf.content);

  const original = btn.textContent;
  btn.disabled = true;
  btn.textContent = 'Excluindo...';

  fetch('/api/posts.php', { method: 'POST', body: fd })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (!data.ok) throw new Error(data.erro || 'Erro ao excluir.');
      const row = btn.closest('tr');
      if (row) {
        row.style.transition = 'opacity .2s, transform .2s';
        row.style.opacity = '0';
        row.style.transform = 'translateX(8px)';
        setTimeout(function () { window.location.reload(); }, 220);
      } else {
        window.location.reload();
      }
    })
    .catch(function (err) {
      alert(err.message || 'Erro ao excluir artigo.');
      btn.disabled = false;
      btn.textContent = original;
    });
}
</script>
HTML;

require_once __DIR__ . '/_header.php';
?>

<div class="stats-grid blog-stats-grid">
  <div class="stat-card-admin">
    <div class="stat-num-admin"><?= (int)$totais['total'] ?></div>
    <div class="stat-label-admin">Artigos no banco</div>
  </div>
  <div class="stat-card-admin">
    <div class="stat-num-admin"><?= (int)$totais['publicados'] ?></div>
    <div class="stat-label-admin">Publicados</div>
  </div>
  <div class="stat-card-admin">
    <div class="stat-num-admin"><?= (int)$totais['rascunhos'] ?></div>
    <div class="stat-label-admin">Rascunhos</div>
  </div>
  <div class="stat-card-admin">
    <div class="stat-num-admin"><?= count($posts) ?></div>
    <div class="stat-label-admin">Resultado atual</div>
  </div>
</div>

<div class="crm-filters">
  <form method="GET" class="filter-form">
    <input
      class="filter-input"
      type="text"
      name="busca"
      value="<?= htmlspecialchars($busca) ?>"
      placeholder="Buscar por titulo, slug, resumo ou categoria..."
    >
    <select name="status" class="filter-select">
      <option value="">Todos os status</option>
      <option value="publicado" <?= $status === 'publicado' ? 'selected' : '' ?>>Publicados</option>
      <option value="rascunho" <?= $status === 'rascunho' ? 'selected' : '' ?>>Rascunhos</option>
    </select>
    <button type="submit" class="filter-btn">Filtrar</button>
    <?php if ($busca !== '' || $status !== ''): ?>
      <a href="/admin/blog-posts.php" class="filter-clear">Limpar</a>
    <?php endif; ?>
  </form>
</div>

<div class="admin-card">
  <?php if (empty($posts)): ?>
    <div class="admin-empty-state">
      <h3>Nenhum artigo encontrado</h3>
      <p>Crie um novo artigo ou limpe os filtros para ver todos.</p>
      <p style="margin-top:16px"><a href="/admin/blog-editor.php">Criar artigo</a></p>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="leads-table blog-admin-table">
        <thead>
          <tr>
            <th>Artigo</th>
            <th>Categoria</th>
            <th>Status</th>
            <th>Publicado</th>
            <th>Atualizado</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $post): ?>
            <?php
              $isPublished = $post['status'] === 'publicado';
              $publishedAt = $post['published_at'] ? date('d/m/Y H:i', strtotime($post['published_at'])) : '-';
              $updatedAt = $post['atualizado_em'] ? date('d/m/Y H:i', strtotime($post['atualizado_em'])) : '-';
            ?>
            <tr>
              <td>
                <div class="blog-post-cell">
                  <div class="blog-post-thumb">
                    <?php if (!empty($post['imagem_capa'])): ?>
                      <img src="<?= htmlspecialchars($post['imagem_capa']) ?>" alt="">
                    <?php else: ?>
                      <span>TS</span>
                    <?php endif; ?>
                  </div>
                  <div class="blog-post-info">
                    <strong><?= htmlspecialchars($post['titulo']) ?></strong>
                    <small>/blog/<?= htmlspecialchars($post['slug']) ?></small>
                  </div>
                </div>
              </td>
              <td><?= htmlspecialchars($post['categoria_nome'] ?? 'Sem categoria') ?></td>
              <td>
                <span class="badge badge-<?= htmlspecialchars($post['status']) ?>">
                  <?= $isPublished ? 'Publicado' : 'Rascunho' ?>
                </span>
              </td>
              <td><?= htmlspecialchars($publishedAt) ?></td>
              <td><?= htmlspecialchars($updatedAt) ?></td>
              <td>
                <div class="blog-row-actions">
                  <a href="/admin/blog-editor.php?id=<?= (int)$post['id'] ?>" class="btn-mini btn-mini-primary">Editar</a>
                  <a href="/blog/preview.php?slug=<?= urlencode($post['slug']) ?>" target="_blank" class="btn-mini">Preview</a>
                  <?php if ($isPublished): ?>
                    <a href="/blog/<?= urlencode($post['slug']) ?>" target="_blank" class="btn-mini">Ver</a>
                  <?php endif; ?>
                  <button type="button" class="btn-mini btn-mini-danger" onclick="excluirPost(<?= (int)$post['id'] ?>, this)">Excluir</button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/_footer.php'; ?>
