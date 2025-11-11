{{-- ===================== HEADER & NAVIGATION: START ===================== --}}
<header id="site-header" class="bg-white/90 backdrop-blur-lg shadow-sm sticky top-0 z-50">
  {{-- ========== TOP NAV BAR: START ========== --}}
  <nav class="container mx-auto px-4 lg:px-8 py-4 flex justify-between items-center">
    {{-- --- BRAND / LOGO: START --- --}}
    <a href="{{ route('home') }}" class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-teal-500 text-transparent bg-clip-text">
      VerseGate
    </a>
    {{-- --- BRAND / LOGO: END --- --}}

    {{-- --- DESKTOP MENU (≥ lg): START --- --}}
    <div class="hidden lg:flex items-center space-x-6 text-gray-600 font-medium">
      <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Home</a>

      {{-- Publik --}}
      <a href="{{ route('proyek.kami') }}" class="hover:text-blue-600 transition-colors">Proyek Kami</a>
      <a href="{{ route('feedback.create') }}" class="hover:text-blue-600 transition-colors">Saran &amp; Masukan</a>

      {{-- Auth --}}
      @auth
        {{-- ADMIN DROPDOWN --}}
        <div class="relative" data-admin-wrapper>
          <button
            type="button"
            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 hover:border-blue-300 hover:text-blue-600 transition-colors"
            aria-haspopup="true"
            aria-expanded="false"
            data-admin-btn
          >
            <i class="fa-solid fa-shield-halved"></i>
            <span>Admin</span>
            <i class="fa-solid fa-chevron-down text-xs" data-admin-caret></i>
          </button>

          <div
            class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl border border-gray-200 shadow-lg p-2"
            role="menu"
            aria-label="Admin menu"
            data-admin-menu
          >
            {{-- Workdesk dipindah ke sini --}}
            <a href="{{ route('workdesk') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 font-semibold"
               role="menuitem">
              <i class="fa-solid fa-briefcase text-indigo-600"></i>
              <span>Workdesk</span>
            </a>

            <a href="{{ route('admin.reports.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50"
               role="menuitem">
              <i class="fa-solid fa-flag text-blue-600"></i>
              <span>Laporan</span>
            </a>
            <a href="{{ route('admin.feedback.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50"
               role="menuitem">
              <i class="fa-regular fa-comments text-emerald-600"></i>
              <span>Saran &amp; Masukan</span>
            </a>
          </div>
        </div>
      @endauth
    </div>
    {{-- --- DESKTOP MENU: END --- --}}

    {{-- --- AUTH BLOCK + BURGER BUTTON: START --- --}}
    <div class="flex items-center space-x-4">
      {{-- Login/Logout: START --}}
      @guest
        <a href="{{ route('login') }}" class="hidden lg:inline-block bg-blue-600 text-white font-semibold px-5 py-2 rounded-full hover:bg-blue-700 transition-transform hover:scale-105">
          Login
        </a>
      @else
        <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
          @csrf
          <button type="submit" class="bg-red-200 rounded-full px-4 py-2 text-sm text-red-600 font-semibold transition-transform hover:scale-105">
            Logout
          </button>
        </form>
      @endguest
      {{-- Login/Logout: END --}}

      {{-- Burger Button (mobile toggle): START --}}
      <button
        type="button"
        class="lg:hidden text-gray-700 focus:outline-none p-2 transition-transform"
        data-menu-btn
        aria-controls="mobile-menu"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        {{-- Icon: open --}}
        <i class="fa-solid fa-bars text-2xl" data-menu-icon="open"></i>
        {{-- Icon: close --}}
        <i class="fa-solid fa-xmark text-2xl hidden" data-menu-icon="close"></i>
      </button>
      {{-- Burger Button: END --}}
    </div>
    {{-- --- AUTH BLOCK + BURGER BUTTON: END --- --}}
  </nav>
  {{-- ========== TOP NAV BAR: END ========== --}}

  {{-- ========== MOBILE DROPDOWN (≤ lg): START ========== --}}
  <div
    id="mobile-menu"
    data-menu
    class="hidden opacity-0 -translate-y-3 transition-all duration-200 lg:hidden px-4 pb-4 border-t border-gray-100"
  >
    <a href="{{ route('home') }}" class="block py-2 px-3 text-gray-600 font-medium rounded hover:bg-blue-50">
      Home
    </a>

    {{-- Publik --}}
    <a href="{{ route('proyek.kami') }}" class="block py-2 px-3 text-gray-600 font-medium rounded hover:bg-blue-50">
      Proyek Kami
    </a>
    <a href="{{ route('feedback.create') }}" class="block py-2 px-3 text-gray-600 font-medium rounded hover:bg-blue-50">
      Saran &amp; Masukan
    </a>

    @auth
      {{-- ADMIN GROUP (collapsible) --}}
      <div class="mt-2 border-t border-gray-100 pt-2">
        <button
          type="button"
          class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100"
          data-adminm-btn
          aria-controls="admin-mobile-menu"
          aria-expanded="false"
        >
          <span class="inline-flex items-center gap-2">
            <i class="fa-solid fa-shield-halved"></i> Admin
          </span>
          <i class="fa-solid fa-chevron-down text-xs" data-adminm-caret></i>
        </button>
        <div id="admin-mobile-menu" data-adminm-menu class="hidden pl-3 mt-1 space-y-1">
          {{-- Workdesk dipindah ke sini --}}
          <a href="{{ route('workdesk') }}" class="block py-2 px-3 text-gray-700 font-semibold rounded hover:bg-blue-50">
            Workdesk
          </a>
          <a href="{{ route('admin.reports.index') }}" class="block py-2 px-3 text-gray-600 font-medium rounded hover:bg-blue-50">
            Laporan
          </a>
          <a href="{{ route('admin.feedback.index') }}" class="block py-2 px-3 text-gray-600 font-medium rounded hover:bg-blue-50">
            Saran &amp; Masukan
          </a>
        </div>
      </div>
    @endauth

    @guest
      <a href="{{ route('login') }}" class="block w-full text-center mt-4 bg-blue-600 text-white font-semibold px-5 py-2 rounded-full hover:bg-blue-700">
        Login
      </a>
    @else
      <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button type="submit" class="w-full bg-red-50 text-red-600 font-semibold px-5 py-2 rounded-full hover:bg-red-100">
          Logout
        </button>
      </form>
    @endguest
  </div>
  {{-- ========== MOBILE DROPDOWN: END ========== --}}
</header>
{{-- ===================== HEADER & NAVIGATION: END ======================= --}}

@push('scripts')
<script>
(function initHeaderMenu(){
  const header = document.getElementById('site-header');
  if (!header) return;

  // ==== Mobile burger ====
  const btn       = header.querySelector('[data-menu-btn]');
  const menu      = header.querySelector('[data-menu]');
  const iconOpen  = header.querySelector('[data-menu-icon="open"]]');
  const iconClose = header.querySelector('[data-menu-icon="close"]]');
})();
</script>

<script>
(function initHeaderMenu2(){
  const header = document.getElementById('site-header');
  if (!header) return;

  const btn       = header.querySelector('[data-menu-btn]');
  const menu      = header.querySelector('[data-menu]');
  const iconOpen  = header.querySelector('[data-menu-icon="open"]');
  const iconClose = header.querySelector('[data-menu-icon="close"]');

  function showCloseIcon(show){ iconOpen.classList.toggle('hidden', show); iconClose.classList.toggle('hidden', !show); }
  function openMenu(){
    menu.classList.remove('hidden');
    requestAnimationFrame(()=>{ menu.classList.remove('opacity-0','-translate-y-3'); menu.classList.add('opacity-100','translate-y-0'); });
    btn.setAttribute('aria-expanded','true'); showCloseIcon(true);
  }
  function closeMenu(){
    menu.classList.add('opacity-0','-translate-y-3'); menu.classList.remove('opacity-100','translate-y-0');
    setTimeout(()=>{ if(menu.classList.contains('opacity-0')) menu.classList.add('hidden'); },200);
    btn.setAttribute('aria-expanded','false'); showCloseIcon(false);
  }
  btn?.addEventListener('click',e=>{ e.preventDefault(); menu.classList.contains('hidden') ? openMenu() : closeMenu(); });
  document.addEventListener('click',e=>{ if(!header.contains(e.target)) closeMenu(); });
  document.addEventListener('keydown',e=>{ if(e.key==='Escape') closeMenu(); });
  window.addEventListener('resize',()=>{ if(window.innerWidth>=1024) closeMenu(); });

  // ==== Desktop Admin dropdown ====
  const adminWrapper = header.querySelector('[data-admin-wrapper]');
  const adminBtn     = header.querySelector('[data-admin-btn]');
  const adminMenu    = header.querySelector('[data-admin-menu]');
  const adminCaret   = header.querySelector('[data-admin-caret]');
  function openAdmin(){ adminMenu?.classList.remove('hidden'); adminBtn?.setAttribute('aria-expanded','true'); if(adminCaret) adminCaret.style.transform='rotate(180deg)'; }
  function closeAdmin(){ adminMenu?.classList.add('hidden'); adminBtn?.setAttribute('aria-expanded','false'); if(adminCaret) adminCaret.style.transform='rotate(0deg)'; }
  adminBtn?.addEventListener('click',e=>{ e.preventDefault(); (adminMenu?.classList.contains('hidden')?openAdmin:closeAdmin)(); });
  document.addEventListener('click',e=>{ if(adminWrapper && !adminWrapper.contains(e.target)) closeAdmin(); });
  document.addEventListener('keydown',e=>{ if(e.key==='Escape') closeAdmin(); });

  // ==== Mobile Admin collapsible ====
  const adminmBtn   = header.querySelector('[data-adminm-btn]');
  const adminmMenu  = header.querySelector('[data-adminm-menu]');
  const adminmCaret = header.querySelector('[data-adminm-caret]');
  adminmBtn?.addEventListener('click', () => {
    const isHidden = adminmMenu.classList.contains('hidden');
    adminmMenu.classList.toggle('hidden', !isHidden);
    adminmBtn.setAttribute('aria-expanded', String(isHidden));
    if (adminmCaret) adminmCaret.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
  });
})();
</script>
@endpush
