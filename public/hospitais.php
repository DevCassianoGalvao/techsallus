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
  <title><?= tx('Sistema de Gestão Hospitalar e Pronto Atendimento | TechSallus', 'Hospital and Urgent Care Management System | TechSallus', 'Sistema de Gestión Hospitalaria y Urgencias | TechSallus') ?></title>
  <meta name="description" content="<?= tx('Integre triagem, internação, centro cirúrgico, prescrição, beira leito, farmácia, faturamento e gestão hospitalar em uma jornada conectada.', 'Integrate triage, admission, surgical center, prescriptions, bedside care, pharmacy, billing and hospital management in one connected journey.', 'Integre triaje, internación, centro quirúrgico, prescripción, cabecera, farmacia, facturación y gestión hospitalaria en un recorrido conectado.') ?>"/>

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
      <div class="hero-label"><div class="hero-label-bar"></div><span><?= tx('Coordenação para uma operação em que cada minuto importa', 'Coordination for an operation where every minute matters', 'Coordinación para una operación donde cada minuto importa') ?></span></div>
      <h1 class="hero-headline"><?= tx('Conecte a complexidade hospitalar sem perder tempo, informação ou receita.', 'Connect hospital complexity without losing time, information or revenue.', 'Conecte la complejidad hospitalaria sin perder tiempo, información ni ingresos.') ?></h1>
      <p class="page-hero-sub"><?= tx('A TechSallus integra urgência, triagem, atendimento, internação, centro cirúrgico, prescrição, beira leito, farmácia, exames, faturamento e gestão em uma jornada contínua.', 'TechSallus integrates urgency, triage, care, admission, surgical center, prescriptions, bedside care, pharmacy, exams, billing and management in one continuous journey.', 'TechSallus integra urgencia, triaje, atención, internación, centro quirúrgico, prescripción, cabecera, farmacia, exámenes, facturación y gestión en un recorrido continuo.') ?></p>
      <div class="hero-ctas">
        <a href="/contato" class="btn-primary"><?= tx('Quero integrar meu hospital', 'I want to integrate my hospital', 'Quiero integrar mi hospital') ?></a>
      </div>
      <p class="hero-caption"><?= tx('Solução dimensionada para hospitais e serviços de pronto atendimento de pequeno, médio e grande porte.', 'A solution sized for small, medium and large hospitals and urgent care services.', 'Solución dimensionada para hospitales y servicios de urgencias de pequeño, mediano y gran porte.') ?></p>
    </div>
  </div>
</section>

<section class="page-section">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('No hospital, um dado parado em um setor vira atraso em outro.', 'In a hospital, data stuck in one department becomes a delay in another.', 'En el hospital, un dato detenido en un sector se convierte en atraso en otro.') ?></h2>
    <p class="page-section-sub"><?= tx('A assistência depende de muitas equipes, protocolos e recursos trabalhando em sincronia. Quando informações clínicas, leitos, prescrições, farmácia e faturamento não se conectam, surgem rupturas que afetam o paciente e o resultado. A TechSallus organiza esses fluxos em uma visão única da operação.', 'Care depends on many teams, protocols and resources working in sync. When clinical information, beds, prescriptions, pharmacy and billing don\'t connect, breakdowns emerge that affect the patient and the results. TechSallus organizes these flows into a single view of the operation.', 'La asistencia depende de muchos equipos, protocolos y recursos trabajando en sincronía. Cuando la información clínica, las camas, las prescripciones, la farmacia y la facturación no se conectan, surgen rupturas que afectan al paciente y al resultado. TechSallus organiza estos flujos en una visión única de la operación.') ?></p>
    <div class="card-grid card-grid-3">
      <div class="card"><div class="card-title"><?= tx('Urgência mais coordenada', 'More coordinated urgent care', 'Urgencia más coordinada') ?></div><p class="card-desc"><?= tx('Senhas, triagem, protocolos, cores e tempos ajudam a priorizar o atendimento de acordo com o perfil do paciente.', 'Tickets, triage, protocols, color codes and timers help prioritize care according to the patient\'s profile.', 'Turnos, triaje, protocolos, colores y tiempos ayudan a priorizar la atención según el perfil del paciente.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Internação com visibilidade', 'Admissions with visibility', 'Internación con visibilidad') ?></div><p class="card-desc"><?= tx('Pedidos, censo, especialidades, centro cirúrgico e previsão de alta ficam conectados à jornada.', 'Orders, census, specialties, surgical center and discharge forecasts stay connected to the journey.', 'Pedidos, censo, especialidades, centro quirúrgico y previsión de alta quedan conectados al recorrido.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Assistência no ponto de cuidado', 'Care at the point of care', 'Asistencia en el punto de cuidado') ?></div><p class="card-desc"><?= tx('Prontuário, prescrição, beira leito, balanço hídrico e checagem apoiam a continuidade do cuidado.', 'Medical records, prescriptions, bedside care, fluid balance and checks support continuity of care.', 'Historia clínica, prescripción, cabecera, balance hídrico y verificación apoyan la continuidad del cuidado.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Farmácia e suprimentos integrados', 'Integrated pharmacy and supplies', 'Farmacia e insumos integrados') ?></div><p class="card-desc"><?= tx('Compras, estoque, dispensação e automações se relacionam com prescrição e consumo.', 'Purchasing, inventory, dispensing and automation relate to prescriptions and consumption.', 'Compras, inventario, dispensación y automatizaciones se relacionan con la prescripción y el consumo.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Ciclo de receita mais consistente', 'A more consistent revenue cycle', 'Ciclo de ingresos más consistente') ?></div><p class="card-desc"><?= tx('TISS, glosas, repasses, plantões e financeiro acompanham a produção assistencial.', 'TISS, denials, payouts, on-call shifts and finance track clinical production.', 'TISS, glosas, repartos, guardias y financiero acompañan la producción asistencial.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Gestão com indicadores e custos', 'Management with indicators and costs', 'Gestión con indicadores y costos') ?></div><p class="card-desc"><?= tx('BI e Apure Custos conectam a operação hospitalar à análise de desempenho e margem.', 'BI and Apure Custos connect hospital operations to performance and margin analysis.', 'BI y Apure Custos conectan la operación hospitalaria al análisis de desempeño y margen.') ?></p></div>
    </div>
  </div>
</section>

<section class="page-section page-section-tint">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Uma jornada contínua do acolhimento à alta.', 'A continuous journey from intake to discharge.', 'Un recorrido continuo desde la acogida hasta el alta.') ?></h2>
    <div class="numbered-steps">
      <div class="step-item"><div class="step-num">1</div><p class="step-text"><strong><?= tx('Recepção e triagem.', 'Reception and triage.', 'Recepción y triaje.') ?></strong> <?= tx('Organize senhas, chamadas e protocolos configuráveis, com cálculo de prioridade e acompanhamento de tempos.', 'Organize tickets, calling and configurable protocols, with priority calculation and time tracking.', 'Organice turnos, llamadas y protocolos configurables, con cálculo de prioridad y seguimiento de tiempos.') ?></p></div>
      <div class="step-item"><div class="step-num">2</div><p class="step-text"><strong><?= tx('Atendimento e decisão clínica.', 'Care and clinical decision-making.', 'Atención y decisión clínica.') ?></strong> <?= tx('Prontuário, solicitações de especialistas, exames e laudos dão continuidade à avaliação do paciente.', 'Medical records, specialist requests, exams and reports give continuity to the patient\'s assessment.', 'Historia clínica, solicitudes de especialistas, exámenes e informes dan continuidad a la evaluación del paciente.') ?></p></div>
      <div class="step-item"><div class="step-num">3</div><p class="step-text"><strong><?= tx('Internação e centro cirúrgico.', 'Admission and surgical center.', 'Internación y centro quirúrgico.') ?></strong> <?= tx('Pedidos de internação e cirurgia, autorizações, censo, acompanhamento por especialidade e previsão de alta compartilham a mesma informação.', 'Admission and surgery orders, authorizations, census, tracking by specialty and discharge forecasts share the same information.', 'Pedidos de internación y cirugía, autorizaciones, censo, seguimiento por especialidad y previsión de alta comparten la misma información.') ?></p></div>
      <div class="step-item"><div class="step-num">4</div><p class="step-text"><strong><?= tx('Prescrição e beira leito.', 'Prescription and bedside care.', 'Prescripción y cabecera.') ?></strong> <?= tx('Prescrições médica, dietética e quimioterápica se conectam à checagem de medicamentos, ao balanço hídrico e à rotina assistencial.', 'Medical, dietary and chemotherapy prescriptions connect to medication checks, fluid balance and clinical routine.', 'Prescripciones médica, dietética y quimioterápica se conectan a la verificación de medicamentos, el balance hídrico y la rutina asistencial.') ?></p></div>
      <div class="step-item"><div class="step-num">5</div><p class="step-text"><strong><?= tx('Farmácia e dispensação.', 'Pharmacy and dispensing.', 'Farmacia y dispensación.') ?></strong> <?= tx('Estoque, farmácia, lotes, robôs e dispensários automatizados podem integrar o fluxo conforme o projeto.', 'Inventory, pharmacy, batches, robots and automated dispensers can integrate into the flow according to the project.', 'Inventario, farmacia, lotes, robots y dispensarios automatizados pueden integrarse al flujo según el proyecto.') ?></p></div>
      <div class="step-item"><div class="step-num">6</div><p class="step-text"><strong><?= tx('Faturamento, BI e custos.', 'Billing, BI and costs.', 'Facturación, BI y costos.') ?></strong> <?= tx('Produção, TISS, glosas, repasses, financeiro e indicadores formam a base para análise operacional e econômica.', 'Production, TISS, denials, payouts, finance and indicators form the basis for operational and financial analysis.', 'Producción, TISS, glosas, repartos, financiero e indicadores forman la base para el análisis operativo y económico.') ?></p></div>
    </div>
  </div>
</section>

<section class="page-section">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Recursos disponíveis', 'Available features', 'Recursos disponibles') ?></h2>
    <div class="table-wrap">
      <table class="resource-table">
        <thead><tr><th><?= tx('Frente da operação', 'Area of operation', 'Frente de la operación') ?></th><th><?= tx('Recursos disponíveis', 'Available features', 'Recursos disponibles') ?></th></tr></thead>
        <tbody>
          <tr><td><?= tx('Urgência e acolhimento', 'Urgent care and intake', 'Urgencia y acogida') ?></td><td><ul>
            <li><?= tx('Agendamento, confirmação por WhatsApp, distribuição de senhas e chamadas por Smart TV.', 'Scheduling, WhatsApp confirmation, ticket distribution and Smart TV calling.', 'Agenda, confirmación por WhatsApp, distribución de turnos y llamadas por Smart TV.') ?></li>
            <li><?= tx('Triagem com protocolos, cálculo de cores e tempos configuráveis por grupo de pacientes.', 'Triage with protocols, color-code calculation and configurable timers by patient group.', 'Triaje con protocolos, cálculo de colores y tiempos configurables por grupo de pacientes.') ?></li>
            <li><?= tx('Integração HL7 com monitores de parâmetros vitais.', 'HL7 integration with vital-sign monitors.', 'Integración HL7 con monitores de signos vitales.') ?></li>
          </ul></td></tr>
          <tr><td><?= tx('Internação e centro cirúrgico', 'Admission and surgical center', 'Internación y centro quirúrgico') ?></td><td><ul>
            <li><?= tx('Pedidos de internação, cirurgia e especialistas.', 'Admission, surgery and specialist orders.', 'Pedidos de internación, cirugía y especialistas.') ?></li>
            <li><?= tx('Agendamento e autorização de centro cirúrgico.', 'Surgical center scheduling and authorization.', 'Agenda y autorización de centro quirúrgico.') ?></li>
            <li><?= tx('Acompanhamento por especialidade, censo de internados e previsão de alta.', 'Tracking by specialty, inpatient census and discharge forecasts.', 'Seguimiento por especialidad, censo de internados y previsión de alta.') ?></li>
          </ul></td></tr>
          <tr><td><?= tx('Prontuário e assistência', 'Medical records and care', 'Historia clínica y asistencia') ?></td><td><ul>
            <li><?= tx('Prontuário eletrônico orientado pelo método SOAP.', 'Electronic medical record based on the SOAP method.', 'Historia clínica electrónica orientada por el método SOAP.') ?></li>
            <li><?= tx('Prescrição médica, dietética e quimioterápica com ciclos.', 'Medical, dietary and chemotherapy prescriptions with cycles.', 'Prescripción médica, dietética y quimioterápica con ciclos.') ?></li>
            <li><?= tx('Beira leito, balanço hídrico e checagem de medicamentos.', 'Bedside care, fluid balance and medication checks.', 'Cabecera, balance hídrico y verificación de medicamentos.') ?></li>
          </ul></td></tr>
          <tr><td><?= tx('Imagens e laudos', 'Images and reports', 'Imágenes e informes') ?></td><td><ul>
            <li><?= tx('PACS e visualização de imagens DICOM.', 'PACS and DICOM image viewing.', 'PACS y visualización de imágenes DICOM.') ?></li>
            <li><?= tx('Encaminhamento de imagens para outros RIS e fluxos de telerradiologia conforme o projeto.', 'Image referral to other RIS systems and teleradiology flows according to the project.', 'Envío de imágenes a otros RIS y flujos de telerradiología según el proyecto.') ?></li>
            <li><?= tx('Laudos complementares.', 'Complementary reports.', 'Informes complementarios.') ?></li>
          </ul></td></tr>
          <tr><td><?= tx('Faturamento e gestão', 'Billing and management', 'Facturación y gestión') ?></td><td><ul>
            <li><?= tx('Faturamento TISS, controle de glosas, repasses e plantões médicos.', 'TISS billing, denial control, payouts and on-call physician shifts.', 'Facturación TISS, control de glosas, repartos y guardias médicas.') ?></li>
            <li><?= tx('Contas a receber, contas a pagar, tesouraria e exportação contábil.', 'Accounts receivable, accounts payable, treasury and accounting export.', 'Cuentas por cobrar, cuentas por pagar, tesorería y exportación contable.') ?></li>
            <li><?= tx('Integração com o Apure Custos.', 'Integration with Apure Custos.', 'Integración con Apure Custos.') ?></li>
          </ul></td></tr>
          <tr><td><?= tx('Compras, estoque e farmácia', 'Purchasing, inventory and pharmacy', 'Compras, inventario y farmacia') ?></td><td><ul>
            <li><?= tx('Consolidação de solicitações e apoio a cotações de materiais e medicamentos.', 'Consolidation of requests and support for material and medication quotes.', 'Consolidación de solicitudes y apoyo a cotizaciones de materiales y medicamentos.') ?></li>
            <li><?= tx('Estoque, farmácia, etiquetagem por lote e integração com desblistagem.', 'Inventory, pharmacy, batch labeling and blister-packaging integration.', 'Inventario, farmacia, etiquetado por lote e integración con desblisterado.') ?></li>
            <li><?= tx('Integração com robôs de farmácia e dispensários automatizados conforme o projeto.', 'Integration with pharmacy robots and automated dispensers according to the project.', 'Integración con robots de farmacia y dispensarios automatizados según el proyecto.') ?></li>
          </ul></td></tr>
          <tr><td><?= tx('Operação hospitalar', 'Hospital operations', 'Operación hospitalaria') ?></td><td><ul>
            <li><?= tx('Fluxos de oncologia e quimioterapia.', 'Oncology and chemotherapy flows.', 'Flujos de oncología y quimioterapia.') ?></li>
            <li><?= tx('Gestão de patrimônio, manutenção e serviços.', 'Asset, maintenance and service management.', 'Gestión de patrimonio, mantenimiento y servicios.') ?></li>
          </ul></td></tr>
          <tr><td><?= tx('Dados e infraestrutura', 'Data and infrastructure', 'Datos e infraestructura') ?></td><td><ul>
            <li><?= tx('Painéis de BI em Power BI e estruturas para evolução das análises.', 'Power BI dashboards and structures for evolving analysis.', 'Paneles de BI en Power BI y estructuras para la evolución de los análisis.') ?></li>
            <li><?= tx('Infraestrutura em nuvem no Brasil, com opções dedicadas ou virtuais, manutenção, backups e SLA conforme contrato.', 'Cloud infrastructure in Brazil, with dedicated or virtual options, maintenance, backups and SLA according to contract.', 'Infraestructura en la nube en Brasil, con opciones dedicadas o virtuales, mantenimiento, copias de seguridad y SLA según contrato.') ?></li>
          </ul></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

<section class="cta-bar cta-bar-dark">
  <div class="container">
    <h2 class="cta-bar-heading"><?= tx('Conecte a complexidade hospitalar sem perder tempo, informação ou receita.', 'Connect hospital complexity without losing time, information or revenue.', 'Conecte la complejidad hospitalaria sin perder tiempo, información ni ingresos.') ?></h2>
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
