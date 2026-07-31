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

$_pep = [
    ['shield', tx('Segurança e rastreabilidade', 'Security and traceability', 'Seguridad y trazabilidad'), tx('Perfis de acesso, registros e requisitos de segurança apoiam o uso responsável da informação clínica.', 'Access profiles, logs and security requirements support the responsible use of clinical information.', 'Perfiles de acceso, registros y requisitos de seguridad apoyan el uso responsable de la información clínica.')],
    ['heartPulse', tx('Continuidade do cuidado', 'Continuity of care', 'Continuidad del cuidado'), tx('A equipe encontra dados relevantes no ponto de atendimento e reduz a dependência de documentos ou controles separados.', 'The team finds relevant data at the point of care and reduces dependency on separate documents or controls.', 'El equipo encuentra datos relevantes en el punto de atención y reduce la dependencia de documentos o controles separados.')],
];

$_interop = [
    ['image', tx('PACS e DICOM', 'PACS and DICOM', 'PACS y DICOM'), tx('Armazene, organize, visualize e compartilhe exames de imagem dentro do fluxo assistencial.', 'Store, organize, view and share imaging exams within the clinical flow.', 'Almacene, organice, visualice y comparta exámenes de imagen dentro del flujo asistencial.')],
    ['monitor', tx('RIS e telerradiologia', 'RIS and teleradiology', 'RIS y telerradiología'), tx('Encaminhe imagens e conecte o fluxo de radiologia do agendamento à emissão de laudos, conforme o projeto.', 'Route images and connect the radiology flow from scheduling to report issuance, according to the project.', 'Envíe imágenes y conecte el flujo de radiología desde la agenda hasta la emisión de informes, según el proyecto.')],
    ['plug', 'HL7', tx('Integre monitores de parâmetros vitais e outros equipamentos para reduzir digitação manual e dar continuidade aos dados.', 'Integrate vital-sign monitors and other equipment to reduce manual data entry and give continuity to data.', 'Integre monitores de signos vitales y otros equipos para reducir la digitación manual y dar continuidad a los datos.')],
    ['database', tx('HIS e LIS', 'HIS and LIS', 'HIS y LIS'), tx('Conecte informações hospitalares, clínicas e laboratoriais de acordo com as interfaces e o escopo definidos.', 'Connect hospital, clinical and laboratory information according to the defined interfaces and scope.', 'Conecte información hospitalaria, clínica y de laboratorio de acuerdo con las interfaces y el alcance definidos.')],
];

$_gloss = [
    ['SOAP', tx('Método de registro clínico orientado por problemas: Subjetivo, Objetivo, Avaliação e Plano.', 'Problem-oriented clinical record method: Subjective, Objective, Assessment and Plan.', 'Método de registro clínico orientado por problemas: Subjetivo, Objetivo, Evaluación y Plan.')],
    ['SBIS / CFM', tx('Referências brasileiras ligadas à certificação e aos requisitos de segurança de sistemas de registro eletrônico em saúde.', 'Brazilian references tied to certification and security requirements for electronic health record systems.', 'Referencias brasileñas ligadas a la certificación y a los requisitos de seguridad de sistemas de registro electrónico en salud.')],
    ['NGS1 / NGS2', tx('Níveis de garantia de segurança definidos nos manuais de certificação de sistemas de registro eletrônico em saúde.', 'Security assurance levels defined in the certification manuals for electronic health record systems.', 'Niveles de garantía de seguridad definidos en los manuales de certificación de sistemas de registro electrónico en salud.')],
    ['TISS', tx('Padrão da Agência Nacional de Saúde Suplementar para troca de informações entre prestadores e operadoras.', 'Standard from Brazil\'s National Supplementary Health Agency for information exchange between providers and payers.', 'Estándar de la Agencia Nacional de Salud Suplementaria para el intercambio de información entre prestadores y aseguradoras.')],
    ['TUSS', tx('Terminologia padronizada para códigos e descrições de procedimentos, exames, taxas, materiais e medicamentos na saúde suplementar.', 'Standardized terminology for codes and descriptions of procedures, exams, fees, materials and medications in supplementary health.', 'Terminología estandarizada para códigos y descripciones de procedimientos, exámenes, tasas, materiales y medicamentos en la salud suplementaria.')],
    ['PACS', tx('Ambiente usado para armazenar, organizar, distribuir e visualizar exames de imagem.', 'Environment used to store, organize, distribute and view imaging exams.', 'Entorno usado para almacenar, organizar, distribuir y visualizar exámenes de imagen.')],
    ['DICOM', tx('Padrão internacional para armazenar e compartilhar imagens médicas e seus metadados.', 'International standard for storing and sharing medical images and their metadata.', 'Estándar internacional para almacenar y compartir imágenes médicas y sus metadatos.')],
    ['RIS', tx('Sistema que organiza o fluxo de radiologia, do agendamento à emissão e entrega do laudo.', 'System that organizes the radiology flow, from scheduling to report issuance and delivery.', 'Sistema que organiza el flujo de radiología, desde la agenda hasta la emisión y entrega del informe.')],
    ['HL7', tx('Padrão de interoperabilidade usado para trocar informações entre sistemas e equipamentos de saúde.', 'Interoperability standard used to exchange information between healthcare systems and equipment.', 'Estándar de interoperabilidad usado para intercambiar información entre sistemas y equipos de salud.')],
    ['HIS', tx('Sistema de informação hospitalar que integra fluxos clínicos, administrativos e financeiros.', 'Hospital information system that integrates clinical, administrative and financial flows.', 'Sistema de información hospitalaria que integra flujos clínicos, administrativos y financieros.')],
    ['LIS', tx('Sistema de informação laboratorial que acompanha o ciclo do exame, da solicitação à liberação do resultado.', 'Laboratory information system that tracks the exam cycle, from request to result release.', 'Sistema de información de laboratorio que acompaña el ciclo del examen, desde la solicitud hasta la liberación del resultado.')],
    ['BI', tx('Business Intelligence: organização e análise de dados para criação de indicadores, relatórios e painéis de gestão.', 'Business Intelligence: organizing and analyzing data to create indicators, reports and management dashboards.', 'Business Intelligence: organización y análisis de datos para la creación de indicadores, informes y paneles de gestión.')],
    [tx('Tabela de fatos', 'Fact table', 'Tabla de hechos'), tx('Estrutura de dados que reúne métricas de eventos da operação e permite aprofundar análises por diferentes dimensões.', 'Data structure that gathers metrics from operational events and allows deeper analysis across different dimensions.', 'Estructura de datos que reúne métricas de eventos de la operación y permite profundizar análisis por diferentes dimensiones.')],
    ['RDS', tx('Serviço gerenciado de banco de dados relacional em nuvem, utilizado em arquiteturas compatíveis.', 'Managed cloud relational database service, used in compatible architectures.', 'Servicio gestionado de base de datos relacional en la nube, utilizado en arquitecturas compatibles.')],
    ['EC2', tx('Serviço de servidores virtuais em nuvem, usado para executar aplicações em arquiteturas compatíveis.', 'Cloud virtual server service, used to run applications in compatible architectures.', 'Servicio de servidores virtuales en la nube, usado para ejecutar aplicaciones en arquitecturas compatibles.')],
    [tx('Desblistagem', 'Blister depackaging', 'Desblisterado'), tx('Processo de retirada de comprimidos e cápsulas de embalagens blister para apoiar separação, fracionamento ou dispensação.', 'Process of removing tablets and capsules from blister packs to support separation, splitting or dispensing.', 'Proceso de retirada de comprimidos y cápsulas de envases blister para apoyar separación, fraccionamiento o dispensación.')],
];
?>
<!DOCTYPE html>
<html lang="<?= $LANG ?>">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= tx('Tecnologia, Integrações e Segurança para Saúde | TechSallus', 'Technology, Integrations and Security for Healthcare | TechSallus', 'Tecnología, Integraciones y Seguridad para Salud | TechSallus') ?></title>
<meta name="description" content="<?= tx('Conheça os recursos de prontuário, TISS/TUSS, PACS/DICOM, HL7, nuvem, backups e BI que sustentam uma operação de saúde conectada.', 'Learn about the medical records, TISS/TUSS, PACS/DICOM, HL7, cloud, backup and BI capabilities that sustain a connected healthcare operation.', 'Conozca los recursos de historia clínica, TISS/TUSS, PACS/DICOM, HL7, nube, copias de seguridad y BI que sostienen una operación de salud conectada.') ?>"/>
<link rel="icon" type="image/png" href="/assets/img/favicon.png"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<link rel="stylesheet" href="/assets/css/main.css?v=20260731d"/>
<?= getScripts('head') ?>
</head>
<body>
<?= getScripts('body') ?>

<?php include __DIR__ . '/_partials/nav.php'; ?>

<main>

<section class="subhero">
  <div class="wrap">
    <span class="eyebrow reveal"><?= tx('Robustez técnica a serviço da operação', 'Technical robustness at the service of the operation', 'Robustez técnica al servicio de la operación') ?></span>
    <h1 class="reveal"><?= tx('Integração e continuidade para uma instituição de saúde que não pode parar.', 'Integration and continuity for a health institution that cannot stop.', 'Integración y continuidad para una institución de salud que no puede detenerse.') ?></h1>
    <p class="reveal"><?= tx('A TechSallus combina recursos clínicos, administrativos e financeiros com padrões de interoperabilidade, infraestrutura em nuvem, rotinas de continuidade e capacidade analítica.', 'TechSallus combines clinical, administrative and financial capabilities with interoperability standards, cloud infrastructure, continuity routines and analytical capacity.', 'TechSallus combina recursos clínicos, administrativos y financieros con estándares de interoperabilidad, infraestructura en la nube, rutinas de continuidad y capacidad analítica.') ?></p>
    <div class="hero-ctas reveal"><a href="/contato" class="btn btn-primary"><?= tx('Falar com um especialista técnico', 'Talk to a technical specialist', 'Hablar con un especialista técnico') ?></a></div>
    <div class="hero-tag reveal"><?= tx('A arquitetura, as integrações e os serviços são definidos conforme o perfil e o escopo de cada projeto', 'Architecture, integrations and services are defined according to the profile and scope of each project', 'La arquitectura, las integraciones y los servicios se definen según el perfil y el alcance de cada proyecto') ?></div>
  </div>
</section>

<section class="section tint">
  <div class="wrap">
    <div class="section-head center">
      <span class="eyebrow reveal"><?= tx('Prontuário e informação clínica', 'Medical records and clinical information', 'Historia clínica e información clínica') ?></span>
      <h2 class="reveal"><?= tx('Registro clínico estruturado do início ao fim do cuidado.', 'Structured clinical record from start to finish of care.', 'Registro clínico estructurado del inicio al fin del cuidado.') ?></h2>
      <p class="reveal"><?= tx('O prontuário eletrônico orientado pelo método SOAP organiza relatos, achados, avaliação e plano de cuidado em uma estrutura clara. Dados clínicos, laudos, prescrições e evoluções permanecem vinculados à jornada do paciente.', 'The electronic medical record based on the SOAP method organizes subjective and objective findings, assessment and care plan in a clear structure. Clinical data, reports, prescriptions and progress notes stay linked to the patient\'s journey.', 'La historia clínica electrónica orientada por el método SOAP organiza relatos, hallazgos, evaluación y plan de cuidado en una estructura clara. Los datos clínicos, informes, prescripciones y evoluciones permanecen vinculados al recorrido del paciente.') ?></p>
    </div>
    <div class="grid-2">
      <?php foreach ($_pep as [$_i, $_t, $_d]): ?>
        <div class="card reveal"><div class="icon-badge"><?= ic($_i) ?></div><h3><?= $_t ?></h3><p><?= $_d ?></p></div>
      <?php endforeach; ?>
    </div>
    <div class="cert-card reveal">
      <div class="icon-badge"><?= ic('shield') ?></div>
      <div><h4><?= tx('Certificação S-RES apresentada', 'S-RES certification presented', 'Certificación S-RES presentada') ?></h4><p><?= tx('PEP TechSallus v15.0.0, Clínica/Ambulatório, Estágio 1 - NGS2, certificado nº 100, versão 5.2.', 'PEP TechSallus v15.0.0, Clinic/Outpatient, Stage 1 - NGS2, certificate No. 100, version 5.2.', 'PEP TechSallus v15.0.0, Clínica/Ambulatorio, Etapa 1 - NGS2, certificado n.º 100, versión 5.2.') ?></p></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="section-head center">
      <span class="eyebrow reveal"><?= tx('Interoperabilidade', 'Interoperability', 'Interoperabilidad') ?></span>
      <h2 class="reveal"><?= tx('Interoperabilidade com imagens, sistemas e equipamentos.', 'Interoperability with images, systems and equipment.', 'Interoperabilidad con imágenes, sistemas y equipos.') ?></h2>
    </div>
    <div class="grid-4">
      <?php foreach ($_interop as [$_i, $_t, $_d]): ?>
        <div class="card reveal"><div class="icon-badge"><?= ic($_i) ?></div><h3><?= $_t ?></h3><p><?= $_d ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section tint">
  <div class="wrap">
    <div class="section-head center">
      <span class="eyebrow reveal"><?= tx('Infraestrutura', 'Infrastructure', 'Infraestructura') ?></span>
      <h2 class="reveal"><?= tx('Infraestrutura, continuidade e acesso.', 'Infrastructure, continuity and access.', 'Infraestructura, continuidad y acceso.') ?></h2>
    </div>
    <div class="mod-grid">
      <div class="mod-col reveal">
        <h3><?= tx('Operação em nuvem', 'Cloud operation', 'Operación en la nube') ?></h3>
        <ul>
          <li><?= tx('Infraestrutura no Brasil conforme a modalidade', 'Infrastructure in Brazil according to the plan', 'Infraestructura en Brasil según la modalidad') ?></li>
          <li><?= tx('Opções de ambiente dedicado ou virtual', 'Dedicated or virtual environment options', 'Opciones de entorno dedicado o virtual') ?></li>
          <li><?= tx('Configuração e manutenção técnica', 'Technical configuration and maintenance', 'Configuración y mantenimiento técnico') ?></li>
          <li><?= tx('Atualizações de sistema operacional e banco de dados', 'Operating system and database updates', 'Actualizaciones de sistema operativo y base de datos') ?></li>
        </ul>
      </div>
      <div class="mod-col filled reveal">
        <h3><?= tx('Continuidade do serviço', 'Service continuity', 'Continuidad del servicio') ?></h3>
        <ul>
          <li><?= tx('Rotinas de backup de banco de dados e aplicações', 'Database and application backup routines', 'Rutinas de copia de seguridad de base de datos y aplicaciones') ?></li>
          <li><?= tx('Arquitetura definida conforme o porte e o projeto', 'Architecture defined according to size and project', 'Arquitectura definida según el porte y el proyecto') ?></li>
          <li><?= tx('SLA de disponibilidade conforme contrato', 'Availability SLA according to contract', 'SLA de disponibilidad según contrato') ?></li>
          <li><?= tx('Acesso multiplataforma nos escopos compatíveis', 'Multi-platform access in compatible scopes', 'Acceso multiplataforma en los alcances compatibles') ?></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap" style="max-width:820px">
    <span class="eyebrow reveal"><?= tx('Dados e BI', 'Data and BI', 'Datos y BI') ?></span>
    <h2 class="reveal" style="margin-top:18px;font-size:clamp(28px,3.2vw,38px)"><?= tx('Dados para acompanhar e aprofundar a gestão.', 'Data to track and deepen management.', 'Datos para acompañar y profundizar la gestión.') ?></h2>
    <p class="reveal" style="color:var(--muted);font-size:17px;margin-top:16px;line-height:1.65"><?= tx('Painéis desenvolvidos em Power BI transformam dados operacionais em indicadores. As estruturas de dados podem ser disponibilizadas para que a equipe técnica do cliente amplie dimensões, medidas e análises conforme a necessidade.', 'Dashboards built in Power BI turn operational data into indicators. Data structures can be made available so the client\'s technical team can expand dimensions, measures and analysis as needed.', 'Los paneles desarrollados en Power BI transforman los datos operativos en indicadores. Las estructuras de datos pueden ponerse a disposición para que el equipo técnico del cliente amplíe dimensiones, medidas y análisis según la necesidad.') ?></p>
  </div>
</section>

<section class="section tint">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow reveal"><?= tx('Conteúdo complementar', 'Additional content', 'Contenido complementario') ?></span>
      <h2 class="reveal"><?= tx('Glossário técnico.', 'Technical glossary.', 'Glosario técnico.') ?></h2>
      <p class="reveal"><?= tx('Termos utilizados no site, traduzidos em linguagem direta.', 'Terms used on the site, translated into plain language.', 'Términos utilizados en el sitio, traducidos en lenguaje directo.') ?></p>
    </div>
    <div class="gloss-grid">
      <?php foreach ($_gloss as [$_t, $_d]): ?>
        <div class="gloss-row reveal"><div class="term"><?= $_t ?></div><div class="def"><?= $_d ?></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section band-dark" id="cta">
  <div class="wrap">
    <span class="eyebrow reveal"><?= tx('Tecnologia como meio, resultado como objetivo', 'Technology as the means, results as the goal', 'Tecnología como medio, resultado como objetivo') ?></span>
    <h2 class="reveal"><?= tx('Cada integração tem um papel prático na sua operação.', 'Every integration plays a practical role in your operation.', 'Cada integración tiene un papel práctico en su operación.') ?></h2>
    <p class="reveal" style="max-width:640px"><?= tx('Reduzir digitação duplicada, preservar a continuidade da informação e dar mais confiança à operação.', 'Reducing duplicate data entry, preserving continuity of information and giving more confidence to the operation.', 'Reducir la digitación duplicada, preservar la continuidad de la información y dar más confianza a la operación.') ?></p>
    <a href="/contato" class="btn btn-dark reveal"><?= tx('Conversar sobre integrações e arquitetura', 'Talk about integrations and architecture', 'Conversar sobre integraciones y arquitectura') ?></a>
  </div>
</section>

</main>

<?php include __DIR__ . '/_partials/footer.php'; ?>
<script src="/assets/js/main.js?v=20260731d"></script>
</body>
</html>
