<?php
define('ADMIN_PAGE', true);

$rootDir = dirname(__DIR__);
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Auth.php';
require_once $rootDir . '/core/Schema.php';
Env::load($rootDir . '/.env');
Auth::require();
Schema::ensureLeadTrackingColumns();

$adminUser = Auth::user();
$pageTitle = 'Contatos';
$activeNav = 'contatos';

$busca   = trim($_GET['busca']   ?? '');
$estado  = trim($_GET['estado']  ?? '');
$tipo    = trim($_GET['tipo']    ?? '');
$porte   = trim($_GET['porte']   ?? '');
$periodo = trim($_GET['periodo'] ?? '');

$where  = ['1=1'];
$params = [];

if ($busca) {
    $t = "%{$busca}%";
    $where[] = "(nome LIKE ? OR instituicao LIKE ? OR email LIKE ? OR cidade LIKE ?)";
    $params = array_merge($params, [$t, $t, $t, $t]);
}
if ($estado) {
    $where[] = "estado = ?";
    $params[] = $estado;
}
if ($tipo) {
    $where[] = "tipo_instituicao = ?";
    $params[] = $tipo;
}
if ($porte) {
    $where[] = "porte = ?";
    $params[] = $porte;
}
if ($periodo === 'hoje') {
    $where[] = "DATE(criado_em) = CURDATE()";
} elseif ($periodo === 'semana') {
    $where[] = "criado_em >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif ($periodo === 'mes') {
    $where[] = "criado_em >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
}

$whereSQL = implode(' AND ', $where);
$total = (int)(DB::fetchOne("SELECT COUNT(*) AS t FROM leads WHERE {$whereSQL}", $params)['t'] ?? 0);

$porPagina = 25;
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$offset = ($pagina - 1) * $porPagina;
$totalPags = (int)ceil($total / $porPagina);

$contatos = DB::fetchAll(
    "SELECT * FROM leads WHERE {$whereSQL} ORDER BY criado_em DESC LIMIT {$porPagina} OFFSET {$offset}",
    $params
);

$estados = DB::fetchAll("SELECT DISTINCT estado FROM leads WHERE estado IS NOT NULL AND estado != '' ORDER BY estado");
$tipos = DB::fetchAll("SELECT DISTINCT tipo_instituicao FROM leads WHERE tipo_instituicao IS NOT NULL AND tipo_instituicao != '' ORDER BY tipo_instituicao");

$queryStr = http_build_query(array_filter([
    'busca'   => $busca,
    'estado'  => $estado,
    'tipo'    => $tipo,
    'porte'   => $porte,
    'periodo' => $periodo,
]));
$exportUrl = '/api/exportar-leads.php' . ($queryStr ? '?' . $queryStr : '');

$topbarExtra = '<div class="topbar-actions">
  <span class="admin-muted">' . $total . ' contato' . ($total !== 1 ? 's' : '') . '</span>
  <a href="' . htmlspecialchars($exportUrl) . '" class="btn-export">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
      <polyline points="7 10 12 15 17 10"/>
      <line x1="12" y1="15" x2="12" y2="3"/>
    </svg>
    Exportar CSV
  </a>
</div>';

include __DIR__ . '/_header.php';
?>

<div class="crm-filters">
  <form method="GET" class="filter-form" action="/admin/contatos.php">
    <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Nome, e-mail, instituição, cidade..." class="filter-input">

    <select name="estado" class="filter-select">
      <option value="">Todos os estados</option>
      <?php foreach ($estados as $e): ?>
        <option value="<?= htmlspecialchars($e['estado']) ?>" <?= $estado === $e['estado'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($e['estado']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="tipo" class="filter-select">
      <option value="">Todos os tipos</option>
      <?php foreach ($tipos as $t): ?>
        <option value="<?= htmlspecialchars($t['tipo_instituicao']) ?>" <?= $tipo === $t['tipo_instituicao'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($t['tipo_instituicao']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="porte" class="filter-select">
      <option value="">Todos os portes</option>
      <option value="1-2" <?= $porte === '1-2' ? 'selected' : '' ?>>1-2 profissionais</option>
      <option value="3-9" <?= $porte === '3-9' ? 'selected' : '' ?>>3-9 profissionais</option>
      <option value="10-30" <?= $porte === '10-30' ? 'selected' : '' ?>>10-30 profissionais</option>
      <option value="31-100" <?= $porte === '31-100' ? 'selected' : '' ?>>31-100 profissionais</option>
      <option value="100+" <?= $porte === '100+' ? 'selected' : '' ?>>100+ profissionais</option>
    </select>

    <select name="periodo" class="filter-select">
      <option value="">Qualquer período</option>
      <option value="hoje" <?= $periodo === 'hoje' ? 'selected' : '' ?>>Hoje</option>
      <option value="semana" <?= $periodo === 'semana' ? 'selected' : '' ?>>Últimos 7 dias</option>
      <option value="mes" <?= $periodo === 'mes' ? 'selected' : '' ?>>Últimos 30 dias</option>
    </select>

    <button type="submit" class="filter-btn">Filtrar</button>
    <?php if ($busca || $estado || $tipo || $porte || $periodo): ?>
      <a href="/admin/contatos.php" class="filter-clear">Limpar</a>
    <?php endif; ?>
  </form>
</div>

<div class="admin-card">
  <div class="table-wrap">
    <table class="leads-table">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Cargo</th>
          <th>Tipo</th>
          <th>Instituição</th>
          <th>Cidade/UF</th>
          <th>Porte</th>
          <th>E-mail</th>
          <th>WhatsApp</th>
          <th>Status</th>
          <th>UTM</th>
          <th>Data</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($contatos as $c): ?>
          <tr>
            <td class="td-nome"><?= htmlspecialchars($c['nome']) ?></td>
            <td><?= htmlspecialchars($c['cargo']) ?></td>
            <td><?= htmlspecialchars($c['tipo_instituicao'] ?? '-') ?></td>
            <td><?= htmlspecialchars($c['instituicao']) ?></td>
            <td><?= htmlspecialchars(($c['cidade'] ?? '-') . ' / ' . ($c['estado'] ?? '-')) ?></td>
            <td><?= htmlspecialchars($c['porte']) ?></td>
            <td>
              <a href="mailto:<?= htmlspecialchars($c['email']) ?>" style="color:#094a86">
                <?= htmlspecialchars($c['email']) ?>
              </a>
            </td>
            <td>
              <a href="https://wa.me/55<?= preg_replace('/\D/', '', $c['whatsapp']) ?>" target="_blank" rel="noopener noreferrer" style="color:#25d366;text-decoration:none">
                <?= htmlspecialchars($c['whatsapp']) ?>
              </a>
            </td>
            <td><span class="badge badge-<?= htmlspecialchars($c['status']) ?>"><?= ucfirst($c['status']) ?></span></td>
            <td style="font-size:11px;color:#64748b">
              <?= htmlspecialchars(($c['utm_source'] ?: '-') . ' / ' . ($c['utm_campaign'] ?: '-')) ?>
            </td>
            <td style="font-size:12px;white-space:nowrap"><?= date('d/m/Y', strtotime($c['criado_em'])) ?></td>
          </tr>
        <?php endforeach; ?>

        <?php if (empty($contatos)): ?>
          <tr>
            <td colspan="11" style="text-align:center;color:#94a3b8;padding:40px">Nenhum contato encontrado.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($totalPags > 1): ?>
    <div style="display:flex;justify-content:center;gap:6px;padding:20px 0 4px">
      <?php if ($pagina > 1): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $pagina - 1])) ?>" class="btn-export">←</a>
      <?php endif; ?>

      <?php for ($i = max(1, $pagina - 2); $i <= min($totalPags, $pagina + 2); $i++): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>" class="btn-export" style="<?= $i === $pagina ? 'background:#094a86;color:#fff;border-color:#094a86' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>

      <?php if ($pagina < $totalPags): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $pagina + 1])) ?>" class="btn-export">→</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/_footer.php'; ?>
