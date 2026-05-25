<?php
/* ─────────────────────────────────────────────────────────────
   admin/crm.php — CRM Kanban board
   ───────────────────────────────────────────────────────────── */
define('ADMIN_PAGE', true);

$rootDir = dirname(__DIR__);
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Auth.php';
Env::load($rootDir . '/.env');
Auth::require();

$adminUser = Auth::user();
$pageTitle = 'CRM · Leads';
$activeNav = 'crm';

/* ── Load leads ─────────────────────────────────────────────── */
$dbOk   = false;
$grouped = [
    'novo'       => [],
    'contato'    => [],
    'proposta'   => [],
    'fechamento' => [],
];

try {
    $leads = DB::fetchAll(
        "SELECT id, nome, instituicao, cargo, email, whatsapp, porte, status,
                utm_source, utm_medium, utm_campaign, criado_em
         FROM leads ORDER BY criado_em DESC"
    );
    foreach ($leads as $lead) {
        $s = $lead['status'];
        if (isset($grouped[$s])) $grouped[$s][] = $lead;
    }
    $dbOk = true;
} catch (Exception $e) {
    $dbError = $e->getMessage();
}

$colMeta = [
    'novo'       => ['label' => 'Novo',       'class' => 'col-novo'],
    'contato'    => ['label' => 'Contato',     'class' => 'col-contato'],
    'proposta'   => ['label' => 'Proposta',    'class' => 'col-proposta'],
    'fechamento' => ['label' => 'Fechamento',  'class' => 'col-fechamento'],
];

/* SortableJS from CDN loaded before admin.js */
$extraScripts = '<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>';

include __DIR__ . '/_header.php';
?>

<?php if (!$dbOk): ?>
  <div class="admin-empty-state">
    <div class="admin-empty-icon">⚠️</div>
    <h3>Banco de dados não conectado</h3>
    <p>Execute o <a href="/install.php?token=techsallus-install-2026">instalador</a> para configurar as tabelas.</p>
    <?php if (!empty($dbError)): ?>
      <pre class="admin-error-detail"><?= htmlspecialchars($dbError) ?></pre>
    <?php endif; ?>
  </div>

<?php else: ?>

  <div class="kanban-board">
    <?php foreach ($colMeta as $status => $meta): ?>
      <div class="kanban-col <?= $meta['class'] ?>" data-status="<?= $status ?>">
        <div class="kanban-col-header">
          <span class="col-title"><?= $meta['label'] ?></span>
          <span class="col-count"><?= count($grouped[$status]) ?></span>
        </div>
        <div class="kanban-col-body" id="col-<?= $status ?>">
          <?php foreach ($grouped[$status] as $lead): ?>
            <?php
              $utm = trim(implode(' · ', array_filter([
                  $lead['utm_source'],
                  $lead['utm_medium'],
                  $lead['utm_campaign'],
              ])));
            ?>
            <div class="kanban-card"
                 data-id="<?= (int)$lead['id'] ?>"
                 data-nome="<?= htmlspecialchars($lead['nome'], ENT_QUOTES) ?>"
                 data-inst="<?= htmlspecialchars($lead['instituicao'], ENT_QUOTES) ?>"
                 data-cargo="<?= htmlspecialchars($lead['cargo'], ENT_QUOTES) ?>"
                 data-email="<?= htmlspecialchars($lead['email'], ENT_QUOTES) ?>"
                 data-wa="<?= htmlspecialchars($lead['whatsapp'], ENT_QUOTES) ?>"
                 data-porte="<?= htmlspecialchars($lead['porte'], ENT_QUOTES) ?>"
                 data-status="<?= htmlspecialchars($lead['status'], ENT_QUOTES) ?>"
                 data-data="<?= date('d/m/Y', strtotime($lead['criado_em'])) ?>"
                 data-utm="<?= htmlspecialchars($utm, ENT_QUOTES) ?>">
              <div class="kanban-card-name"><?= htmlspecialchars($lead['nome']) ?></div>
              <div class="kanban-card-inst"><?= htmlspecialchars($lead['instituicao']) ?></div>
              <div class="kanban-card-meta">
                <span class="kanban-card-porte"><?= htmlspecialchars($lead['porte']) ?></span>
                <span class="kanban-card-date"><?= date('d/m/Y', strtotime($lead['criado_em'])) ?></span>
              </div>
              <button type="button" class="kanban-card-btn">Ver detalhes</button>
            </div>
          <?php endforeach; ?>

          <?php if (empty($grouped[$status])): ?>
            <div class="kanban-empty">Nenhum lead</div>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

<?php endif; ?>

<!-- Lead Modal -->
<div class="modal-overlay" id="lead-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title-text">
  <div class="modal-box">
    <div class="modal-header">
      <div>
        <div class="modal-title" id="modal-title-text">—</div>
        <div class="modal-sub" id="modal-sub-text">—</div>
      </div>
      <button type="button" class="modal-close" id="modal-close" aria-label="Fechar">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div class="modal-tabs">
      <button type="button" class="modal-tab active" data-tab="detalhes">Detalhes</button>
      <button type="button" class="modal-tab" data-tab="notas">Notas</button>
    </div>

    <div class="modal-body">
      <!-- Detalhes tab -->
      <div class="modal-tab-pane active" data-tab="detalhes">
        <div class="modal-detail-grid">
          <div class="modal-detail-item">
            <label>Cargo</label>
            <span id="modal-cargo">—</span>
          </div>
          <div class="modal-detail-item">
            <label>Porte</label>
            <span id="modal-porte">—</span>
          </div>
          <div class="modal-detail-item">
            <label>E-mail</label>
            <span id="modal-email">—</span>
          </div>
          <div class="modal-detail-item">
            <label>WhatsApp</label>
            <span id="modal-whatsapp">—</span>
          </div>
          <div class="modal-detail-item">
            <label>Status</label>
            <span id="modal-status">—</span>
          </div>
          <div class="modal-detail-item">
            <label>Cadastrado em</label>
            <span id="modal-data">—</span>
          </div>
          <div class="modal-detail-item modal-detail-full" id="modal-utm-row" style="display:none">
            <label>UTM</label>
            <span id="modal-utm">—</span>
          </div>
        </div>
      </div>

      <!-- Notas tab -->
      <div class="modal-tab-pane" data-tab="notas">
        <div class="notes-list" id="notes-list"></div>
        <form id="note-form" class="note-add">
          <textarea class="note-input" id="note-input" placeholder="Adicionar nota…" rows="2"></textarea>
          <button type="submit" class="note-submit">Salvar</button>
        </form>
      </div>
    </div><!-- /.modal-body -->
  </div><!-- /.modal-box -->
</div><!-- /.modal-overlay -->

<?php include __DIR__ . '/_footer.php'; ?>
