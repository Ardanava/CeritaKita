{{-- ===================== HEADER & NAVIGATION: START ===================== --}}
<header id="site-header" class="bg-white/90 backdrop-blur-lg shadow-sm sticky top-0 z-50">
  {{-- ========== TOP NAV BAR: START ========== --}}
  <nav class="container mx-auto px-4 lg:px-8 py-4 flex justify-between items-center">
    {{-- --- BRAND / LOGO: START --- --}}
    <a href="{{ route('home') }}" class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-teal-500 text-transparent bg-clip-text">
      CeritaKita
    </a>
    {{-- --- BRAND / LOGO: END --- --}}

    {{-- --- DESKTOP MENU (≥ lg): START --- --}}
    <div class="hidden lg:flex items-center space-x-6 text-gray-600 font-medium">
      <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Home</a>

      @auth
        <a href="{{ route('workdesk') }}" class="hover:text-blue-600 transition-colors font-semibold">Workdesk</a>
      @endauth

      <a href="{{ route('proyek.kami') }}" class="hover:text-blue-600 transition-colors">Proyek Kami</a>
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

    @auth
      <a href="{{ route('workdesk') }}" class="block py-2 px-3 text-blue-600 font-semibold rounded hover:bg-blue-50">
        Workdesk
      </a>
    @endauth

    <a href="{{ route('proyek.kami') }}" class="block py-2 px-3 text-gray-600 font-medium rounded hover:bg-blue-50">
      Proyek Kami
    </a>

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
// ===================== HEADER MENU SCRIPT: START =====================
(function initHeaderMenu(){
  // --- cache elements: START ---
  const header = document.getElementById('site-header');
  if (!header) return;
  const btn       = header.querySelector('[data-menu-btn]');
  const menu      = header.querySelector('[data-menu]');
  const iconOpen  = header.querySelector('[data-menu-icon="open"]');
  const iconClose = header.querySelector('[data-menu-icon="close"]');
  if (!btn || !menu) return;
  // --- cache elements: END ---

  // --- icon swap helper: START ---
  function showCloseIcon(showClose) {
    iconOpen.classList.toggle('hidden', showClose);
    iconClose.classList.toggle('hidden', !showClose);
  }
  // --- icon swap helper: END ---

  // --- open/close handlers: START ---
  function openMenu() {
    menu.classList.remove('hidden');
    requestAnimationFrame(() => {
      menu.classList.remove('opacity-0', '-translate-y-3');
      menu.classList.add('opacity-100', 'translate-y-0');
    });
    btn.setAttribute('aria-expanded', 'true');
    showCloseIcon(true);
  }

  function closeMenu() {
    menu.classList.add('opacity-0', '-translate-y-3');
    menu.classList.remove('opacity-100', 'translate-y-0');
    setTimeout(() => {
      if (menu.classList.contains('opacity-0')) {
        menu.classList.add('hidden');
      }
    }, 200);
    btn.setAttribute('aria-expanded', 'false');
    showCloseIcon(false);
  }
  // --- open/close handlers: END ---

  // --- event bindings: START ---
  btn.addEventListener('click', (e) => {
    e.preventDefault();
    const isHidden = menu.classList.contains('hidden');
    isHidden ? openMenu() : closeMenu();
  });

  document.addEventListener('click', (e) => {
    if (!header.contains(e.target)) closeMenu();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeMenu();
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) closeMenu();
  });
  // --- event bindings: END ---
})();
// ===================== HEADER MENU SCRIPT: END =======================
</script>
@endpush
