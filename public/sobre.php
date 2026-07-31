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
  <title><?= tx('Sobre a TechSallus | Tecnologia para Gestão em Saúde', 'About TechSallus | Technology for Healthcare Management', 'Sobre TechSallus | Tecnología para Gestión en Salud') ?></title>
  <meta name="description" content="<?= tx('Conheça a visão da TechSallus para conectar atendimento, operação, faturamento e gestão de custos em instituições de saúde.', 'Learn about TechSallus\'s vision to connect care, operations, billing and cost management in health institutions.', 'Conozca la visión de TechSallus para conectar atención, operación, facturación y gestión de costos en instituciones de salud.') ?>"/>

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
      <div class="hero-label"><div class="hero-label-bar"></div><span><?= tx('Tecnologia pensada a partir da operação real', 'Technology designed from real-world operations', 'Tecnología pensada desde la operación real') ?></span></div>
      <h1 class="hero-headline"><?= tx('Soluções de saúde começam com boas perguntas, não com uma lista de módulos.', 'Healthcare solutions start with good questions, not a list of modules.', 'Las soluciones de salud empiezan con buenas preguntas, no con una lista de módulos.') ?></h1>
      <p class="page-hero-sub"><?= tx('Como reduzir faltas? Como fazer o paciente esperar menos? Como diminuir retrabalho no faturamento? Como saber quais serviços geram resultado? É a partir desses desafios que a TechSallus organiza suas soluções.', 'How to reduce no-shows? How to make patients wait less? How to reduce rework in billing? How to know which services generate results? TechSallus organizes its solutions around these challenges.', '¿Cómo reducir las ausencias? ¿Cómo hacer que el paciente espere menos? ¿Cómo disminuir el retrabajo en la facturación? ¿Cómo saber qué servicios generan resultado? A partir de estos desafíos, TechSallus organiza sus soluciones.') ?></p>
      <div class="hero-ctas">
        <a href="/contato" class="btn-primary"><?= tx('Falar sobre minha operação', 'Talk about my operation', 'Hablar sobre mi operación') ?></a>
      </div>
    </div>
  </div>
</section>

<section class="page-section">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Da agenda ao resultado.', 'From scheduling to results.', 'De la agenda al resultado.') ?></h2>
    <p class="page-section-sub"><?= tx('A TechSallus conecta processos clínicos, administrativos e financeiros para que a informação acompanhe o paciente e sustente a gestão. A plataforma é modular para respeitar o momento de cada instituição e integrada para evitar novas ilhas de informação.', 'TechSallus connects clinical, administrative and financial processes so information follows the patient and sustains management. The platform is modular to respect each institution\'s moment, and integrated to avoid new islands of information.', 'TechSallus conecta procesos clínicos, administrativos y financieros para que la información acompañe al paciente y sostenga la gestión. La plataforma es modular para respetar el momento de cada institución e integrada para evitar nuevas islas de información.') ?></p>
    <div class="card-grid card-grid-3">
      <div class="card"><div class="card-title"><?= tx('Começar pelo problema', 'Start with the problem', 'Comenzar por el problema') ?></div><p class="card-desc"><?= tx('O escopo parte do gargalo mais relevante para a instituição, como agenda, fluxo, faturamento, suprimentos ou custos.', 'Scope starts from the most relevant bottleneck for the institution, such as scheduling, flow, billing, supplies or costs.', 'El alcance parte del cuello de botella más relevante para la institución, como agenda, flujo, facturación, insumos o costos.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Entender a realidade da saúde', 'Understand the reality of healthcare', 'Entender la realidad de la salud') ?></div><p class="card-desc"><?= tx('A solução contempla padrões, regras e rotinas específicas de consultórios, clínicas, hospitais e pronto atendimento.', 'The solution covers specific standards, rules and routines for private practices, clinics, hospitals and urgent care.', 'La solución contempla estándares, reglas y rutinas específicas de consultorios, clínicas, hospitales y urgencias.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Conectar a jornada', 'Connect the journey', 'Conectar el recorrido') ?></div><p class="card-desc"><?= tx('Atendimento, assistência, faturamento e gestão compartilham informações ao longo da operação.', 'Care, clinical work, billing and management share information throughout the operation.', 'Atención, asistencia, facturación y gestión comparten información a lo largo de la operación.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Evoluir no ritmo do cliente', 'Evolve at the client\'s pace', 'Evolucionar al ritmo del cliente') ?></div><p class="card-desc"><?= tx('Módulos e integrações podem acompanhar o crescimento e o aumento de complexidade da instituição.', 'Modules and integrations can keep pace with the institution\'s growth and increasing complexity.', 'Los módulos e integraciones pueden acompañar el crecimiento y el aumento de complejidad de la institución.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Traduzir dados em decisão', 'Translate data into decisions', 'Traducir datos en decisión') ?></div><p class="card-desc"><?= tx('BI e gestão de custos ampliam a visão sobre desempenho, margem e prioridades.', 'BI and cost management broaden the view on performance, margin and priorities.', 'BI y gestión de costos amplían la visión sobre desempeño, margen y prioridades.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Manter profundidade técnica', 'Maintain technical depth', 'Mantener profundidad técnica') ?></div><p class="card-desc"><?= tx('Interoperabilidade, imagens, equipamentos, nuvem e continuidade sustentam a operação sem dominar a conversa.', 'Interoperability, imaging, equipment, cloud and continuity sustain the operation without dominating the conversation.', 'Interoperabilidad, imágenes, equipos, nube y continuidad sostienen la operación sin dominar la conversación.') ?></p></div>
    </div>
  </div>
</section>

<section class="page-section page-section-tint">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Soluções para diferentes realidades', 'Solutions for different realities', 'Soluciones para diferentes realidades') ?></h2>
    <div class="list-columns">
      <div class="list-column">
        <div class="list-column-title"><?= tx('Operação assistencial', 'Clinical operations', 'Operación asistencial') ?></div>
        <ul>
          <li><?= tx('Consultórios', 'Private practices', 'Consultorios') ?></li>
          <li><?= tx('Clínicas e policlínicas', 'Clinics and polyclinics', 'Clínicas y policlínicas') ?></li>
          <li><?= tx('Hospitais', 'Hospitals', 'Hospitales') ?></li>
          <li><?= tx('Pronto atendimento', 'Urgent care', 'Urgencias') ?></li>
        </ul>
      </div>
      <div class="list-column">
        <div class="list-column-title"><?= tx('Gestão e decisão', 'Management and decision-making', 'Gestión y decisión') ?></div>
        <ul>
          <li><?= tx('Faturamento e glosas', 'Billing and denials', 'Facturación y glosas') ?></li>
          <li><?= tx('Financeiro e repasses', 'Finance and payouts', 'Financiero y repartos') ?></li>
          <li><?= tx('BI e indicadores', 'BI and dashboards', 'BI e indicadores') ?></li>
          <li><?= tx('Apure Custos', 'Apure Custos', 'Apure Custos') ?></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="cta-bar cta-bar-dark">
  <div class="container">
    <h2 class="cta-bar-heading"><?= tx('Nosso papel', 'Our role', 'Nuestro papel') ?></h2>
    <p class="page-section-sub" style="margin:-8px auto 24px"><?= tx('Usar tecnologia para tornar o atendimento mais fluido, a operação mais controlada e a decisão mais bem informada.', 'Using technology to make care more fluid, operations more controlled and decisions better informed.', 'Usar tecnología para hacer la atención más fluida, la operación más controlada y la decisión mejor informada.') ?></p>
    <div class="cta-bar-actions"><a href="/contato" class="btn-primary"><?= tx('Falar com um especialista', 'Talk to a specialist', 'Hablar con un especialista') ?></a></div>
  </div>
</section>

</main>

<?php include __DIR__ . '/_partials/footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script src="/assets/js/main.js?v=20260731a"></script>
</body>
</html>
