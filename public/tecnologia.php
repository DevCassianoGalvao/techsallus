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
  <title><?= tx('Tecnologia, Integrações e Segurança para Saúde | TechSallus', 'Technology, Integrations and Security for Healthcare | TechSallus', 'Tecnología, Integraciones y Seguridad para Salud | TechSallus') ?></title>
  <meta name="description" content="<?= tx('Conheça os recursos de prontuário, TISS/TUSS, PACS/DICOM, HL7, nuvem, backups e BI que sustentam uma operação de saúde conectada.', 'Learn about the medical records, TISS/TUSS, PACS/DICOM, HL7, cloud, backup and BI capabilities that sustain a connected healthcare operation.', 'Conozca los recursos de historia clínica, TISS/TUSS, PACS/DICOM, HL7, nube, copias de seguridad y BI que sostienen una operación de salud conectada.') ?>"/>

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
      <div class="hero-label"><div class="hero-label-bar"></div><span><?= tx('Robustez técnica a serviço da operação', 'Technical robustness at the service of the operation', 'Robustez técnica al servicio de la operación') ?></span></div>
      <h1 class="hero-headline"><?= tx('Integração e continuidade para uma instituição de saúde que não pode parar.', 'Integration and continuity for a health institution that cannot stop.', 'Integración y continuidad para una institución de salud que no puede detenerse.') ?></h1>
      <p class="page-hero-sub"><?= tx('A TechSallus combina recursos clínicos, administrativos e financeiros com padrões de interoperabilidade, infraestrutura em nuvem, rotinas de continuidade e capacidade analítica.', 'TechSallus combines clinical, administrative and financial capabilities with interoperability standards, cloud infrastructure, continuity routines and analytical capacity.', 'TechSallus combina recursos clínicos, administrativos y financieros con estándares de interoperabilidad, infraestructura en la nube, rutinas de continuidad y capacidad analítica.') ?></p>
      <div class="hero-ctas">
        <a href="/contato" class="btn-primary"><?= tx('Falar com um especialista técnico', 'Talk to a technical specialist', 'Hablar con un especialista técnico') ?></a>
      </div>
      <p class="hero-caption"><?= tx('A arquitetura, as integrações e os serviços são definidos conforme o perfil e o escopo de cada projeto.', 'Architecture, integrations and services are defined according to the profile and scope of each project.', 'La arquitectura, las integraciones y los servicios se definen según el perfil y el alcance de cada proyecto.') ?></p>
    </div>
  </div>
</section>

<section class="page-section">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Prontuário e informação clínica', 'Medical records and clinical information', 'Historia clínica e información clínica') ?></h2>
    <p class="page-section-sub"><?= tx('O prontuário eletrônico orientado pelo método SOAP organiza relatos, achados, avaliação e plano de cuidado em uma estrutura clara. Dados clínicos, laudos, prescrições e evoluções permanecem vinculados à jornada do paciente.', 'The electronic medical record based on the SOAP method organizes subjective and objective findings, assessment and care plan in a clear structure. Clinical data, reports, prescriptions and progress notes stay linked to the patient\'s journey.', 'La historia clínica electrónica orientada por el método SOAP organiza relatos, hallazgos, evaluación y plan de cuidado en una estructura clara. Los datos clínicos, informes, prescripciones y evoluciones permanecen vinculados al recorrido del paciente.') ?></p>
    <div class="card-grid card-grid-2">
      <div class="card"><div class="card-title"><?= tx('Segurança e rastreabilidade', 'Security and traceability', 'Seguridad y trazabilidad') ?></div><p class="card-desc"><?= tx('Perfis de acesso, registros e requisitos de segurança apoiam o uso responsável da informação clínica.', 'Access profiles, logs and security requirements support the responsible use of clinical information.', 'Perfiles de acceso, registros y requisitos de seguridad apoyan el uso responsable de la información clínica.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('Continuidade do cuidado', 'Continuity of care', 'Continuidad del cuidado') ?></div><p class="card-desc"><?= tx('A equipe encontra dados relevantes no ponto de atendimento e reduz a dependência de documentos ou controles separados.', 'The team finds relevant data at the point of care and reduces dependency on separate documents or controls.', 'El equipo encuentra datos relevantes en el punto de atención y reduce la dependencia de documentos o controles separados.') ?></p></div>
    </div>
    <div class="card" style="margin-top:20px;max-width:520px">
      <div class="card-title"><?= tx('Certificação S-RES apresentada', 'S-RES certification presented', 'Certificación S-RES presentada') ?></div>
      <p class="card-desc"><?= tx('PEP TechSallus v15.0.0, Clínica/Ambulatório, Estágio 1 - NGS2, certificado nº 100, versão 5.2.', 'PEP TechSallus v15.0.0, Clinic/Outpatient, Stage 1 - NGS2, certificate No. 100, version 5.2.', 'PEP TechSallus v15.0.0, Clínica/Ambulatorio, Etapa 1 - NGS2, certificado n.º 100, versión 5.2.') ?></p>
    </div>
  </div>
</section>

<section class="page-section page-section-tint">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Interoperabilidade com imagens, sistemas e equipamentos', 'Interoperability with images, systems and equipment', 'Interoperabilidad con imágenes, sistemas y equipos') ?></h2>
    <div class="card-grid card-grid-2">
      <div class="card"><div class="card-title"><?= tx('PACS e DICOM', 'PACS and DICOM', 'PACS y DICOM') ?></div><p class="card-desc"><?= tx('Armazene, organize, visualize e compartilhe exames de imagem dentro do fluxo assistencial.', 'Store, organize, view and share imaging exams within the clinical flow.', 'Almacene, organice, visualice y comparta exámenes de imagen dentro del flujo asistencial.') ?></p></div>
      <div class="card"><div class="card-title"><?= tx('RIS e telerradiologia', 'RIS and teleradiology', 'RIS y telerradiología') ?></div><p class="card-desc"><?= tx('Encaminhe imagens e conecte o fluxo de radiologia do agendamento à emissão de laudos, conforme o projeto.', 'Route images and connect the radiology flow from scheduling to report issuance, according to the project.', 'Envíe imágenes y conecte el flujo de radiología desde la agenda hasta la emisión de informes, según el proyecto.') ?></p></div>
      <div class="card"><div class="card-title">HL7</div><p class="card-desc"><?= tx('Integre monitores de parâmetros vitais e outros equipamentos para reduzir digitação manual e dar continuidade aos dados.', 'Integrate vital-sign monitors and other equipment to reduce manual data entry and give continuity to data.', 'Integre monitores de signos vitales y otros equipos para reducir la digitación manual y dar continuidad a los datos.') ?></p></div>
      <div class="card"><div class="card-title">HIS e LIS</div><p class="card-desc"><?= tx('Conecte informações hospitalares, clínicas e laboratoriais de acordo com as interfaces e o escopo definidos.', 'Connect hospital, clinical and laboratory information according to the defined interfaces and scope.', 'Conecte información hospitalaria, clínica y de laboratorio de acuerdo con las interfaces y el alcance definidos.') ?></p></div>
    </div>
  </div>
</section>

<section class="page-section">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Infraestrutura, continuidade e acesso', 'Infrastructure, continuity and access', 'Infraestructura, continuidad y acceso') ?></h2>
    <div class="card-grid card-grid-2">
      <div class="card">
        <div class="card-title"><?= tx('Operação em nuvem', 'Cloud operation', 'Operación en la nube') ?></div>
        <ul style="margin-top:8px;display:flex;flex-direction:column;gap:6px">
          <li class="card-desc"><?= tx('Infraestrutura no Brasil conforme a modalidade', 'Infrastructure in Brazil according to the plan', 'Infraestructura en Brasil según la modalidad') ?></li>
          <li class="card-desc"><?= tx('Opções de ambiente dedicado ou virtual', 'Dedicated or virtual environment options', 'Opciones de entorno dedicado o virtual') ?></li>
          <li class="card-desc"><?= tx('Configuração e manutenção técnica', 'Technical configuration and maintenance', 'Configuración y mantenimiento técnico') ?></li>
          <li class="card-desc"><?= tx('Atualizações de sistema operacional e banco de dados', 'Operating system and database updates', 'Actualizaciones de sistema operativo y base de datos') ?></li>
        </ul>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Continuidade do serviço', 'Service continuity', 'Continuidad del servicio') ?></div>
        <ul style="margin-top:8px;display:flex;flex-direction:column;gap:6px">
          <li class="card-desc"><?= tx('Rotinas de backup de banco de dados e aplicações', 'Database and application backup routines', 'Rutinas de copia de seguridad de base de datos y aplicaciones') ?></li>
          <li class="card-desc"><?= tx('Arquitetura definida conforme o porte e o projeto', 'Architecture defined according to size and project', 'Arquitectura definida según el porte y el proyecto') ?></li>
          <li class="card-desc"><?= tx('SLA de disponibilidade conforme contrato', 'Availability SLA according to contract', 'SLA de disponibilidad según contrato') ?></li>
          <li class="card-desc"><?= tx('Acesso multiplataforma nos escopos compatíveis', 'Multi-platform access in compatible scopes', 'Acceso multiplataforma en los alcances compatibles') ?></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="page-section page-section-tint">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Dados para acompanhar e aprofundar a gestão', 'Data to track and deepen management', 'Datos para acompañar y profundizar la gestión') ?></h2>
    <p class="page-section-sub"><?= tx('Painéis desenvolvidos em Power BI transformam dados operacionais em indicadores. As estruturas de dados podem ser disponibilizadas para que a equipe técnica do cliente amplie dimensões, medidas e análises conforme a necessidade.', 'Dashboards built in Power BI turn operational data into indicators. Data structures can be made available so the client\'s technical team can expand dimensions, measures and analysis as needed.', 'Los paneles desarrollados en Power BI transforman los datos operativos en indicadores. Las estructuras de datos pueden ponerse a disposición para que el equipo técnico del cliente amplíe dimensiones, medidas y análisis según la necesidad.') ?></p>
  </div>
</section>

<section class="cta-bar cta-bar-dark">
  <div class="container">
    <h2 class="cta-bar-heading"><?= tx('Tecnologia como meio, resultado como objetivo', 'Technology as the means, results as the goal', 'Tecnología como medio, resultado como objetivo') ?></h2>
    <p class="page-section-sub" style="margin:-8px auto 24px"><?= tx('Cada padrão, integração e componente de infraestrutura tem um papel prático: reduzir digitação duplicada, preservar a continuidade da informação e dar mais confiança à operação.', 'Every standard, integration and infrastructure component has a practical role: reducing duplicate data entry, preserving continuity of information and giving more confidence to the operation.', 'Cada estándar, integración y componente de infraestructura tiene un papel práctico: reducir la digitación duplicada, preservar la continuidad de la información y dar más confianza a la operación.') ?></p>
    <div class="cta-bar-actions"><a href="/contato" class="btn-primary"><?= tx('Conversar sobre integrações e arquitetura', 'Talk about integrations and architecture', 'Conversar sobre integraciones y arquitectura') ?></a></div>
  </div>
</section>

</main>

<?php include __DIR__ . '/_partials/footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script src="/assets/js/main.js?v=20260731a"></script>
</body>
</html>
