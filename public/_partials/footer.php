<footer class="footer">
  <div class="container footer-inner">
    <div class="footer-col">
      <div class="footer-brand">techsallus</div>
      <p class="footer-tagline">Sistema de gestão hospitalar desde 1994</p>
    </div>
    <div class="footer-col">
      <h4>Links</h4>
      <ul>
        <li><a href="/#sistema">Sistema</a></li>
        <li><a href="/#modulos">Módulos</a></li>
        <li><a href="/#planos">Planos</a></li>
        <li><a href="/blog/">Blog</a></li>
        <li><a href="/#suporte">Suporte</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Contato</h4>
      <ul class="footer-contact">
        <li>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ff7300" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <a href="mailto:faleconosco@techsallus.com.br">faleconosco@techsallus.com.br</a>
        </li>
        <li>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ff7300" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <span>Rua Ewerton Visco, 290, Ed. Boulevard Side, Salas 1601 — Salvador, Bahia</span>
        </li>
        <li>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ff7300" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <span>Segunda a sexta, das 8h às 18h</span>
        </li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Presente em</h4>
      <ul class="footer-estados">
        <?php
        $estados = ['São Paulo','Rio de Janeiro','Espírito Santo','Rondônia','Maranhão','Sergipe','Alagoas','Bahia'];
        foreach ($estados as $e): ?>
          <li><span class="estado-dot"></span><?= $e ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <span>© <?= date('Y') ?> Techsallus. Todos os direitos reservados.</span>
      <a href="/admin/login.php">Acesso Restrito</a>
    </div>
  </div>
</footer>

<!-- WhatsApp flutuante -->
<a href="https://wa.me/557181299624?text=Ol%C3%A1%2C%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es%20sobre%20o%20sistema%20de%20voc%C3%AAs"
   target="_blank" rel="noopener noreferrer"
   class="whatsapp-float" aria-label="Falar pelo WhatsApp">
  <svg viewBox="0 0 24 24" fill="white" width="26" height="26">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z"/>
    <path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.974-1.304A9.963 9.963 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z" fill="none" stroke="white" stroke-width="1.5"/>
  </svg>
</a>
