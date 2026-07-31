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
    ['zap', tx('Urgência mais coordenada', 'More coordinated urgent care', 'Urgencia más coordinada'), tx('Senhas, triagem, protocolos, cores e tempos ajudam a priorizar o atendimento de acordo com o perfil do paciente.', 'Tickets, triage, protocols, color codes and timers help prioritize care according to the patient\'s profile.', 'Turnos, triaje, protocolos, colores y tiempos ayudan a priorizar la atención según el perfil del paciente.')],
    ['building', tx('Internação com visibilidade', 'Admissions with visibility', 'Internación con visibilidad'), tx('Pedidos, censo, especialidades, centro cirúrgico e previsão de alta ficam conectados à jornada.', 'Orders, census, specialties, surgical center and discharge forecasts stay connected to the journey.', 'Pedidos, censo, especialidades, centro quirúrgico y previsión de alta quedan conectados al recorrido.')],
    ['heartPulse', tx('Assistência no ponto de cuidado', 'Care at the point of care', 'Asistencia en el punto de cuidado'), tx('Prontuário, prescrição, beira leito, balanço hídrico e checagem apoiam a continuidade do cuidado.', 'Medical records, prescriptions, bedside care, fluid balance and checks support continuity of care.', 'Historia clínica, prescripción, cabecera, balance hídrico y verificación apoyan la continuidad del cuidado.')],
    ['boxCheck', tx('Farmácia e suprimentos integrados', 'Integrated pharmacy and supplies', 'Farmacia e insumos integrados'), tx('Compras, estoque, dispensação e automações se relacionam com prescrição e consumo.', 'Purchasing, inventory, dispensing and automation relate to prescriptions and consumption.', 'Compras, inventario, dispensación y automatizaciones se relacionan con la prescripción y el consumo.')],
    ['receiptCheck', tx('Ciclo de receita mais consistente', 'A more consistent revenue cycle', 'Ciclo de ingresos más consistente'), tx('TISS, glosas, repasses, plantões e financeiro acompanham a produção assistencial.', 'TISS, denials, payouts, on-call shifts and finance track clinical production.', 'TISS, glosas, repartos, guardias y financiero acompañan la producción asistencial.')],
    ['chartUp', tx('Gestão com indicadores e custos', 'Management with indicators and costs', 'Gestión con indicadores y costos'), tx('BI e Apure Custos conectam a operação hospitalar à análise de desempenho e margem.', 'BI and Apure Custos connect hospital operations to performance and margin analysis.', 'BI y Apure Custos conectan la operación hospitalaria al análisis de desempeño y margen.')],
];

$_journey = [
    [tx('Recepção e triagem', 'Reception and triage', 'Recepción y triaje'), tx('Organize senhas, chamadas e protocolos configuráveis, com cálculo de prioridade e acompanhamento de tempos.', 'Organize tickets, calling and configurable protocols, with priority calculation and time tracking.', 'Organice turnos, llamadas y protocolos configurables, con cálculo de prioridad y seguimiento de tiempos.')],
    [tx('Atendimento e decisão clínica', 'Care and clinical decision-making', 'Atención y decisión clínica'), tx('Prontuário, solicitações de especialistas, exames e laudos dão continuidade à avaliação do paciente.', 'Medical records, specialist requests, exams and reports give continuity to the patient\'s assessment.', 'Historia clínica, solicitudes de especialistas, exámenes e informes dan continuidad a la evaluación del paciente.')],
    [tx('Internação e centro cirúrgico', 'Admission and surgical center', 'Internación y centro quirúrgico'), tx('Pedidos de internação e cirurgia, autorizações, censo, acompanhamento por especialidade e previsão de alta compartilham a mesma informação.', 'Admission and surgery orders, authorizations, census, tracking by specialty and discharge forecasts share the same information.', 'Pedidos de internación y cirugía, autorizaciones, censo, seguimiento por especialidad y previsión de alta comparten la misma información.')],
    [tx('Prescrição e beira leito', 'Prescription and bedside care', 'Prescripción y cabecera'), tx('Prescrições médica, dietética e quimioterápica se conectam à checagem de medicamentos, ao balanço hídrico e à rotina assistencial.', 'Medical, dietary and chemotherapy prescriptions connect to medication checks, fluid balance and clinical routine.', 'Prescripciones médica, dietética y quimioterápica se conectan a la verificación de medicamentos, el balance hídrico y la rutina asistencial.')],
    [tx('Farmácia e dispensação', 'Pharmacy and dispensing', 'Farmacia y dispensación'), tx('Estoque, farmácia, lotes, robôs e dispensários automatizados podem integrar o fluxo conforme o projeto.', 'Inventory, pharmacy, batches, robots and automated dispensers can integrate into the flow according to the project.', 'Inventario, farmacia, lotes, robots y dispensarios automatizados pueden integrarse al flujo según el proyecto.')],
    [tx('Faturamento, BI e custos', 'Billing, BI and costs', 'Facturación, BI y costos'), tx('Produção, TISS, glosas, repasses, financeiro e indicadores formam a base para análise operacional e econômica.', 'Production, TISS, denials, payouts, finance and indicators form the basis for operational and financial analysis.', 'Producción, TISS, glosas, repartos, financiero e indicadores forman la base para el análisis operativo y económico.')],
];

$_resources = [
    [tx('Urgência e acolhimento', 'Urgent care and intake', 'Urgencia y acogida'), [
        tx('Agendamento, confirmação por WhatsApp, distribuição de senhas e chamadas por Smart TV.', 'Scheduling, WhatsApp confirmation, ticket distribution and Smart TV calling.', 'Agenda, confirmación por WhatsApp, distribución de turnos y llamadas por Smart TV.'),
        tx('Triagem com protocolos, cálculo de cores e tempos configuráveis por grupo de pacientes.', 'Triage with protocols, color-code calculation and configurable timers by patient group.', 'Triaje con protocolos, cálculo de colores y tiempos configurables por grupo de pacientes.'),
        tx('Integração HL7 com monitores de parâmetros vitais.', 'HL7 integration with vital-sign monitors.', 'Integración HL7 con monitores de signos vitales.'),
    ]],
    [tx('Internação e centro cirúrgico', 'Admission and surgical center', 'Internación y centro quirúrgico'), [
        tx('Pedidos de internação, cirurgia e especialistas.', 'Admission, surgery and specialist orders.', 'Pedidos de internación, cirugía y especialistas.'),
        tx('Agendamento e autorização de centro cirúrgico.', 'Surgical center scheduling and authorization.', 'Agenda y autorización de centro quirúrgico.'),
        tx('Acompanhamento por especialidade, censo de internados e previsão de alta.', 'Tracking by specialty, inpatient census and discharge forecasts.', 'Seguimiento por especialidad, censo de internados y previsión de alta.'),
    ]],
    [tx('Prontuário e assistência', 'Medical records and care', 'Historia clínica y asistencia'), [
        tx('Prontuário eletrônico orientado pelo método SOAP.', 'Electronic medical record based on the SOAP method.', 'Historia clínica electrónica orientada por el método SOAP.'),
        tx('Prescrição médica, dietética e quimioterápica com ciclos.', 'Medical, dietary and chemotherapy prescriptions with cycles.', 'Prescripción médica, dietética y quimioterápica con ciclos.'),
        tx('Beira leito, balanço hídrico e checagem de medicamentos.', 'Bedside care, fluid balance and medication checks.', 'Cabecera, balance hídrico y verificación de medicamentos.'),
    ]],
    [tx('Imagens e laudos', 'Images and reports', 'Imágenes e informes'), [
        tx('PACS e visualização de imagens DICOM.', 'PACS and DICOM image viewing.', 'PACS y visualización de imágenes DICOM.'),
        tx('Encaminhamento de imagens para outros RIS e fluxos de telerradiologia conforme o projeto.', 'Image referral to other RIS systems and teleradiology flows according to the project.', 'Envío de imágenes a otros RIS y flujos de telerradiología según el proyecto.'),
        tx('Laudos complementares.', 'Complementary reports.', 'Informes complementarios.'),
    ]],
    [tx('Faturamento e gestão', 'Billing and management', 'Facturación y gestión'), [
        tx('Faturamento TISS, controle de glosas, repasses e plantões médicos.', 'TISS billing, denial control, payouts and on-call physician shifts.', 'Facturación TISS, control de glosas, repartos y guardias médicas.'),
        tx('Contas a receber, contas a pagar, tesouraria e exportação contábil.', 'Accounts receivable, accounts payable, treasury and accounting export.', 'Cuentas por cobrar, cuentas por pagar, tesorería y exportación contable.'),
        tx('Integração com o Apure Custos.', 'Integration with Apure Custos.', 'Integración con Apure Custos.'),
    ]],
    [tx('Compras, estoque e farmácia', 'Purchasing, inventory and pharmacy', 'Compras, inventario y farmacia'), [
        tx('Consolidação de solicitações e apoio a cotações de materiais e medicamentos.', 'Consolidation of requests and support for material and medication quotes.', 'Consolidación de solicitudes y apoyo a cotizaciones de materiales y medicamentos.'),
        tx('Estoque, farmácia, etiquetagem por lote e integração com desblistagem.', 'Inventory, pharmacy, batch labeling and blister-packaging integration.', 'Inventario, farmacia, etiquetado por lote e integración con desblisterado.'),
        tx('Integração com robôs de farmácia e dispensários automatizados conforme o projeto.', 'Integration with pharmacy robots and automated dispensers according to the project.', 'Integración con robots de farmacia y dispensarios automatizados según el proyecto.'),
    ]],
    [tx('Operação hospitalar', 'Hospital operations', 'Operación hospitalaria'), [
        tx('Fluxos de oncologia e quimioterapia.', 'Oncology and chemotherapy flows.', 'Flujos de oncología y quimioterapia.'),
        tx('Gestão de patrimônio, manutenção e serviços.', 'Asset, maintenance and service management.', 'Gestión de patrimonio, mantenimiento y servicios.'),
    ]],
    [tx('Dados e infraestrutura', 'Data and infrastructure', 'Datos e infraestructura'), [
        tx('Painéis de BI em Power BI e estruturas para evolução das análises.', 'Power BI dashboards and structures for evolving analysis.', 'Paneles de BI en Power BI y estructuras para la evolución de los análisis.'),
        tx('Infraestrutura em nuvem no Brasil, com opções dedicadas ou virtuais, manutenção, backups e SLA conforme contrato.', 'Cloud infrastructure in Brazil, with dedicated or virtual options, maintenance, backups and SLA according to contract.', 'Infraestructura en la nube en Brasil, con opciones dedicadas o virtuales, mantenimiento, copias de seguridad y SLA según contrato.'),
    ]],
];
?>
<!DOCTYPE html>
<html lang="<?= $LANG ?>">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= tx('Sistema de Gestão Hospitalar e Pronto Atendimento | TechSallus', 'Hospital and Urgent Care Management System | TechSallus', 'Sistema de Gestión Hospitalaria y Urgencias | TechSallus') ?></title>
<meta name="description" content="<?= tx('Integre triagem, internação, centro cirúrgico, prescrição, beira leito, farmácia, faturamento e gestão hospitalar em uma jornada conectada.', 'Integrate triage, admission, surgical center, prescriptions, bedside care, pharmacy, billing and hospital management in one connected journey.', 'Integre triaje, internación, centro quirúrgico, prescripción, cabecera, farmacia, facturación y gestión hospitalaria en un recorrido conectado.') ?>"/>
<link rel="icon" type="image/png" href="/assets/img/favicon.png"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<link rel="stylesheet" href="/assets/css/main.css?v=20260731c"/>
<?= getScripts('head') ?>
</head>
<body>
<?= getScripts('body') ?>

<?php include __DIR__ . '/_partials/nav.php'; ?>

<main>

<section class="subhero">
  <div class="wrap">
    <span class="eyebrow reveal"><?= tx('Coordenação para uma operação em que cada minuto importa', 'Coordination for an operation where every minute matters', 'Coordinación para una operación donde cada minuto importa') ?></span>
    <h1 class="reveal"><?= tx('Conecte a complexidade hospitalar sem perder tempo, informação ou receita.', 'Connect hospital complexity without losing time, information or revenue.', 'Conecte la complejidad hospitalaria sin perder tiempo, información ni ingresos.') ?></h1>
    <p class="reveal"><?= tx('A TechSallus integra urgência, triagem, atendimento, internação, centro cirúrgico, prescrição, beira leito, farmácia, exames, faturamento e gestão em uma jornada contínua.', 'TechSallus integrates urgency, triage, care, admission, surgical center, prescriptions, bedside care, pharmacy, exams, billing and management in one continuous journey.', 'TechSallus integra urgencia, triaje, atención, internación, centro quirúrgico, prescripción, cabecera, farmacia, exámenes, facturación y gestión en un recorrido continuo.') ?></p>
    <div class="hero-ctas reveal"><a href="/contato" class="btn btn-primary"><?= tx('Quero integrar meu hospital', 'I want to integrate my hospital', 'Quiero integrar mi hospital') ?></a></div>
    <div class="hero-tag reveal"><?= tx('Solução dimensionada para hospitais e serviços de pronto atendimento de pequeno, médio e grande porte', 'A solution sized for small, medium and large hospitals and urgent care services', 'Solución dimensionada para hospitales y servicios de urgencias de pequeño, mediano y gran porte') ?></div>
  </div>
</section>

<section class="section tint">
  <div class="wrap">
    <div class="section-head center">
      <span class="eyebrow reveal"><?= tx('O custo de um dado parado', 'The cost of stalled data', 'El costo de un dato detenido') ?></span>
      <h2 class="reveal"><?= tx('No hospital, um dado parado em um setor vira atraso em outro.', 'In a hospital, data stuck in one department becomes a delay in another.', 'En el hospital, un dato detenido en un sector se convierte en atraso en otro.') ?></h2>
      <p class="reveal"><?= tx('Quando informações clínicas, leitos, prescrições, farmácia e faturamento não se conectam, surgem rupturas que afetam o paciente e o resultado.', 'When clinical information, beds, prescriptions, pharmacy and billing don\'t connect, breakdowns emerge that affect the patient and the results.', 'Cuando la información clínica, las camas, las prescripciones, la farmacia y la facturación no se conectan, surgen rupturas que afectan al paciente y al resultado.') ?></p>
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
      <span class="eyebrow reveal"><?= tx('A jornada hospitalar', 'The hospital journey', 'El recorrido hospitalario') ?></span>
      <h2 class="reveal"><?= tx('Uma jornada contínua do acolhimento à alta.', 'A continuous journey from intake to discharge.', 'Un recorrido continuo desde la acogida hasta el alta.') ?></h2>
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
      <h2 class="reveal"><?= tx('Cobertura completa da operação hospitalar.', 'Complete coverage of hospital operations.', 'Cobertura completa de la operación hospitalaria.') ?></h2>
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
    <span class="eyebrow reveal"><?= tx('Pronto para integrar sua operação?', 'Ready to integrate your operation?', '¿Listo para integrar su operación?') ?></span>
    <h2 class="reveal"><?= tx('Conecte urgência, internação, farmácia e faturamento em uma visão única.', 'Connect urgency, admission, pharmacy and billing in a single view.', 'Conecte urgencia, internación, farmacia y facturación en una visión única.') ?></h2>
    <p class="reveal"><?= tx('Fale com um especialista e entenda como dimensionar a solução para o porte do seu hospital ou pronto atendimento.', 'Talk to a specialist and understand how to size the solution for your hospital or urgent care unit.', 'Hable con un especialista y entienda cómo dimensionar la solución para el porte de su hospital o urgencias.') ?></p>
    <a href="/contato" class="btn btn-primary reveal"><?= tx('Quero integrar meu hospital', 'I want to integrate my hospital', 'Quiero integrar mi hospital') ?></a>
  </div>
</section>

</main>

<?php include __DIR__ . '/_partials/footer.php'; ?>
<script src="/assets/js/main.js?v=20260731c"></script>
</body>
</html>
