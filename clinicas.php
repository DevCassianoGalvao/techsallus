<?php
$_root = __DIR__;
require_once $_root . '/core/Security.php';
Security::headers();
Security::csrfToken();

require_once __DIR__ . '/config/i18n.php';
require_once $_root . '/core/Settings.php';
require_once __DIR__ . '/_partials/icons.php';

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

$_benefits = [
    ['calendarCheck', tx('Melhor aproveitamento da agenda', 'Better use of the schedule', 'Mejor aprovechamiento de la agenda'), tx('Agendamento por call center e confirmações por WhatsApp apoiam ocupação e reduzem trabalho repetitivo.', 'Call-center scheduling and WhatsApp confirmations support occupancy and reduce repetitive work.', 'La agenda por call center y las confirmaciones por WhatsApp apoyan la ocupación y reducen el trabajo repetitivo.')],
    ['users', tx('Chegada mais organizada', 'A more organized arrival', 'Llegada más organizada'), tx('Totem, recepção, guias, senhas e chamadas distribuem o fluxo com mais clareza.', 'Kiosk, front desk, claims, tickets and calling distribute the flow more clearly.', 'Totem, recepción, guías, turnos y llamadas distribuyen el flujo con más claridad.')],
    ['heartPulse', tx('Assistência conectada', 'Connected care', 'Asistencia conectada'), tx('Acolhimento, prontuário, laudos, imagens e dados de equipamentos acompanham o paciente.', 'Intake, medical records, reports, images and equipment data follow the patient.', 'Acogida, historia clínica, informes, imágenes y datos de equipos acompañan al paciente.')],
    ['receiptCheck', tx('Receita sob controle', 'Revenue under control', 'Ingresos bajo control'), tx('TISS, glosas, repasses e financeiro ficam ligados à produção realizada.', 'TISS, denials, payouts and finance stay linked to the production performed.', 'TISS, glosas, repartos y financiero quedan ligados a la producción realizada.')],
    ['boxCheck', tx('Suprimentos integrados', 'Integrated supplies', 'Insumos integrados'), tx('Compras, estoque, farmácia, lotes, patrimônio e manutenção ganham visibilidade.', 'Purchasing, inventory, pharmacy, batches, assets and maintenance gain visibility.', 'Compras, inventario, farmacia, lotes, patrimonio y mantenimiento ganan visibilidad.')],
    ['chartUp', tx('Gestão orientada por dados', 'Data-driven management', 'Gestión orientada por datos'), tx('Painéis de BI e integração com o Apure Custos ampliam a leitura da operação.', 'BI dashboards and integration with Apure Custos broaden the reading of the operation.', 'Paneles de BI e integración con Apure Custos amplían la lectura de la operación.')],
];

$_journey = [
    [tx('Agendar e confirmar', 'Schedule and confirm', 'Agendar y confirmar'), tx('Centralize agendas, especialidades e unidades, com confirmação via WhatsApp conforme a contratação.', 'Centralize schedules, specialties and units, with WhatsApp confirmation according to the contracted plan.', 'Centralice agendas, especialidades y unidades, con confirmación por WhatsApp según la contratación.')],
    [tx('Receber e organizar o fluxo', 'Receive and organize the flow', 'Recibir y organizar el flujo'), tx('Totem, recepção, geração de guias, distribuição de senhas e chamadas por Smart TV ajudam a reduzir atritos na chegada.', 'Kiosk, front desk, claim generation, ticket distribution and Smart TV calling help reduce friction on arrival.', 'Totem, recepción, generación de guías, distribución de turnos y llamadas por Smart TV ayudan a reducir la fricción en la llegada.')],
    [tx('Acolher e atender', 'Welcome and care', 'Acoger y atender'), tx('Protocolos configuráveis e integração com monitores de parâmetros vitais via HL7 apoiam o direcionamento do paciente.', 'Configurable protocols and HL7 integration with vital-sign monitors support patient routing.', 'Protocolos configurables e integración con monitores de signos vitales vía HL7 apoyan la derivación del paciente.')],
    [tx('Realizar procedimentos e exames', 'Perform procedures and exams', 'Realizar procedimientos y exámenes'), tx('Prontuário, laudos, PACS/DICOM e integração com RIS conectam a informação clínica ao atendimento.', 'Medical records, reports, PACS/DICOM and RIS integration connect clinical information to care.', 'Historia clínica, informes, PACS/DICOM e integración con RIS conectan la información clínica a la atención.')],
    [tx('Faturar e acompanhar', 'Bill and track', 'Facturar y acompañar'), tx('TISS, glosas, repasses e financeiro dão continuidade ao ciclo de receita.', 'TISS, denials, payouts and finance give continuity to the revenue cycle.', 'TISS, glosas, repartos y financiero dan continuidad al ciclo de ingresos.')],
    [tx('Gerir recursos e desempenho', 'Manage resources and performance', 'Gestionar recursos y desempeño'), tx('Compras, estoque, farmácia, patrimônio, manutenção, BI e custos completam a visão da operação.', 'Purchasing, inventory, pharmacy, assets, maintenance, BI and costs complete the view of the operation.', 'Compras, inventario, farmacia, patrimonio, mantenimiento, BI y costos completan la visión de la operación.')],
];

$_resources = [
    [tx('Agenda e jornada do paciente', 'Schedule and patient journey', 'Agenda y recorrido del paciente'), [
        tx('Agendamento por call center e confirmação por WhatsApp.', 'Call-center scheduling and WhatsApp confirmation.', 'Agenda por call center y confirmación por WhatsApp.'),
        tx('Atendimento por totem ou recepção, com geração de guias conforme o convênio.', 'Check-in via kiosk or front desk, with claim generation according to the payer.', 'Atención por totem o recepción, con generación de guías según el convenio.'),
        tx('Distribuição de senhas e chamadas por Smart TV.', 'Ticket distribution and Smart TV calling.', 'Distribución de turnos y llamadas por Smart TV.'),
        tx('Acolhimento configurável por especialidade ou perfil de paciente.', 'Intake configurable by specialty or patient profile.', 'Acogida configurable por especialidad o perfil de paciente.'),
    ]],
    [tx('Assistência e diagnóstico', 'Care and diagnostics', 'Asistencia y diagnóstico'), [
        tx('Prontuário eletrônico orientado pelo método SOAP.', 'Electronic medical record based on the SOAP method.', 'Historia clínica electrónica orientada por el método SOAP.'),
        tx('Integração HL7 com monitores de parâmetros vitais.', 'HL7 integration with vital-sign monitors.', 'Integración HL7 con monitores de signos vitales.'),
        tx('Laudos complementares, PACS/DICOM e integração com RIS.', 'Complementary reports, PACS/DICOM and RIS integration.', 'Informes complementarios, PACS/DICOM e integración con RIS.'),
        tx('Fluxos de oncologia e quimioterapia conforme o escopo.', 'Oncology and chemotherapy flows according to scope.', 'Flujos de oncología y quimioterapia según el alcance.'),
    ]],
    [tx('Faturamento e financeiro', 'Billing and finance', 'Facturación y financiero'), [
        tx('Faturamento TISS, repasses médicos e controle de glosas.', 'TISS billing, physician payouts and denial control.', 'Facturación TISS, repartos médicos y control de glosas.'),
        tx('Contas a receber, contas a pagar e tesouraria.', 'Accounts receivable, accounts payable and treasury.', 'Cuentas por cobrar, cuentas por pagar y tesorería.'),
        tx('Exportação para contabilidade e integração com o Apure Custos.', 'Export to accounting and integration with Apure Custos.', 'Exportación a contabilidad e integración con Apure Custos.'),
    ]],
    [tx('Compras e suprimentos', 'Purchasing and supplies', 'Compras e insumos'), [
        tx('Consolidação de solicitações e apoio a cotações de materiais e medicamentos.', 'Consolidation of requests and support for material and medication quotes.', 'Consolidación de solicitudes y apoyo a cotizaciones de materiales y medicamentos.'),
        tx('Gestão de estoque e farmácia.', 'Inventory and pharmacy management.', 'Gestión de inventario y farmacia.'),
        tx('Etiquetagem por lote e integração com equipamentos de desblistagem conforme projeto.', 'Batch labeling and integration with blister-packaging equipment according to project.', 'Etiquetado por lote e integración con equipos de desblisterado según proyecto.'),
    ]],
    [tx('Operação e patrimônio', 'Operations and assets', 'Operación y patrimonio'), [
        tx('Gestão de patrimônio, manutenção e serviços.', 'Asset, maintenance and service management.', 'Gestión de patrimonio, mantenimiento y servicios.'),
        tx('Administração de infraestrutura dedicada ou virtual conforme a necessidade.', 'Administration of dedicated or virtual infrastructure as needed.', 'Administración de infraestructura dedicada o virtual según la necesidad.'),
    ]],
    [tx('Indicadores e BI', 'Indicators and BI', 'Indicadores y BI'), [
        tx('Painéis desenvolvidos em Power BI.', 'Dashboards built in Power BI.', 'Paneles desarrollados en Power BI.'),
        tx('Estruturas de dados disponibilizadas para evolução de dimensões, medidas e análises pela equipe do cliente.', 'Data structures made available for the client\'s team to evolve dimensions, measures and analyses.', 'Estructuras de datos disponibles para que el equipo del cliente evolucione dimensiones, medidas y análisis.'),
    ]],
];
?>
<!DOCTYPE html>
<html lang="<?= $LANG ?>">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= tx('Sistema de Gestão para Clínicas e Policlínicas | TechSallus', 'Management System for Clinics and Polyclinics | TechSallus', 'Sistema de Gestión para Clínicas y Policlínicas | TechSallus') ?></title>
<meta name="description" content="<?= tx('Conecte agendamento, recepção, acolhimento, atendimento, exames, faturamento, estoque, farmácia, financeiro e BI em uma única operação.', 'Connect scheduling, front desk, intake, care, exams, billing, inventory, pharmacy, finance and BI in a single operation.', 'Conecte agenda, recepción, acogida, atención, exámenes, facturación, inventario, farmacia, financiero y BI en una sola operación.') ?>"/>
<link rel="icon" type="image/png" href="/assets/img/favicon.png"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<link rel="stylesheet" href="/assets/css/main.css?v=20260731f"/>
<?= getScripts('head') ?>
</head>
<body>
<?= getScripts('body') ?>

<?php include __DIR__ . '/_partials/nav.php'; ?>

<main>

<section class="subhero">
  <div class="wrap">
    <span class="eyebrow reveal"><?= tx('Alto volume com mais fluidez e controle', 'High volume with more fluidity and control', 'Alto volumen con más fluidez y control') ?></span>
    <h1 class="reveal"><?= tx('Atenda mais pacientes sem perder a visão do fluxo, do faturamento e dos recursos.', 'See more patients without losing sight of flow, billing and resources.', 'Atienda a más pacientes sin perder la visión del flujo, la facturación y los recursos.') ?></h1>
    <p class="reveal"><?= tx('A TechSallus conecta agendamento, confirmação, check-in, acolhimento, atendimento, exames, faturamento e gestão para reduzir filas, retrabalho e perdas entre os setores.', 'TechSallus connects scheduling, confirmation, check-in, intake, care, exams, billing and management to reduce queues, rework and losses between departments.', 'TechSallus conecta agenda, confirmación, check-in, acogida, atención, exámenes, facturación y gestión para reducir filas, retrabajo y pérdidas entre los sectores.') ?></p>
    <div class="hero-ctas reveal"><a href="/contato" class="btn btn-primary"><?= tx('Quero melhorar o fluxo da minha clínica', 'I want to improve my clinic\'s flow', 'Quiero mejorar el flujo de mi clínica') ?></a></div>
    <div class="hero-tag reveal"><?= tx('Solução modular para clínicas de múltiplas especialidades, policlínicas e operações com alto volume de atendimento', 'A modular solution for multi-specialty clinics, polyclinics and high-volume operations', 'Solución modular para clínicas de múltiples especialidades, policlínicas y operaciones de alto volumen') ?></div>
  </div>
</section>

<section class="section tint">
  <div class="wrap">
    <div class="section-head center">
      <span class="eyebrow reveal"><?= tx('O que o volume expõe', 'What volume exposes', 'Lo que el volumen expone') ?></span>
      <h2 class="reveal"><?= tx('Quando o volume cresce, pequenas rupturas viram grandes gargalos.', 'As volume grows, small breakdowns become big bottlenecks.', 'Cuando el volumen crece, pequeñas rupturas se convierten en grandes cuellos de botella.') ?></h2>
      <p class="reveal"><?= tx('Uma confirmação que não chega, um cadastro repetido, uma guia inconsistente ou um material sem rastreabilidade pode se multiplicar centenas de vezes ao longo do mês.', 'A confirmation that doesn\'t arrive, a duplicated registration, an inconsistent claim or an untraceable supply can multiply hundreds of times over a month.', 'Una confirmación que no llega, un registro repetido, una guía inconsistente o un material sin trazabilidad puede multiplicarse cientos de veces en el mes.') ?></p>
    </div>
    <div class="grid-6">
      <?php foreach ($_benefits as [$_i, $_t, $_d]): ?>
        <div class="card reveal"><div class="icon-badge"><?= ic($_i) ?></div><h3><?= $_t ?></h3><p><?= $_d ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="section-head center">
      <span class="eyebrow reveal"><?= tx('O fluxo conectado', 'The connected flow', 'El flujo conectado') ?></span>
      <h2 class="reveal"><?= tx('Da confirmação ao resultado da clínica.', 'From confirmation to the clinic\'s results.', 'De la confirmación al resultado de la clínica.') ?></h2>
    </div>
    <div class="journey">
      <?php foreach ($_journey as $_n => [$_t, $_d]): ?>
        <div class="j-step reveal"><div class="j-num"><?= $_n + 1 ?></div><div class="j-body"><h3><?= $_t ?></h3><p><?= $_d ?></p></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section tint">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow reveal"><?= tx('Recursos disponíveis', 'Available features', 'Recursos disponibles') ?></span>
      <h2 class="reveal"><?= tx('Uma operação completa, por frente de trabalho.', 'A complete operation, by area of work.', 'Una operación completa, por frente de trabajo.') ?></h2>
    </div>
    <div class="resource-table">
      <?php foreach ($_resources as [$_h, $_items]): ?>
        <div class="resource-row reveal"><div class="rr-head"><?= $_h ?></div><div class="rr-body"><ul><?php foreach ($_items as $_it): ?><li><?= $_it ?></li><?php endforeach; ?></ul></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section band-dark" id="cta">
  <div class="wrap">
    <span class="eyebrow reveal"><?= tx('Mais pacientes não precisam significar mais caos', 'More patients doesn\'t have to mean more chaos', 'Más pacientes no tiene que significar más caos') ?></span>
    <h2 class="reveal"><?= tx('Cresça sem multiplicar controles manuais entre setores.', 'Grow without multiplying manual controls between departments.', 'Crezca sin multiplicar controles manuales entre sectores.') ?></h2>
    <p class="reveal"><?= tx('Com as etapas conectadas, a clínica ganha capacidade para crescer sem multiplicar retrabalho.', 'With connected steps, the clinic gains the ability to grow without multiplying rework.', 'Con las etapas conectadas, la clínica gana capacidad para crecer sin multiplicar el retrabajo.') ?></p>
    <a href="/contato" class="btn btn-primary reveal"><?= tx('Conhecer a solução para clínicas', 'See the solution for clinics', 'Conocer la solución para clínicas') ?></a>
  </div>
</section>

</main>

<?php include __DIR__ . '/_partials/footer.php'; ?>
<script src="/assets/js/main.js?v=20260731f"></script>
</body>
</html>
