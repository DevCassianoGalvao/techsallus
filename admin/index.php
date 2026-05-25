<?php
/* ─────────────────────────────────────────────────────────────
   admin/index.php — Dashboard
   ───────────────────────────────────────────────────────────── */
define('ADMIN_PAGE', true);

$rootDir = dirname(__DIR__);
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Auth.php';
Env::load($rootDir . '/.env');
Auth::require();

$adminUser = Auth::user();
$pageTitle = 'Dashboard';
$activeNav = 'dashboard';

/* ── Stats ─────────────────────────────────────────────────── */
$dbOk        = false;
$totalLeads  = 0;
$byStatus    = [];
$byPorte     = [];
$recentLeads = [];
$statusMap   = [
    'novo'        => ['label' => 'Novo',        'n' => 0],
    'contato'     => ['label' => 'Contato',     'n' => 0],
    'proposta'    => ['label' => 'Proposta',     'n' => 0],
    'fechamento'  => ['label' => 'Fechamento',   'n' => 0],
];

try {
    $totalLeads = (int)(DB::fetchOne("SELECT COUNT(*) AS n FROM leads")['n'] ?? 0);

    $rows = DB::fetchAll("SELECT status, COUNT(*) AS n FROM leads GROUP BY status");
    foreach ($rows as $r) {
        if (isset($statusMap[$r['status']])) {
            $statusMap[$r['status']]['n'] = (int)$r['n'];
        }
    }

    $byPorte = DB::fetchAll(
        "SELECT porte, COUNT(*) AS n FROM leads GROUP BY porte ORDER BY n DESC LIMIT 8"
    );

    $recentLeads = DB::fetchAll(
        "SELECT id, nome, instituicao, porte, status, criado_em FROM leads ORDER BY criado_em DESC LIMIT 8"
    );

    $dbOk = true;
} catch (Exception $e) {
    $dbError = $e->getMessage();
}

$emAndamento = $statusMap['contato']['n'] + $statusMap['proposta']['n'];

/* ── Pipeline bar width helper ────────────────────────────── */
function barWidth(int $n, int $total): int {
    return $total > 0 ? (int)round($n / $total * 100) : 0;
}

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

  <!-- Stats grid -->
  <div class="stats-grid">
    <div class="stat-card-admin">
      <div class="stat-icon" style="background:#eff6ff">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div class="stat-num-admin"><?= $totalLeads ?></div>
      <div class="stat-label-admin">Total de leads</div>
    </div>

    <div class="stat-card-admin">
      <div class="stat-icon" style="background:#f1f5f9">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#64748b" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>
      <div class="stat-num-admin"><?= $statusMap['novo']['n'] ?></div>
      <div class="stat-label-admin">Aguardando contato</div>
    </div>

    <div class="stat-card-admin">
      <div class="stat-icon" style="background:#fffbeb">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#d97706" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
      </div>
      <div class="stat-num-admin"><?= $emAndamento ?></div>
      <div class="stat-label-admin">Em andamento</div>
    </div>

    <div class="stat-card-admin">
      <div class="stat-icon" style="background:#ecfdf5">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      <div class="stat-num-admin"><?= $statusMap['fechamento']['n'] ?></div>
      <div class="stat-label-admin">Fechamentos</div>
    </div>
  </div>

  <!-- Dashboard grid -->
  <div class="dashboard-grid">

    <!-- Pipeline -->
    <div class="admin-card">
      <h2 class="admin-card-title">Pipeline de vendas</h2>
      <?php foreach ($statusMap as $key => $s): ?>
        <div class="pipeline-item">
          <div class="pipeline-label">
            <span><?= $s['label'] ?></span>
            <span><?= $s['n'] ?> lead<?= $s['n'] !== 1 ? 's' : '' ?></span>
          </div>
          <div class="pipeline-bar">
            <div class="pipeline-bar-fill pipeline-bar-<?= $key ?>" style="width:<?= barWidth($s['n'], $totalLeads) ?>%"></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Porte -->
    <div class="admin-card">
      <h2 class="admin-card-title">Leads por porte</h2>
      <?php if (empty($byPorte)): ?>
        <p class="admin-muted">Nenhum dado ainda.</p>
      <?php else: ?>
        <?php foreach ($byPorte as $p): ?>
          <div class="pipeline-item">
            <div class="pipeline-label">
              <span><?= htmlspecialchars($p['porte']) ?></span>
              <span><?= (int)$p['n'] ?></span>
            </div>
            <div class="pipeline-bar">
              <div class="pipeline-bar-fill" style="background:#094a86;width:<?= barWidth((int)$p['n'], $totalLeads) ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>

  <!-- Recent leads table -->
  <div class="admin-card" style="margin-top:20px">
    <h2 class="admin-card-title">Últimos leads recebidos</h2>
    <?php if (empty($recentLeads)): ?>
      <p class="admin-muted">Nenhum lead cadastrado ainda.</p>
    <?php else: ?>
      <div class="table-wrap">
        <table class="leads-table">
          <thead>
            <tr>
              <th>Nome</th>
              <th>Instituição</th>
              <th>Porte</th>
              <th>Status</th>
              <th>Data</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentLeads as $lead): ?>
              <tr>
                <td class="td-nome"><?= htmlspecialchars($lead['nome']) ?></td>
                <td><?= htmlspecialchars($lead['instituicao']) ?></td>
                <td><?= htmlspecialchars($lead['porte']) ?></td>
                <td><span class="badge badge-<?= $lead['status'] ?>"><?= $lead['status'] ?></span></td>
                <td><?= date('d/m/Y', strtotime($lead['criado_em'])) ?></td>
                <td><a href="/admin/crm.php" class="table-action-link">Ver CRM →</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

<?php endif; ?>

<?php include __DIR__ . '/_footer.php'; ?>
