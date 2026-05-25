<nav id="navbar">
  <div class="container nav-inner">
    <a href="/" class="nav-brand">tech<span>sallus</span></a>
    <ul class="nav-links">
      <li><a href="/#hero">Início</a></li>
      <li><a href="/#sistema">Sistema</a></li>
      <li><a href="/#modulos">Módulos</a></li>
      <li><a href="/#planos">Planos</a></li>
      <li><a href="/#suporte">Suporte</a></li>
      <li><a href="/blog/" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/blog') ? 'active' : '' ?>">Blog</a></li>
    </ul>
    <a href="/#demo" class="nav-cta">Quero conhecer o sistema</a>
    <button class="nav-hamburger" id="nav-hamburger" aria-label="Menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- Menu mobile overlay -->
<div class="mobile-menu" id="mobile-menu" role="dialog" aria-label="Menu de navegação">
  <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Fechar menu">×</button>
  <ul>
    <li><a href="/#hero">Início</a></li>
    <li><a href="/#sistema">Sistema</a></li>
    <li><a href="/#modulos">Módulos</a></li>
    <li><a href="/#planos">Planos</a></li>
    <li><a href="/#suporte">Suporte</a></li>
    <li><a href="/blog/">Blog</a></li>
  </ul>
</div>
