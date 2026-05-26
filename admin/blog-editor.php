<?php
/* ─────────────────────────────────────────────────────────────
   admin/blog-editor.php — Editor de artigos do blog
   ───────────────────────────────────────────────────────────── */
define('ADMIN_PAGE', true);

$rootDir = dirname(__DIR__);
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Auth.php';
Env::load($rootDir . '/.env');
Auth::require();

$adminUser = Auth::user();
$slug      = trim($_GET['slug'] ?? '');
$post      = null;
$saved     = false;
$saveError = '';

/* ── Carregar post existente ────────────────────────────────── */
if ($slug) {
    try {
        $post = DB::fetchOne("SELECT * FROM posts WHERE slug = ? LIMIT 1", [$slug]);
    } catch (Exception $e) { /* sem DB ainda */ }
}

/* ── Categorias ─────────────────────────────────────────────── */
$categorias = [];
try {
    $categorias = DB::fetchAll("SELECT id, nome FROM categorias ORDER BY nome");
} catch (Exception $e) {}

/* ── POST — salvar artigo ───────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo    = trim($_POST['titulo']       ?? '');
    $postSlug  = trim($_POST['slug']         ?? '');
    $catId     = (int)($_POST['categoria_id'] ?? 0) ?: null;
    $tags      = trim($_POST['tags']         ?? '');
    $resumo    = trim($_POST['resumo']       ?? '');
    $conteudo  = trim($_POST['conteudo']     ?? '');
    $metaTitle = trim($_POST['meta_title']   ?? '');
    $metaDesc  = trim($_POST['meta_desc']    ?? '');
    $status    = in_array($_POST['status'] ?? '', ['rascunho', 'publicado'])
                 ? $_POST['status'] : 'rascunho';

    if (!$titulo || !$postSlug) {
        $saveError = 'Título e slug são obrigatórios.';
    } else {
        try {
            if ($post) {
                /* Definir published_at apenas na primeira publicação */
                $pubAt = $post['published_at'];
                if ($status === 'publicado' && !$pubAt) {
                    $pubAt = date('Y-m-d H:i:s');
                }
                DB::query(
                    "UPDATE posts SET titulo=?, slug=?, categoria_id=?, tags=?, resumo=?, conteudo=?,
                     meta_title=?, meta_desc=?, status=?, published_at=?, atualizado_em=NOW()
                     WHERE id=?",
                    [$titulo, $postSlug, $catId, $tags, $resumo, $conteudo,
                     $metaTitle, $metaDesc, $status, $pubAt, $post['id']]
                );
            } else {
                $pubAt = $status === 'publicado' ? date('Y-m-d H:i:s') : null;
                DB::insert(
                    "INSERT INTO posts (titulo, slug, categoria_id, tags, resumo, conteudo,
                     meta_title, meta_desc, status, published_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [$titulo, $postSlug, $catId, $tags, $resumo, $conteudo,
                     $metaTitle, $metaDesc, $status, $pubAt]
                );
            }
            $saved    = true;
            $slug     = $postSlug;
            $post     = DB::fetchOne("SELECT * FROM posts WHERE slug = ? LIMIT 1", [$postSlug]);
        } catch (Exception $e) {
            $saveError = 'Erro ao salvar: ' . $e->getMessage();
        }
    }
}

/* ── Página ─────────────────────────────────────────────────── */
$pageTitle = $post ? 'Editar: ' . ($post['titulo'] ?? '—') : 'Novo Artigo';
$activeNav = 'blog';

/* Botão prévia no topbar */
$topbarExtra = '';
if ($post && $slug) {
    $previewUrl  = '/blog/preview.php?slug=' . urlencode($slug);
    $topbarExtra = '<div class="topbar-actions">
      <a href="' . htmlspecialchars($previewUrl) . '" target="_blank" class="btn-preview">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
          <circle cx="12" cy="12" r="3"/>
        </svg>
        Pré-visualizar
      </a>
    </div>';
}

include __DIR__ . '/_header.php';
?>

<div class="admin-card" style="max-width:900px">

  <?php if ($saved): ?>
    <div class="editor-alert-ok" style="margin-bottom:20px">✓ Artigo salvo com sucesso!</div>
  <?php endif; ?>
  <?php if ($saveError): ?>
    <div class="editor-alert-err" style="margin-bottom:20px"><?= htmlspecialchars($saveError) ?></div>
  <?php endif; ?>

  <form method="POST" class="editor-form-grid">

    <div class="editor-field full">
      <label class="editor-label" for="ed-titulo">Título</label>
      <input class="editor-input" id="ed-titulo" name="titulo" type="text" required maxlength="250"
             placeholder="Título do artigo"
             value="<?= htmlspecialchars($post['titulo'] ?? ($_POST['titulo'] ?? '')) ?>"/>
    </div>

    <div class="editor-field">
      <label class="editor-label" for="ed-slug">Slug (URL)</label>
      <input class="editor-input" id="ed-slug" name="slug" type="text" required maxlength="250"
             placeholder="meu-artigo-aqui"
             value="<?= htmlspecialchars($post['slug'] ?? ($_POST['slug'] ?? '')) ?>"/>
    </div>

    <div class="editor-field">
      <label class="editor-label" for="ed-categoria">Categoria</label>
      <select class="editor-select" id="ed-categoria" name="categoria_id">
        <option value="">— Sem categoria —</option>
        <?php foreach ($categorias as $cat): ?>
          <option value="<?= (int)$cat['id'] ?>"
            <?= (int)($post['categoria_id'] ?? ($_POST['categoria_id'] ?? 0)) === (int)$cat['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['nome']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="editor-field full">
      <label class="editor-label" for="ed-tags">Tags (separadas por vírgula)</label>
      <input class="editor-input" id="ed-tags" name="tags" type="text" maxlength="500"
             placeholder="gestão, tecnologia, faturamento"
             value="<?= htmlspecialchars($post['tags'] ?? ($_POST['tags'] ?? '')) ?>"/>
    </div>

    <div class="editor-field full">
      <label class="editor-label" for="ed-resumo">Resumo / Lead</label>
      <textarea class="editor-textarea" id="ed-resumo" name="resumo" rows="3"
                placeholder="Breve descrição exibida na listagem e como meta description padrão."><?= htmlspecialchars($post['resumo'] ?? ($_POST['resumo'] ?? '')) ?></textarea>
    </div>

    <div class="editor-field full">
      <label class="editor-label" for="ed-conteudo">Conteúdo (HTML)</label>
      <textarea class="editor-textarea tall" id="ed-conteudo" name="conteudo"
                placeholder="<p>Conteúdo em HTML...</p>"><?= htmlspecialchars($post['conteudo'] ?? ($_POST['conteudo'] ?? '')) ?></textarea>
    </div>

    <div class="editor-field">
      <label class="editor-label" for="ed-imagem">Imagem de capa (URL)</label>
      <input class="editor-input" id="ed-imagem" name="imagem_capa" type="text" maxlength="300"
             placeholder="https://..."
             value="<?= htmlspecialchars($post['imagem_capa'] ?? ($_POST['imagem_capa'] ?? '')) ?>"/>
    </div>

    <div class="editor-field">
      <label class="editor-label" for="ed-status">Status</label>
      <select class="editor-select" id="ed-status" name="status">
        <option value="rascunho" <?= ($post['status'] ?? 'rascunho') === 'rascunho' ? 'selected' : '' ?>>Rascunho</option>
        <option value="publicado" <?= ($post['status'] ?? '') === 'publicado' ? 'selected' : '' ?>>Publicado</option>
      </select>
    </div>

    <div class="editor-field full" style="border-top:1px solid var(--border);padding-top:16px;margin-top:4px">
      <label class="editor-label" style="margin-bottom:12px">SEO</label>
    </div>

    <div class="editor-field">
      <label class="editor-label" for="ed-meta-title">Meta title (max 160)</label>
      <input class="editor-input" id="ed-meta-title" name="meta_title" type="text" maxlength="160"
             placeholder="Igual ao título se vazio"
             value="<?= htmlspecialchars($post['meta_title'] ?? ($_POST['meta_title'] ?? '')) ?>"/>
    </div>

    <div class="editor-field">
      <label class="editor-label" for="ed-meta-desc">Meta description (max 320)</label>
      <input class="editor-input" id="ed-meta-desc" name="meta_desc" type="text" maxlength="320"
             placeholder="Igual ao resumo se vazio"
             value="<?= htmlspecialchars($post['meta_desc'] ?? ($_POST['meta_desc'] ?? '')) ?>"/>
    </div>

    <div class="editor-actions full">
      <button type="submit" class="btn-save">Salvar artigo</button>
      <?php if ($post && $slug): ?>
        <a href="/blog/preview.php?slug=<?= urlencode($slug) ?>" target="_blank" class="btn-preview">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
          Pré-visualizar
        </a>
        <?php if ($post['status'] === 'publicado'): ?>
          <a href="/blog/<?= urlencode($slug) ?>" target="_blank" class="filter-clear" style="color:var(--blue)">
            Ver no blog →
          </a>
        <?php endif; ?>
      <?php endif; ?>
    </div>

  </form>
</div>

<?php include __DIR__ . '/_footer.php'; ?>
