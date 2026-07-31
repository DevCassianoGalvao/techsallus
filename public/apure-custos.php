<?php
$_root = file_exists(__DIR__ . '/../core/Settings.php') ? dirname(__DIR__) : __DIR__;
require_once $_root . '/core/Security.php';
Security::headers();
Security::csrfToken();

$_cfg = file_exists(__DIR__ . '/../config/i18n.php')
    ? __DIR__ . '/../config/i18n.php'
    : __DIR__ . '/config/i18n.php';
require_once $_cfg;
require_once $_root . '/core/Settings.php';

function getScripts(string $pos): string {
    try {
        return implode("\n", array_column(
            DB::fetchAll("SELECT conteudo FROM scripts_injecao WHERE posicao=? AND ativo=1 AND conteudo != ''", [$pos]),
            'conteudo'
        ));
    } catch (Exception $e) {
        return '';
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG ?>">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= tx('Gestão de Custos Hospitalares e Clínicos | Apure Custos', 'Hospital and Clinic Cost Management | Apure Custos', 'Gestión de Costos Hospitalarios y Clínicos | Apure Custos') ?></title>
  <meta name="description" content="<?= tx('Conheça custos e resultados por especialidade, paciente, unidade, produto e procedimento para decidir com base em margem, ponto de equilíbrio e viabilidade.', 'Know your costs and results by specialty, patient, unit, product and procedure to decide based on margin, break-even point and viability.', 'Conozca costos y resultados por especialidad, paciente, unidad, producto y procedimiento para decidir con base en margen, punto de equilibrio y viabilidad.') ?>"/>

  <link rel="icon" type="image/png" href="/assets/img/favicon.png"/>
  <link rel="shortcut icon" href="/assets/img/favicon.png"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/assets/css/main.css?v=20260731a"/>
  <?= getScripts('head') ?>
</head>
<body>
<?= getScripts('body') ?>

<?php include __DIR__ . '/_partials/nav.php'; ?>

<main>

<section class="page-hero">
  <svg class="hero-grid-bg" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" aria-hidden="true">
    <defs><pattern id="linegrid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.13)" stroke-width="0.8"/></pattern></defs>
    <rect width="100%" height="100%" fill="url(#linegrid)"/>
  </svg>
  <div class="container">
    <div class="page-hero-inner">
      <div class="hero-label"><div class="hero-label-bar"></div><span><?= tx('Gestão de custos para instituições de saúde', 'Cost management for health institutions', 'Gestión de costos para instituciones de salud') ?></span></div>
      <h1 class="hero-headline"><?= tx('Descubra onde sua operação gera resultado — e onde perde margem.', 'Discover where your operation generates results — and where it loses margin.', 'Descubra dónde su operación genera resultado — y dónde pierde margen.') ?></h1>
      <p class="page-hero-sub"><?= tx('O Apure Custos transforma dados de produção, materiais, pessoal, depreciação, custos gerais e repasses médicos em informação para mensuração, controle, decisão e análise de resultados.', 'Apure Custos turns production, materials, personnel, depreciation, overhead and physician payout data into information for measurement, control, decision-making and results analysis.', 'Apure Custos transforma datos de producción, materiales, personal, depreciación, costos generales y repartos médicos en información para medición, control, decisión y análisis de resultados.') ?></p>
      <div class="hero-ctas">
        <a href="/contato" class="btn-primary"><?= tx('Quero conhecer meus custos', 'I want to know my costs', 'Quiero conocer mis costos') ?></a>
        <a href="#como-funciona" class="btn-outline-white"><?= tx('Entender como funciona', 'Understand how it works', 'Entender cómo funciona') ?></a>
      </div>
    </div>
  </div>
</section>

<section class="page-section">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Faturamento não é margem.', 'Billing is not margin.', 'Facturación no es margen.') ?></h2>
    <p class="page-section-sub"><?= tx('Uma especialidade pode crescer em produção e ainda consumir resultado. Um procedimento pode parecer rentável até que materiais, equipe, estrutura, depreciação e repasses sejam considerados. O Apure reúne essas variáveis para mostrar o custo real da operação.', 'A specialty can grow in production and still consume results. A procedure can look profitable until materials, staff, structure, depreciation and payouts are factored in. Apure brings these variables together to show the real cost of the operation.', 'Una especialidad puede crecer en producción y aun así consumir resultado. Un procedimiento puede parecer rentable hasta considerar materiales, equipo, estructura, depreciación y repartos. Apure reúne estas variables para mostrar el costo real de la operación.') ?></p>
    <div class="card-grid card-grid-3">
      <div class="card"><div class="card-title"><?= tx('Resultado por especialidade', 'Results by specialty', 'Resultado por especialidad') ?></div><p class="card-desc"><?= tx('Compare produção, custo e resultado de cada linha assistencial.', 'Compare production, cost and results for each clinical line.', 'Compare producción, costo y resultado de cada línea asistencial.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Resultado por paciente', 'Results by patient', 'Resultado por paciente') ?></div><p class="card-desc"><?= tx('Analise o consumo de recursos e o resultado associado a diferentes perfis de atendimento.', 'Analyze resource consumption and the results associated with different care profiles.', 'Analice el consumo de recursos y el resultado asociado a diferentes perfiles de atención.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Resultado por unidade de negócio', 'Results by business unit', 'Resultado por unidad de negocio') ?></div><p class="card-desc"><?= tx('Entenda o desempenho de unidades, centros de custo e operações distintas.', 'Understand the performance of units, cost centers and different operations.', 'Entienda el desempeño de unidades, centros de costo y operaciones distintas.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Resultado por produto e procedimento', 'Results by product and procedure', 'Resultado por producto y procedimiento') ?></div><p class="card-desc"><?= tx('Conheça custos unitários e apoie decisões de preço, negociação e portfólio.', 'Know unit costs and support pricing, negotiation and portfolio decisions.', 'Conozca costos unitarios y apoye decisiones de precio, negociación y portafolio.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Ponto de equilíbrio', 'Break-even point', 'Punto de equilibrio') ?></div><p class="card-desc"><?= tx('Identifique o nível de produção necessário para cobrir a estrutura e sustentar a operação.', 'Identify the production level needed to cover overhead and sustain the operation.', 'Identifique el nivel de producción necesario para cubrir la estructura y sostener la operación.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Viabilidade de investimentos', 'Investment viability', 'Viabilidad de inversiones') ?></div><p class="card-desc"><?= tx('Avalie expansão, equipamentos, serviços e projetos com uma base econômica mais consistente.', 'Evaluate expansion, equipment, services and projects on a more consistent economic basis.', 'Evalúe expansión, equipos, servicios y proyectos con una base económica más consistente.') ?></p></div>
    </div>
  </div>
</section>

<section class="page-section page-section-tint">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('As informações que formam o custo real.', 'The information that forms the real cost.', 'La información que forma el costo real.') ?></h2>
    <div class="card-grid card-grid-3">
      <div class="card"><div class="card-title"><?= tx('Receita e produção', 'Revenue and production', 'Ingresos y producción') ?></div><p class="card-desc"><?= tx('Atendimentos, procedimentos, serviços e faturamento realizados.', 'Visits, procedures, services and billing performed.', 'Atenciones, procedimientos, servicios y facturación realizados.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Materiais e medicamentos', 'Materials and medication', 'Materiales y medicamentos') ?></div><p class="card-desc"><?= tx('Consumo associado à assistência, aos procedimentos e às unidades.', 'Consumption associated with care, procedures and units.', 'Consumo asociado a la asistencia, los procedimientos y las unidades.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Pessoal', 'Personnel', 'Personal') ?></div><p class="card-desc"><?= tx('Custos de equipes próprias, plantões e demais componentes relacionados.', 'Costs of in-house staff, on-call shifts and other related components.', 'Costos de equipos propios, guardias y demás componentes relacionados.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Depreciação', 'Depreciation', 'Depreciación') ?></div><p class="card-desc"><?= tx('Uso econômico de equipamentos, instalações e ativos.', 'Economic use of equipment, facilities and assets.', 'Uso económico de equipos, instalaciones y activos.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Custos gerais', 'Overhead', 'Costos generales') ?></div><p class="card-desc"><?= tx('Despesas compartilhadas e estruturas de apoio.', 'Shared expenses and support structures.', 'Gastos compartidos y estructuras de apoyo.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Repasses médicos', 'Physician payouts', 'Repartos médicos') ?></div><p class="card-desc"><?= tx('Valores vinculados à produção e às regras de remuneração.', 'Amounts tied to production and compensation rules.', 'Valores vinculados a la producción y a las reglas de remuneración.') ?></p></div>
    </div>
  </div>
</section>

<section class="page-section" id="como-funciona">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Da integração dos dados à decisão gerencial.', 'From data integration to management decisions.', 'De la integración de datos a la decisión gerencial.') ?></h2>
    <div class="numbered-steps">
      <div class="step-item"><div class="step-num">1</div><p class="step-text"><strong><?= tx('Diagnóstico.', 'Diagnosis.', 'Diagnóstico.') ?></strong> <?= tx('Definição do responsável, revisão de centros de custo, plano de contas, fontes de dados e regras de negócio.', 'Defining the owner, reviewing cost centers, the chart of accounts, data sources and business rules.', 'Definición del responsable, revisión de centros de costo, plan de cuentas, fuentes de datos y reglas de negocio.') ?></p></div>
      <div class="step-item"><div class="step-num">2</div><p class="step-text"><strong><?= tx('Integração.', 'Integration.', 'Integración.') ?></strong> <?= tx('Importação das informações dos sistemas de origem para evitar redigitação e retrabalho.', 'Importing information from source systems to avoid re-entry and rework.', 'Importación de la información de los sistemas de origen para evitar redigitación y retrabajo.') ?></p></div>
      <div class="step-item"><div class="step-num">3</div><p class="step-text"><strong><?= tx('Parametrização.', 'Parameterization.', 'Parametrización.') ?></strong> <?= tx('Configuração de critérios, regras de rateio, procedimentos, produtos e dimensões de análise.', 'Configuring criteria, allocation rules, procedures, products and analysis dimensions.', 'Configuración de criterios, reglas de prorrateo, procedimientos, productos y dimensiones de análisis.') ?></p></div>
      <div class="step-item"><div class="step-num">4</div><p class="step-text"><strong><?= tx('Cálculo e validação.', 'Calculation and validation.', 'Cálculo y validación.') ?></strong> <?= tx('Apuração dos custos, análise dos resultados e ajustes em conjunto com os gestores.', 'Cost calculation, results analysis and adjustments together with managers.', 'Cálculo de los costos, análisis de los resultados y ajustes junto con los gestores.') ?></p></div>
      <div class="step-item"><div class="step-num">5</div><p class="step-text"><strong><?= tx('Gestão contínua.', 'Ongoing management.', 'Gestión continua.') ?></strong> <?= tx('Liberação de relatórios gerenciais e painéis de BI para acompanhamento, metas e tomada de decisão.', 'Release of management reports and BI dashboards for tracking, goals and decision-making.', 'Liberación de informes gerenciales y paneles de BI para seguimiento, metas y toma de decisiones.') ?></p></div>
    </div>
  </div>
</section>

<section class="cta-bar cta-bar-dark">
  <div class="container">
    <h2 class="cta-bar-heading"><?= tx('Do dado operacional à decisão', 'From operational data to decision', 'Del dato operativo a la decisión') ?></h2>
    <p class="page-section-sub" style="margin:-8px auto 24px"><?= tx('O Apure Custos não entrega apenas um cálculo. Ele organiza uma rotina de gestão para que diferentes áreas enxerguem o mesmo resultado e decidam com uma base comum.', 'Apure Custos doesn\'t just deliver a calculation. It organizes a management routine so different areas see the same result and decide on common ground.', 'Apure Custos no entrega solo un cálculo. Organiza una rutina de gestión para que diferentes áreas vean el mismo resultado y decidan con una base común.') ?></p>
    <div class="cta-bar-actions"><a href="/contato" class="btn-primary"><?= tx('Transformar meus custos em decisão', 'Turn my costs into decisions', 'Transformar mis costos en decisión') ?></a></div>
  </div>
</section>

</main>

<?php include __DIR__ . '/_partials/footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script src="/assets/js/main.js?v=20260731a"></script>
</body>
</html>
