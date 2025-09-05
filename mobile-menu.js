// Universal Mobile Menu Script für alle Seiten
document.addEventListener('DOMContentLoaded', function() {
  // Erstelle mobile Menu-Button falls nicht vorhanden
  const navbar = document.querySelector('nav > div');
  if (navbar && !document.getElementById('mobile-menu-btn')) {
    // Wandle grid zu flex für mobile
    navbar.classList.remove('grid', 'grid-cols-3');
    navbar.classList.add('flex', 'items-center', 'justify-between', 'md:grid', 'md:grid-cols-3');
    
    // Verstecke Desktop-CTA auf mobile
    const desktopCta = navbar.children[2];
    if (desktopCta) {
      desktopCta.classList.add('hidden', 'md:flex');
    }
    
    // Erstelle Mobile Menu Button
    const mobileMenuBtn = document.createElement('button');
    mobileMenuBtn.id = 'mobile-menu-btn';
    mobileMenuBtn.className = 'md:hidden relative z-30 p-2 text-slate-300 hover:text-white transition-colors';
    mobileMenuBtn.innerHTML = `
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
      </svg>
    `;
    
    // Füge Button nach Logo hinzu
    navbar.children[0].after(mobileMenuBtn);
    
    // Erstelle Mobile Menu Overlay
    const mobileMenu = document.createElement('div');
    mobileMenu.id = 'mobile-menu';
    mobileMenu.className = 'md:hidden fixed inset-0 z-40 bg-slate-900/95 backdrop-blur-sm opacity-0 invisible transition-all duration-300';
    
    // Hole aktuelle Seite für aktiven Link
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    
    mobileMenu.innerHTML = `
      <div class="flex flex-col h-full pt-20 px-6 relative">
        <!-- Schließen-Kreuz oben rechts -->
        <button id="mobile-menu-close" class="absolute top-6 right-6 z-50 p-2 text-slate-300 hover:text-white transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
        
        <ul class="space-y-6 text-2xl font-medium text-slate-200">
          <li><a href="index.html" class="block hover:text-white transition-colors ${currentPage === 'index.html' ? 'text-white' : ''}">Home</a></li>
          <li><a href="leistungen.html" class="block hover:text-white transition-colors ${currentPage === 'leistungen.html' ? 'text-white' : ''}">Leistungen</a></li>
          <li><a href="ueber-mich.html" class="block hover:text-white transition-colors ${currentPage === 'ueber-mich.html' ? 'text-white' : ''}">Über mich</a></li>
          <li><a href="kontakt.html" class="block hover:text-white transition-colors ${currentPage === 'kontakt.html' ? 'text-white' : ''}">Kontakt</a></li>
        </ul>
        <div class="mt-8">
          <a href="gespraech.html" class="inline-flex w-full items-center justify-center gap-2.5 rounded-xl bg-blue-600 px-6 py-4 text-lg font-medium text-white shadow-lg transition-all duration-300 hover:bg-blue-700 hover:scale-[1.02]">
            Gespräch vereinbaren
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 12h14M13 5l7 7-7 7" />
            </svg>
          </a>
        </div>
      </div>
    `;
    
    // Füge Mobile Menu zum Header hinzu
    document.querySelector('header').appendChild(mobileMenu);
    
    // Menu Toggle Funktionalität
    let isMenuOpen = false;
    
    function toggleMenu() {
      isMenuOpen = !isMenuOpen;
      
      if (isMenuOpen) {
        mobileMenu.classList.remove('opacity-0', 'invisible');
        mobileMenu.classList.add('opacity-100', 'visible');
        document.body.style.overflow = 'hidden';
        
        mobileMenuBtn.innerHTML = `
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        `;
      } else {
        mobileMenu.classList.add('opacity-0', 'invisible');
        mobileMenu.classList.remove('opacity-100', 'visible');
        document.body.style.overflow = '';
        
        mobileMenuBtn.innerHTML = `
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        `;
      }
    }
    
    mobileMenuBtn.addEventListener('click', toggleMenu);
    
    // Event Listener für das Schließen-Kreuz
    const mobileMenuClose = mobileMenu.querySelector('#mobile-menu-close');
    mobileMenuClose.addEventListener('click', toggleMenu);
    
    // Schließe Menu bei Link-Klick
    mobileMenu.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        if (isMenuOpen) toggleMenu();
      });
    });
    
    // Schließe Menu bei Klick außerhalb
    mobileMenu.addEventListener('click', (e) => {
      if (e.target === mobileMenu) {
        toggleMenu();
      }
    });
  }
});
