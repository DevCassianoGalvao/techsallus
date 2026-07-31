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
  <title><?= tx('Reduza Faltas, Filas e Glosas na Saúde | TechSallus', 'Reduce No-Shows, Queues and Denials in Healthcare | TechSallus', 'Reduzca Ausencias, Filas y Glosas en Salud | TechSallus') ?></title>
  <meta name="description" content="<?= tx('Conheça como a TechSallus ajuda instituições de saúde a melhorar a ocupação da agenda, acelerar o atendimento, controlar o faturamento e enxergar custos.', 'Learn how TechSallus helps health institutions improve schedule occupancy, speed up care, control billing and gain cost visibility.', 'Conozca cómo TechSallus ayuda a las instituciones de salud a mejorar la ocupación de la agenda, acelerar la atención, controlar la facturación y visualizar costos.') ?>"/>

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
      <div class="hero-label"><div class="hero-label-bar"></div><span><?= tx('Problemas que a gestão reconhece', 'Problems management recognizes', 'Problemas que la gestión reconoce') ?></span></div>
      <h1 class="hero-headline"><?= tx('Menos gargalos entre o paciente e o resultado.', 'Fewer bottlenecks between the patient and the result.', 'Menos cuellos de botella entre el paciente y el resultado.') ?></h1>
      <p class="page-hero-sub"><?= tx('Conecte pessoas, processos e informações para reduzir perdas, acelerar a jornada do paciente e dar mais controle às áreas assistencial, administrativa e financeira.', 'Connect people, processes and information to reduce losses, speed up the patient journey and give more control to clinical, administrative and financial areas.', 'Conecte personas, procesos e información para reducir pérdidas, acelerar el recorrido del paciente y dar más control a las áreas asistencial, administrativa y financiera.') ?></p>
      <div class="hero-ctas">
        <a href="/contato" class="btn-primary"><?= tx('Quero conversar sobre meu principal desafio', 'I want to talk about my main challenge', 'Quiero conversar sobre mi principal desafío') ?></a>
      </div>
      <p class="hero-caption"><?= tx('A TechSallus combina módulos e integrações de acordo com a realidade de cada instituição.', 'TechSallus combines modules and integrations according to each institution\'s reality.', 'TechSallus combina módulos e integraciones de acuerdo con la realidad de cada institución.') ?></p>
    </div>
  </div>
</section>

<section class="page-section" id="absenteismo">
  <div class="container">
    <div class="card" style="max-width:900px">
      <div class="card-title" style="font-size:20px"><?= tx('Absenteísmo e ocupação da agenda', 'No-shows and schedule occupancy', 'Ausentismo y ocupación de la agenda') ?></div>
      <p class="card-desc" style="margin-bottom:10px"><strong><?= tx('O desafio:', 'The challenge:', 'El desafío:') ?></strong> <?= tx('Horários vazios diminuem a capacidade de atendimento, enquanto confirmações manuais ocupam a equipe com ligações e mensagens repetitivas.', 'Empty time slots reduce care capacity, while manual confirmations tie up staff with repetitive calls and messages.', 'Los horarios vacíos disminuyen la capacidad de atención, mientras que las confirmaciones manuales ocupan al equipo con llamadas y mensajes repetitivos.') ?></p>
      <p class="card-desc" style="margin-bottom:10px"><strong><?= tx('Como a TechSallus ajuda:', 'How TechSallus helps:', 'Cómo ayuda TechSallus:') ?></strong> <?= tx('Agendamento integrado e confirmação por WhatsApp, com chatbot e assistência de IA conforme a contratação, apoiam uma rotina mais previsível e reduzem o esforço operacional.', 'Integrated scheduling and WhatsApp confirmation, with chatbot and AI assistance according to the plan, support a more predictable routine and reduce operational effort.', 'La agenda integrada y la confirmación por WhatsApp, con chatbot y asistencia de IA según la contratación, apoyan una rutina más previsible y reducen el esfuerzo operativo.') ?></p>
      <p class="card-desc"><strong><?= tx('Impacto para a operação:', 'Impact on the operation:', 'Impacto para la operación:') ?></strong> <?= tx('Melhores condições para aproveitar a agenda, reagir a cancelamentos e concentrar a equipe em atividades de maior valor.', 'Better conditions to make the most of the schedule, react to cancellations and focus the team on higher-value activities.', 'Mejores condiciones para aprovechar la agenda, reaccionar a cancelaciones y concentrar al equipo en actividades de mayor valor.') ?></p>
    </div>
  </div>
</section>

<section class="page-section page-section-tint" id="fluxo">
  <div class="container">
    <div class="card" style="max-width:900px">
      <div class="card-title" style="font-size:20px"><?= tx('Velocidade e experiência no atendimento', 'Speed and experience in care', 'Velocidad y experiencia en la atención') ?></div>
      <p class="card-desc" style="margin-bottom:10px"><strong><?= tx('O desafio:', 'The challenge:', 'El desafío:') ?></strong> <?= tx('Filas, cadastros repetidos e pouca coordenação entre recepção, acolhimento, triagem e consultório aumentam a espera e a frustração do paciente.', 'Queues, repeated registrations and poor coordination between front desk, intake, triage and exam room increase waiting time and patient frustration.', 'Filas, registros repetidos y poca coordinación entre recepción, acogida, triaje y consultorio aumentan la espera y la frustración del paciente.') ?></p>
      <p class="card-desc" style="margin-bottom:10px"><strong><?= tx('Como a TechSallus ajuda:', 'How TechSallus helps:', 'Cómo ayuda TechSallus:') ?></strong> <?= tx('Totem, recepção, distribuição de senhas, chamadas por Smart TV, acolhimento e triagem trabalham no mesmo fluxo, com protocolos configuráveis conforme o perfil da operação.', 'Kiosk, front desk, ticket distribution, Smart TV calling, intake and triage work within the same flow, with configurable protocols according to the operation\'s profile.', 'Totem, recepción, distribución de turnos, llamadas por Smart TV, acogida y triaje trabajan en el mismo flujo, con protocolos configurables según el perfil de la operación.') ?></p>
      <p class="card-desc"><strong><?= tx('Impacto para a operação:', 'Impact on the operation:', 'Impacto para la operación:') ?></strong> <?= tx('Mais fluidez para o paciente e mais visibilidade para a equipe acompanhar cada etapa do atendimento.', 'More fluidity for the patient and more visibility for the team to track each step of care.', 'Más fluidez para el paciente y más visibilidad para que el equipo siga cada etapa de la atención.') ?></p>
    </div>
  </div>
</section>

<section class="page-section" id="faturamento">
  <div class="container">
    <div class="card" style="max-width:900px">
      <div class="card-title" style="font-size:20px"><?= tx('Faturamento, glosas e repasses', 'Billing, denials and payouts', 'Facturación, glosas y repartos') ?></div>
      <p class="card-desc" style="margin-bottom:10px"><strong><?= tx('O desafio:', 'The challenge:', 'El desafío:') ?></strong> <?= tx('Regras de convênios, códigos, guias e repasses espalhados em controles paralelos aumentam a chance de inconsistências e atrasam o fechamento.', 'Payer rules, codes, claims and payouts scattered across parallel controls increase the chance of inconsistencies and delay closing.', 'Reglas de convenios, códigos, guías y repartos dispersos en controles paralelos aumentan la posibilidad de inconsistencias y retrasan el cierre.') ?></p>
      <p class="card-desc" style="margin-bottom:10px"><strong><?= tx('Como a TechSallus ajuda:', 'How TechSallus helps:', 'Cómo ayuda TechSallus:') ?></strong> <?= tx('Faturamento TISS, base TUSS, geração de guias, regras de convênios, controle de glosas e repasses médicos ficam conectados à produção assistencial.', 'TISS billing, the TUSS database, claim generation, payer rules, denial control and physician payouts stay connected to clinical production.', 'La facturación TISS, la base TUSS, la generación de guías, las reglas de convenios, el control de glosas y los repartos médicos quedan conectados a la producción asistencial.') ?></p>
      <p class="card-desc"><strong><?= tx('Impacto para a operação:', 'Impact on the operation:', 'Impacto para la operación:') ?></strong> <?= tx('Mais consistência no ciclo de receita, menos retrabalho e maior capacidade de identificar onde o processo precisa de correção.', 'More consistency in the revenue cycle, less rework and a greater ability to identify where the process needs correction.', 'Más consistencia en el ciclo de ingresos, menos retrabajo y mayor capacidad de identificar dónde el proceso necesita corrección.') ?></p>
    </div>
  </div>
</section>

<section class="page-section page-section-tint">
  <div class="container">
    <div class="card" style="max-width:900px">
      <div class="card-title" style="font-size:20px"><?= tx('Continuidade da informação clínica', 'Continuity of clinical information', 'Continuidad de la información clínica') ?></div>
      <p class="card-desc" style="margin-bottom:10px"><strong><?= tx('O desafio:', 'The challenge:', 'El desafío:') ?></strong> <?= tx('Quando prontuário, laudos, imagens, prescrições e dados de equipamentos ficam separados, a equipe perde tempo procurando informação e repetindo registros.', 'When medical records, reports, images, prescriptions and equipment data are kept separate, the team loses time searching for information and repeating records.', 'Cuando la historia clínica, informes, imágenes, prescripciones y datos de equipos quedan separados, el equipo pierde tiempo buscando información y repitiendo registros.') ?></p>
      <p class="card-desc" style="margin-bottom:10px"><strong><?= tx('Como a TechSallus ajuda:', 'How TechSallus helps:', 'Cómo ayuda TechSallus:') ?></strong> <?= tx('Prontuário eletrônico, laudos complementares, PACS/DICOM, integrações com RIS e interoperabilidade via HL7 conectam dados ao ponto de cuidado.', 'Electronic medical records, complementary reports, PACS/DICOM, RIS integrations and HL7 interoperability connect data to the point of care.', 'La historia clínica electrónica, informes complementarios, PACS/DICOM, integraciones con RIS e interoperabilidad vía HL7 conectan los datos al punto de cuidado.') ?></p>
      <p class="card-desc"><strong><?= tx('Impacto para a operação:', 'Impact on the operation:', 'Impacto para la operación:') ?></strong> <?= tx('Informação mais acessível ao longo da jornada e menos rupturas entre atendimento, diagnóstico e conduta.', 'More accessible information throughout the journey and fewer breakdowns between care, diagnosis and treatment.', 'Información más accesible a lo largo del recorrido y menos rupturas entre atención, diagnóstico y conducta.') ?></p>
    </div>
  </div>
</section>

<section class="page-section">
  <div class="container">
    <div class="card" style="max-width:900px">
      <div class="card-title" style="font-size:20px"><?= tx('Materiais, medicamentos e farmácia', 'Materials, medication and pharmacy', 'Materiales, medicamentos y farmacia') ?></div>
      <p class="card-desc" style="margin-bottom:10px"><strong><?= tx('O desafio:', 'The challenge:', 'El desafío:') ?></strong> <?= tx('Compras, estoque, farmácia e consumo assistencial sem uma visão comum dificultam rastreabilidade, reposição e controle de perdas.', 'Purchasing, inventory, pharmacy and clinical consumption without a shared view make traceability, replenishment and loss control harder.', 'Compras, inventario, farmacia y consumo asistencial sin una visión común dificultan la trazabilidad, la reposición y el control de pérdidas.') ?></p>
      <p class="card-desc" style="margin-bottom:10px"><strong><?= tx('Como a TechSallus ajuda:', 'How TechSallus helps:', 'Cómo ayuda TechSallus:') ?></strong> <?= tx('A solução integra solicitações de compra, estoque, farmácia, lotes, etiquetagem e automações de dispensação conforme o escopo do projeto.', 'The solution integrates purchase requests, inventory, pharmacy, batches, labeling and dispensing automation according to the project scope.', 'La solución integra solicitudes de compra, inventario, farmacia, lotes, etiquetado y automatizaciones de dispensación según el alcance del proyecto.') ?></p>
      <p class="card-desc"><strong><?= tx('Impacto para a operação:', 'Impact on the operation:', 'Impacto para la operación:') ?></strong> <?= tx('Mais controle sobre o que entra, o que é consumido e o que precisa ser reposto, com ligação direta à operação assistencial.', 'More control over what comes in, what is consumed and what needs restocking, directly linked to clinical operations.', 'Más control sobre lo que entra, lo que se consume y lo que necesita reponerse, con conexión directa a la operación asistencial.') ?></p>
    </div>
  </div>
</section>

<section class="page-section page-section-tint" id="custos">
  <div class="container">
    <div class="card" style="max-width:900px">
      <div class="card-title" style="font-size:20px"><?= tx('Custos, margem e decisão', 'Costs, margin and decision-making', 'Costos, margen y decisión') ?></div>
      <p class="card-desc" style="margin-bottom:10px"><strong><?= tx('O desafio:', 'The challenge:', 'El desafío:') ?></strong> <?= tx('Receita e volume de atendimento não mostram, sozinhos, quais especialidades, unidades ou procedimentos geram resultado.', 'Revenue and care volume alone do not show which specialties, units or procedures generate results.', 'Los ingresos y el volumen de atención no muestran, por sí solos, qué especialidades, unidades o procedimientos generan resultado.') ?></p>
      <p class="card-desc" style="margin-bottom:10px"><strong><?= tx('Como a TechSallus ajuda:', 'How TechSallus helps:', 'Cómo ayuda TechSallus:') ?></strong> <?= tx('O Apure Custos consolida produção, materiais, pessoal, depreciação, custos gerais e repasses para calcular custos e resultados em diferentes dimensões.', 'Apure Custos consolidates production, materials, personnel, depreciation, overhead and payouts to calculate costs and results across different dimensions.', 'Apure Custos consolida producción, materiales, personal, depreciación, costos generales y repartos para calcular costos y resultados en diferentes dimensiones.') ?></p>
      <p class="card-desc"><strong><?= tx('Impacto para a operação:', 'Impact on the operation:', 'Impacto para la operación:') ?></strong> <?= tx('Decisões apoiadas por margem, ponto de equilíbrio, rentabilidade e viabilidade de investimento, em vez de médias ou percepção.', 'Decisions supported by margin, break-even point, profitability and investment viability, instead of averages or perception.', 'Decisiones apoyadas por margen, punto de equilibrio, rentabilidad y viabilidad de inversión, en lugar de promedios o percepción.') ?></p>
    </div>
  </div>
</section>

<section class="cta-bar cta-bar-dark">
  <div class="container">
    <h2 class="cta-bar-heading"><?= tx('Uma conversa que começa pelo problema', 'A conversation that starts with the problem', 'Una conversación que empieza por el problema') ?></h2>
    <p class="page-section-sub" style="margin:-8px auto 24px"><?= tx('Antes de falar em módulos, a TechSallus mapeia o gargalo que mais impacta a sua instituição e organiza o escopo a partir dessa prioridade.', 'Before talking about modules, TechSallus maps the bottleneck that impacts your institution the most and organizes scope around that priority.', 'Antes de hablar de módulos, TechSallus mapea el cuello de botella que más impacta a su institución y organiza el alcance a partir de esa prioridad.') ?></p>
    <div class="cta-bar-actions"><a href="/contato" class="btn-primary"><?= t('cta_mapear_gargalo') ?></a></div>
  </div>
</section>

</main>

<?php include __DIR__ . '/_partials/footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script src="/assets/js/main.js?v=20260731a"></script>
</body>
</html>
