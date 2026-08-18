<footer>
  <div class="wrap">
    <div class="foot-top">
      <div class="foot-brand">
        <a href="/" class="brand"><img class="footer-logo" src="/assets/img/techsallus-logo.png" alt="TechSallus"/></a>
        <p><?= t('footer_tagline') ?></p>
      </div>

      <div class="foot-col">
        <h5><?= t('footer_col_solucoes') ?></h5>
        <a href="/consultorios"><?= t('footer_link_consultorios') ?></a>
        <a href="/clinicas"><?= t('footer_link_clinicas') ?></a>
        <a href="/hospitais"><?= t('footer_link_hospitais') ?></a>
      </div>

      <div class="foot-col">
        <h5><?= t('footer_col_resultados') ?></h5>
        <a href="/resultados"><?= t('footer_link_absenteismo') ?></a>
        <a href="/resultados"><?= t('footer_link_fluxo') ?></a>
        <a href="/resultados"><?= t('footer_link_faturamento') ?></a>
        <a href="/resultados"><?= t('footer_link_custos') ?></a>
      </div>

      <div class="foot-col">
        <h5><?= t('footer_col_gestao') ?></h5>
        <a href="/apure-custos"><?= t('footer_link_gestao') ?></a>
        <a href="/apure-custos"><?= t('footer_link_apure') ?></a>
        <a href="/tecnologia"><?= t('footer_link_bi') ?></a>
      </div>

      <div class="foot-col">
        <h5><?= t('footer_col_empresa') ?></h5>
        <a href="/sobre"><?= t('footer_link_sobre') ?></a>
        <a href="/tecnologia"><?= t('footer_link_tecnologia') ?></a>
        <a href="/faq"><?= t('footer_link_faq') ?></a>
        <a href="/contato"><?= t('footer_link_contato') ?></a>
      </div>
    </div>

    <div class="foot-bottom">
      <span>&copy; <?= date('Y') ?> TechSallus. <?= tx('Todos os direitos reservados.', 'All rights reserved.', 'Todos los derechos reservados.') ?></span>
      <a href="https://www.instagram.com/cassianogalvao.web" target="_blank" rel="noopener" style="color:inherit">Desenvolvido por @cassianogalvao.web</a>
    </div>
  </div>
</footer>

<?php
$rootDir = dirname(dirname(__DIR__));
if (!class_exists('Settings') && file_exists($rootDir . '/core/Settings.php')) {
    require_once $rootDir . '/core/Settings.php';
}
$whatsappUrl = class_exists('Settings') ? Settings::whatsappUrl() : 'https://wa.me/557181060274?text=Ola%2C%20gostaria%20de%20mais%20informacoes%20sobre%20o%20sistema%20de%20voces';
?>
<a href="<?= htmlspecialchars($whatsappUrl) ?>"
   target="_blank"
   rel="noopener noreferrer"
   class="whatsapp-float"
   aria-label="Falar pelo WhatsApp">
  <svg viewBox="0 0 24 24" fill="white" width="26" height="26" aria-hidden="true">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z"/>
    <path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.974-1.304A9.963 9.963 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z" fill="none" stroke="white" stroke-width="1.5"/>
  </svg>
</a>
