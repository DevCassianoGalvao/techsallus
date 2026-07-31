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
$id        = (int)($_GET['id'] ?? 0);
$slug      = trim($_GET['slug'] ?? '');
$post      = null;
$saved     = false;
$saveError = '';

/* ── Carregar post existente ────────────────────────────────── */
if ($id) {
    try {
        $post = DB::fetchOne("SELECT * FROM posts WHERE id = ? LIMIT 1", [$id]);
        $slug = $post['slug'] ?? '';
    } catch (Exception $e) { /* sem DB ainda */ }
} elseif ($slug) {
    try {
        $post = DB::fetchOne("SELECT * FROM posts WHERE slug = ? LIMIT 1", [$slug]);
        $id = (int)($post['id'] ?? 0);
    } catch (Exception $e) { /* sem DB ainda */ }
}

/* ── Categorias ─────────────────────────────────────────────── */
$categorias = [];
try {
    $categorias = DB::fetchAll("SELECT id, nome FROM categorias ORDER BY nome");
} catch (Exception $e) {}

/* ── POST — salvar artigo ───────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrf()) {
        $saveError = 'Sessao expirada. Recarregue a pagina e tente novamente.';
    } else {
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
}

/* ── Página ─────────────────────────────────────────────────── */
$pageTitle = $post ? 'Editar: ' . ($post['titulo'] ?? '—') : 'Novo Artigo';
$activeNav = 'blog';
$extraHead = '<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet"><script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>';

$topbarExtra = '<div class="topbar-actions">';
$topbarExtra .= '<button type="button" id="btn-ai-open" class="btn-ai-generate">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
  </svg>
  Gerar com IA
</button>';
if ($post && $slug) {
    $previewUrl  = '/blog/preview.php?slug=' . urlencode($slug);
    $topbarExtra .= '<a href="' . htmlspecialchars($previewUrl) . '" target="_blank" class="btn-preview">
      <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
        <circle cx="12" cy="12" r="3"/>
      </svg>
      Pré-visualizar
    </a>';
}
$topbarExtra .= '</div>';

/* Lista de categorias para o modal */
$catNomes = array_column($categorias, 'nome');

include __DIR__ . '/_header.php';
?>

<div class="admin-card" style="max-width:900px">

  <?php if ($saved): ?>
    <div class="editor-alert-ok" style="margin-bottom:20px">✓ Artigo salvo com sucesso!</div>
  <?php endif; ?>
  <?php if ($saveError): ?>
    <div class="editor-alert-err" style="margin-bottom:20px"><?= htmlspecialchars($saveError) ?></div>
  <?php endif; ?>

  <div id="editor-msg" style="display:none;margin-bottom:20px;border-radius:8px;padding:10px 14px;color:#fff;font-size:13px;font-weight:600"></div>

  <form method="POST" class="editor-form-grid" id="form-artigo" enctype="multipart/form-data">
    <?= Security::csrfField() ?>
    <input type="hidden" name="id" value="<?= $post ? (int)$post['id'] : '' ?>"/>

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

    <div class="editor-field">
      <label class="editor-label" for="ed-nova-categoria">Nova categoria</label>
      <input class="editor-input" id="ed-nova-categoria" name="nova_categoria" type="text" maxlength="120"
             placeholder="Ex: Gestão hospitalar"/>
      <small class="editor-help">Preencha este campo para criar uma categoria nova ao salvar. Ela será aplicada neste artigo.</small>
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
      <label class="editor-label" for="campo-conteudo">Conteúdo (HTML)</label>
      <div id="quill-editor" class="quill-editor"></div>
      <textarea class="editor-textarea tall" id="campo-conteudo" name="conteudo"
                placeholder="<p>Conteúdo em HTML...</p>" style="display:none"><?= htmlspecialchars($post['conteudo'] ?? ($_POST['conteudo'] ?? '')) ?></textarea>
    </div>

    <div class="editor-field form-group">
      <label class="editor-label">Imagem de capa</label>
      <?php if (!empty($post['imagem_capa'])): ?>
        <div style="margin-bottom:10px">
          <img src="<?= htmlspecialchars($post['imagem_capa']) ?>"
               alt="Capa atual"
               style="max-height:140px;border-radius:10px;border:1px solid #e2eaf3;display:block">
          <span style="font-size:11px;color:#94a3b8;margin-top:4px;display:block">
            Imagem atual — envie uma nova para substituir
          </span>
        </div>
      <?php endif; ?>
      <input type="file" name="imagem_capa" id="imagem_capa"
             accept="image/jpeg,image/png,image/webp"
             style="display:block;font-size:13px">
      <small style="color:#94a3b8;font-size:11px;margin-top:4px;display:block">
        JPG, PNG ou WebP · Máx 5MB · Convertida automaticamente para WebP (1200px)
      </small>
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
      <label class="editor-label" for="ed-meta-title">Meta title (max 60)</label>
      <input class="editor-input" id="ed-meta-title" name="meta_title" type="text" maxlength="160"
             placeholder="Igual ao título se vazio"
             value="<?= htmlspecialchars($post['meta_title'] ?? ($_POST['meta_title'] ?? '')) ?>"/>
    </div>

    <div class="editor-field">
      <label class="editor-label" for="ed-meta-desc">Meta description (max 155)</label>
      <input class="editor-input" id="ed-meta-desc" name="meta_desc" type="text" maxlength="320"
             placeholder="Igual ao resumo se vazio"
             value="<?= htmlspecialchars($post['meta_desc'] ?? ($_POST['meta_desc'] ?? '')) ?>"/>
    </div>

    <div class="editor-actions full">
      <button type="submit" class="btn-save btn-salvar">Salvar artigo</button>
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

<!-- ══════════════════════════════════════════════
     MODAL — Gerador de Artigo por IA
     ══════════════════════════════════════════════ -->
<div id="ai-modal" class="ai-modal-overlay" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="ai-modal-title">
  <div class="ai-modal-box">
    <div class="ai-modal-header">
      <h2 class="ai-modal-title" id="ai-modal-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        Gerar Artigo com IA
      </h2>
      <button class="ai-modal-close" id="ai-modal-close" aria-label="Fechar">&#x2715;</button>
    </div>

    <div class="ai-modal-body">
      <div class="ai-field">
        <label class="ai-label" for="ai-tema">Tema do artigo <span class="ai-required">*</span></label>
        <input class="ai-input" id="ai-tema" type="text" maxlength="200"
               placeholder="Ex: Como reduzir glosas no faturamento hospitalar"/>
        <span class="ai-hint">Seja específico — quanto mais claro o tema, melhor o resultado.</span>
      </div>

      <div class="ai-field">
        <label class="ai-label" for="ai-categoria">Categoria</label>
        <select class="ai-select" id="ai-categoria">
          <option value="Gestão em Saúde">Gestão em Saúde</option>
          <?php foreach ($categorias as $cat): ?>
            <option value="<?= htmlspecialchars($cat['nome']) ?>"><?= htmlspecialchars($cat['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="ai-field">
        <label class="ai-label" for="ai-tom">Tom / estilo</label>
        <select class="ai-select" id="ai-tom">
          <option value="profissional e didático">Profissional e didático</option>
          <option value="técnico e aprofundado">Técnico e aprofundado</option>
          <option value="acessível para leigos">Acessível para leigos</option>
          <option value="persuasivo e orientado a decisão">Persuasivo e orientado a decisão</option>
        </select>
      </div>

      <div class="ai-field">
        <label class="ai-label" for="ai-pontos">Pontos obrigatórios a cobrir <span class="ai-optional">(opcional)</span></label>
        <textarea class="ai-textarea" id="ai-pontos" rows="3"
                  placeholder="Ex: diferença entre glosa técnica e administrativa&#10;impacto no fluxo de caixa&#10;como a TechSallus resolve"></textarea>
        <span class="ai-hint">Um ponto por linha. A IA vai cobrir cada um deles.</span>
      </div>

      <div id="ai-error" class="ai-error" hidden></div>
    </div>

    <div class="ai-modal-footer">
      <button type="button" class="ai-btn-cancel" id="ai-btn-cancel">Cancelar</button>
      <button type="button" class="ai-btn-generate" id="ai-btn-generate">
        <span class="ai-btn-label">Gerar artigo</span>
        <svg class="ai-btn-spinner" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
      </button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════
     MODAL — Confirmar substituição de conteúdo
     ══════════════════════════════════════════════ -->
<div id="ai-confirm-modal" class="ai-modal-overlay" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="ai-modal-box" style="max-width:480px">
    <div class="ai-modal-header">
      <h2 class="ai-modal-title">Substituir conteúdo atual?</h2>
    </div>
    <div class="ai-modal-body">
      <p style="color:var(--text-2);font-size:14px;line-height:1.6">
        O editor já tem conteúdo. Deseja substituir tudo pelo artigo gerado pela IA?<br>
        <strong>Esta ação não pode ser desfeita.</strong>
      </p>
    </div>
    <div class="ai-modal-footer">
      <button type="button" class="ai-btn-cancel" id="ai-confirm-cancel">Manter conteúdo atual</button>
      <button type="button" class="ai-btn-generate" id="ai-confirm-ok">Sim, substituir</button>
    </div>
  </div>
</div>

<style>
/* ── Botão "Gerar com IA" no topbar ──────────────────────────── */
.btn-ai-generate {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  height: 38px;
  padding: 0 18px;
  border-radius: 8px;
  border: 1.5px solid #7c3aed;
  background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
  color: #fff;
  font-family: var(--font-body);
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: opacity .15s, transform .1s;
}
.btn-ai-generate:hover { opacity: .9; transform: translateY(-1px); }
.btn-ai-generate:active { transform: translateY(0); opacity: 1; }

/* ── Modal overlay ───────────────────────────────────────────── */
.ai-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.55);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 20px;
  opacity: 0;
  pointer-events: none;
  transition: opacity .2s;
}
.ai-modal-overlay.open {
  opacity: 1;
  pointer-events: all;
}

/* ── Modal box ───────────────────────────────────────────────── */
.ai-modal-box {
  background: var(--bg-card, #fff);
  border-radius: 16px;
  width: 100%;
  max-width: 580px;
  box-shadow: 0 24px 64px rgba(0,0,0,.22);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  transform: translateY(16px);
  transition: transform .2s;
}
.ai-modal-overlay.open .ai-modal-box { transform: translateY(0); }

.ai-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 22px 24px 16px;
  border-bottom: 1px solid var(--border, #e8eaf0);
}
.ai-modal-title {
  display: flex;
  align-items: center;
  gap: 10px;
  font-family: var(--font-display);
  font-size: 18px;
  font-weight: 700;
  color: var(--text-1, #0f172a);
  margin: 0;
}
.ai-modal-title svg { color: #7c3aed; }

.ai-modal-close {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  color: var(--text-3, #94a3b8);
  line-height: 1;
  padding: 4px;
  border-radius: 6px;
  transition: background .15s, color .15s;
}
.ai-modal-close:hover { background: var(--bg-2, #f1f5f9); color: var(--text-1, #0f172a); }

.ai-modal-body { padding: 20px 24px; display: flex; flex-direction: column; gap: 16px; }

.ai-modal-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 10px;
  padding: 16px 24px;
  border-top: 1px solid var(--border, #e8eaf0);
  background: var(--bg-2, #f8fafc);
}

/* ── Campos do modal ─────────────────────────────────────────── */
.ai-field { display: flex; flex-direction: column; gap: 6px; }
.ai-label { font-size: 13px; font-weight: 600; color: var(--text-2, #475569); }
.ai-required { color: #e53e3e; }
.ai-optional { font-weight: 400; color: var(--text-3, #94a3b8); }
.ai-hint { font-size: 12px; color: var(--text-3, #94a3b8); line-height: 1.5; }

.ai-input, .ai-select, .ai-textarea {
  width: 100%;
  padding: 9px 12px;
  border: 1.5px solid var(--border, #e2e8f0);
  border-radius: 8px;
  font-family: var(--font-body);
  font-size: 14px;
  color: var(--text-1, #0f172a);
  background: var(--bg-card, #fff);
  transition: border-color .15s;
  box-sizing: border-box;
}
.ai-input:focus, .ai-select:focus, .ai-textarea:focus {
  outline: none;
  border-color: #7c3aed;
  box-shadow: 0 0 0 3px rgba(124,58,237,.12);
}
.ai-textarea { resize: vertical; min-height: 80px; }

/* ── Botões do modal ─────────────────────────────────────────── */
.ai-btn-cancel {
  height: 38px;
  padding: 0 18px;
  border-radius: 8px;
  border: 1.5px solid var(--border, #e2e8f0);
  background: var(--bg-card, #fff);
  color: var(--text-2, #475569);
  font-family: var(--font-body);
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background .15s;
}
.ai-btn-cancel:hover { background: var(--bg-2, #f1f5f9); }

.ai-btn-generate {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  height: 38px;
  padding: 0 22px;
  border-radius: 8px;
  border: none;
  background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
  color: #fff;
  font-family: var(--font-body);
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: opacity .15s;
}
.ai-btn-generate:hover:not(:disabled) { opacity: .9; }
.ai-btn-generate:disabled { opacity: .6; cursor: not-allowed; }

/* ── Spinner ─────────────────────────────────────────────────── */
.ai-btn-spinner { display: none; }
.ai-btn-generate.loading .ai-btn-spinner {
  display: block;
  animation: ai-spin .7s linear infinite;
}
.ai-btn-generate.loading .ai-btn-label::after { content: '...'; }
@keyframes ai-spin { to { transform: rotate(360deg); } }

/* ── Error box ───────────────────────────────────────────────── */
.ai-error {
  background: #fff0f0;
  border: 1.5px solid #fca5a5;
  border-radius: 8px;
  padding: 10px 14px;
  color: #b91c1c;
  font-size: 13px;
  line-height: 1.5;
}
</style>

<script>
(function () {
  'use strict';

  /* ── Referências ────────────────────────────────────────────── */
  const overlay       = document.getElementById('ai-modal');
  const confirmOvl    = document.getElementById('ai-confirm-modal');
  const btnOpen       = document.getElementById('btn-ai-open');
  const btnClose      = document.getElementById('ai-modal-close');
  const btnCancel     = document.getElementById('ai-btn-cancel');
  const btnGenerate   = document.getElementById('ai-btn-generate');
  const btnConfirmOk  = document.getElementById('ai-confirm-ok');
  const btnConfirmNo  = document.getElementById('ai-confirm-cancel');
  const aiError       = document.getElementById('ai-error');

  /* Form inputs */
  const inTema      = document.getElementById('ai-tema');
  const inCat       = document.getElementById('ai-categoria');
  const inTom       = document.getElementById('ai-tom');
  const inPontos    = document.getElementById('ai-pontos');

  /* Editor fields */
  const fTitulo    = document.getElementById('ed-titulo');
  const fSlug      = document.getElementById('ed-slug');
  const fResumo    = document.getElementById('ed-resumo');
  const fConteudo  = document.getElementById('campo-conteudo');
  const fMetaTitle = document.getElementById('ed-meta-title');
  const fMetaDesc  = document.getElementById('ed-meta-desc');
  let quill = null;

  if (window.Quill && fConteudo && document.getElementById('quill-editor')) {
    quill = new Quill('#quill-editor', {
      theme: 'snow',
      modules: {
        toolbar: {
          container: [
            [{ header: [2, 3, false] }],
            ['bold', 'italic', 'underline'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['blockquote', 'link', 'image'],
            ['clean']
          ],
          handlers: {
            image: function () {
              const input = document.createElement('input');
              input.type = 'file';
              input.accept = 'image/jpeg,image/png,image/webp';
              input.onchange = function () {
                const file = input.files && input.files[0];
                if (!file) return;
                if (file.size > 3 * 1024 * 1024) {
                  mostrarMsg('Imagem interna maior que 3MB.', 'error');
                  return;
                }
                const reader = new FileReader();
                reader.onload = function () {
                  const range = quill.getSelection(true);
                  quill.insertEmbed(range.index, 'image', reader.result, 'user');
                  quill.setSelection(range.index + 1);
                };
                reader.readAsDataURL(file);
              };
              input.click();
            }
          }
        }
      }
    });
    quill.root.innerHTML = fConteudo.value || '';
  }

  /* Resultado temporário (aguardando confirmação de substituição) */
  let pendingResult = null;

  /* ── Abrir / fechar modal ───────────────────────────────────── */
  function openModal()  { overlay.classList.add('open'); overlay.removeAttribute('aria-hidden'); inTema.focus(); }
  function closeModal() { overlay.classList.remove('open'); overlay.setAttribute('aria-hidden', 'true'); aiError.hidden = true; }
  function openConfirm()  { confirmOvl.classList.add('open'); confirmOvl.removeAttribute('aria-hidden'); }
  function closeConfirm() { confirmOvl.classList.remove('open'); confirmOvl.setAttribute('aria-hidden', 'true'); }

  if (btnOpen)       btnOpen.addEventListener('click', openModal);
  if (btnClose)      btnClose.addEventListener('click', closeModal);
  if (btnCancel)     btnCancel.addEventListener('click', closeModal);
  if (btnConfirmNo)  btnConfirmNo.addEventListener('click', closeConfirm);

  /* Fechar clicando fora do box */
  overlay.addEventListener('click', function (e) { if (e.target === overlay) closeModal(); });
  confirmOvl.addEventListener('click', function (e) { if (e.target === confirmOvl) closeConfirm(); });

  /* Fechar com ESC */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { closeModal(); closeConfirm(); }
  });

  /* ── Aplicar resultado no editor ───────────────────────────── */
  function applyResult(r) {
    if (fTitulo)    fTitulo.value    = r.titulo    || '';
    if (fSlug)      fSlug.value      = r.slug      || '';
    if (fResumo)    fResumo.value    = r.resumo    || '';
    if (fConteudo)  fConteudo.value  = r.conteudo  || '';
    if (quill)      quill.root.innerHTML = r.conteudo || '';
    if (fMetaTitle) fMetaTitle.value = r.meta_title || '';
    if (fMetaDesc)  fMetaDesc.value  = r.meta_desc  || '';
    pendingResult = null;
  }

  /* ── Confirmar substituição ─────────────────────────────────── */
  if (btnConfirmOk) {
    btnConfirmOk.addEventListener('click', function () {
      if (pendingResult) applyResult(pendingResult);
      closeConfirm();
      closeModal();
    });
  }

  /* ── Gerar artigo ───────────────────────────────────────────── */
  if (btnGenerate) {
    btnGenerate.addEventListener('click', async function () {
      const tema = inTema.value.trim();
      if (!tema) { inTema.focus(); inTema.style.borderColor = '#e53e3e'; return; }
      inTema.style.borderColor = '';

      aiError.hidden = true;
      btnGenerate.disabled = true;
      btnGenerate.classList.add('loading');
      btnGenerate.querySelector('.ai-btn-label').textContent = 'Gerando';

      try {
        const fd = new FormData();
        fd.append('tema',      tema);
        fd.append('tom',       inTom.value);
        fd.append('pontos',    inPontos.value.trim());
        fd.append('categoria', inCat.value);
        const csrf = document.querySelector('meta[name="csrf-token"]');
        if (csrf) fd.append('csrf_token', csrf.content);

        const res  = await fetch('/api/ai-article.php', { method: 'POST', body: fd });
        const data = await res.json();

        if (!res.ok || data.error) {
          throw new Error(data.error || 'Erro desconhecido da API');
        }

        /* Verifica se editor já tem conteúdo */
        const hasContent = fConteudo && fConteudo.value.trim().length > 0;
        if (hasContent) {
          pendingResult = data;
          openConfirm();
        } else {
          applyResult(data);
          closeModal();
        }

      } catch (err) {
        aiError.textContent = '⚠ ' + err.message;
        aiError.hidden = false;
      } finally {
        btnGenerate.disabled = false;
        btnGenerate.classList.remove('loading');
        btnGenerate.querySelector('.ai-btn-label').textContent = 'Gerar artigo';
      }
    });
  }

  /* ── Auto-slug a partir do título ───────────────────────────── */
  if (fTitulo && fSlug) {
    fTitulo.addEventListener('input', function () {
      if (fSlug.dataset.edited) return; /* respeita edição manual */
      fSlug.value = fTitulo.value
        .toLowerCase()
        .normalize('NFD').replace(/[̀-ͯ]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-');
    });
    fSlug.addEventListener('input', function () {
      fSlug.dataset.edited = '1';
    });
  }

  async function salvarArtigo() {
    const campoConteudo = document.getElementById('campo-conteudo');
    if (typeof quill !== 'undefined' && quill && campoConteudo) {
      campoConteudo.value = quill.root.innerHTML;
    }

    const form = document.getElementById('form-artigo');
    const fd = new FormData(form);
    const btn = document.querySelector('.btn-salvar');
    const orig = btn.textContent;

    btn.disabled = true;
    btn.textContent = 'Salvando...';

    try {
      const res = await fetch('/api/posts.php', { method: 'POST', body: fd });

      const text = await res.text();
      let data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        console.error('Resposta inválida do servidor:', text);
        mostrarMsg('Erro: resposta inválida do servidor. Verifique o console.', 'error');
        btn.disabled = false;
        btn.textContent = orig;
        return;
      }

      if (data.ok) {
        if (data.id) {
          const idInput = document.querySelector('[name="id"]');
          if (idInput && !idInput.value) {
            idInput.value = data.id;
            window.history.replaceState({}, '', `/admin/blog-editor.php?id=${data.id}`);
          }
        }
        mostrarMsg('✓ Artigo salvo com sucesso!', 'success');
      } else {
        mostrarMsg('Erro: ' + (data.erro || 'Tente novamente.'), 'error');
      }

    } catch (err) {
      console.error('Erro ao salvar:', err);
      mostrarMsg('Erro de conexão. Verifique o console.', 'error');
    }

    btn.disabled = false;
    btn.textContent = orig;
  }

  function mostrarMsg(texto, tipo) {
    const el = document.getElementById('editor-msg');
    el.textContent = texto;
    el.style.display = 'block';
    el.style.background = tipo === 'success' ? '#27500a' : '#a32d2d';
    clearTimeout(window._msgTimer);
    window._msgTimer = setTimeout(() => { el.style.display = 'none'; }, 5000);
  }

  const formArtigo = document.getElementById('form-artigo');
  if (formArtigo) {
    formArtigo.addEventListener('submit', function (e) {
      e.preventDefault();
      salvarArtigo();
    });
  }

})();
</script>

<?php include __DIR__ . '/_footer.php'; ?>
