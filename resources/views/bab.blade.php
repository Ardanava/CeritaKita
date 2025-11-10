@extends('layouts.clean')

@section('title', $chapter->title . ' - ' . $story->title)

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

<style>
  /* ============================================================
     THEME SYSTEM — SCOPED KE READER SAJA
     Semua variabel & warna hanya berlaku di dalam .reader-scope
     ============================================================ */
  .reader-scope{
    /* default (fallback) — light */
    --back: #F9FAFB;
    --text: #1F2937;
    --muted:#6B7280;
    --surface: rgba(255,255,255,.9);
    --card:#FFFFFF;
    --border:#E5E7EB;
    --blockquote:#cbd5e1;
    --link:#2563eb;
  }
  body.reader-bg-sync{ background-color: var(--ck-page-back) !important; }
  main{ background: transparent; } /* supaya main tidak ikut memutihkan */

  .reader-scope[data-theme="light"]{
    --back:#F9FAFB; --text:#1F2937; --muted:#6B7280;
    --surface: rgba(255,255,255,.9); --card:#FFFFFF; --border:#E5E7EB; --blockquote:#cbd5e1; --link:#2563eb;
  }
  .reader-scope[data-theme="sepia"]{
    --back:#FBF3E9; --text:#5B4636; --muted:#7A6A5E;
    --surface: rgba(247,240,231,.92); --card:#f7f0e7; --border:#E7D9C7; --blockquote:#D8C7AF; --link:#945F2C;
  }
  .reader-scope[data-theme="dark"]{
    --back:#0F172A; --text:#E5E7EB; --muted:#9CA3AF;
    --surface: rgba(17,24,39,.92); --card:#111827; --border:#374151; --blockquote:#334155; --link:#93C5FD;
  }

  /* ============================================================
   MODIFIKASI: Kunci UI & Sidebar ke Tema Light
   ============================================================ */

  /* 1. Buat class baru untuk area konten yang warnanya berubah */
  .reader-scope .reader-content-area {
    color: var(--text);
  }

  /* 2. Paksa Sidebar & UI untuk SELALU menggunakan variabel Light Theme */
  .reader-scope #chapter-sidebar,
  .reader-scope .surface-bar {
    /* * Definisikan ulang SEMUA variabel tema ke nilai 'light'
    * Ini akan meng-override variabel dari [data-theme="dark/sepia"]
    * untuk elemen-elemen ini dan semua turunannya.
    */
    --text: #1F2937;
    --muted: #6B7280;
    --surface: rgba(255,255,255,.9);
    --card: #FFFFFF;
    --border: #E5E7EB;
    --link: #2563eb;
    --blockquote: #cbd5e1; /* Walau tidak terpakai di UI, ini untuk kelengkapan */

    /* Atur warna default untuk UI/Sidebar */
    color: var(--text); 
  }

  /* * Terapkan style spesifik (background/border)
  * menggunakan variabel 'light' yang baru saja kita paksa
  */
  .reader-scope #chapter-sidebar {
    background: var(--card);
    border-right-color: var(--border);
  }
  .reader-scope .surface-bar {
    background: var(--surface);
    border-bottom-color: var(--border);
  }
  /* ============================================================
    AKHIR MODIFIKASI
    ============================================================ */

  /* ============================================================
     BASE DI DALAM READER (tidak menyentuh body/global)
     ============================================================ */
  .reader-scope{ background: var(--back); }
  .reader-scope a{ color: var(--link); }
  .reader-scope .muted{ color: var(--muted); }

  /* Typography konten */
  .reader-scope .story-content{ font-family:'Lora', serif; }
  .reader-scope .story-content .ql-align-center{ text-align:center!important; }
  .reader-scope .story-content .ql-align-right{ text-align:right!important; }
  .reader-scope .story-content .ql-align-justify{ text-align:justify!important; }
  .reader-scope .story-content .ql-size-small{ font-size:.875rem!important; }
  .reader-scope .story-content .ql-size-large{ font-size:1.25rem!important; }
  .reader-scope .story-content .ql-size-huge{  font-size:1.5rem!important; }
  .reader-scope .story-content img{ max-width:100%; height:auto; display:block; margin:.75rem auto; border-radius:.5rem; }
  .reader-scope .story-content ul,
  .reader-scope .story-content ol{ margin:1rem 0 1.25rem; padding-left:1.5rem; }
  .reader-scope .story-content li{ margin-bottom:.75rem; line-height:1.75; }
  .reader-scope .story-content p{ margin-bottom:1.25rem; }
  .reader-scope .story-content h1,.reader-scope .story-content h2,.reader-scope .story-content h3{ font-weight:700; margin:1.5rem 0 1rem; }
  .reader-scope .story-content blockquote{ border-left:4px solid var(--blockquote); margin:1rem 0; padding-left:1rem; font-style:italic; }

  /* Elemen permukaan yang mengikuti tema */
  .reader-scope .surface-bar{ background: var(--surface); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border-bottom:1px solid var(--border); }
  .reader-scope .surface-card{ background: var(--card); color: var(--text); }
  .reader-scope .soft-border{ border:1px solid var(--border); }
  .reader-scope .shadow-weak{ box-shadow: 0 1px 2px rgba(0,0,0,.04); }

  /* Progress bar */
  .reader-scope .progress-bar-wrap{ position:sticky; top:0; z-index:50; height:3px; }
  .reader-scope .progress-bar{ width:0%; height:3px; background:linear-gradient(90deg,#3B82F6,#8B5CF6); transition:width .1s linear; }

  /* Sidebar */
  .reader-scope #chapter-sidebar{ background: var(--card); color: var(--text); border-right:1px solid var(--border); }

  /* Donation card */
  .reader-scope .donation-card{ background: var(--card); color: var(--text); border:1px solid var(--border); }

  /* Sticky UI wrapper */
  .reader-scope #ui-wrapper{ position:sticky; top:0; z-index:40; transition:transform .25s ease; will-change:transform; transform:translateY(0); }

  /* Mobile */
  @media (max-width:640px){
    .reader-scope .story-content{ font-size:17px; line-height:1.75; }
  }
</style>
@endpush

@section('content')
{{-- ===================== READER WRAPPER (SCOPE TEMA) : START ===================== --}}
<div id="reader-wrapper" class="reader-scope" data-theme="light">
  {{-- ===================== READ PROGRESS (TOP) : START ===================== --}}
  <div class="progress-bar-wrap"><div id="read-progress" class="progress-bar"></div></div>
  {{-- ===================== READ PROGRESS (TOP) : END ======================= --}}

  {{-- ===================== SIDEBAR + OVERLAY : START ===================== --}}
  <div id="chapter-sidebar" class="fixed top-0 left-0 w-full max-w-sm h-full z-[60] transform -translate-x-full transition-transform duration-300 ease-in-out">
    <div class="p-4 flex justify-between items-center soft-border" style="border-left:none;border-right:none;border-top:none">
      <h2 class="text-xl font-bold">Daftar Isi</h2>
      <button id="close-sidebar-btn" class="muted hover:text-blue-500 transition-colors" aria-label="Tutup daftar bab">
        <i class="fa-solid fa-times text-2xl"></i>
      </button>
    </div>
    <ul class="p-4 space-y-2 overflow-y-auto h-[calc(100vh-65px)]">
      @forelse ($story->chapters as $c)
        <li>
          <a href="{{ route('stories.chapter', ['storySlug'=>$story->slug, 'chapterSlug'=>$c->slug]) }}"
             class="block p-3 rounded-lg {{ $c->id === $chapter->id ? 'bg-blue-100 text-blue-800 font-semibold' : 'hover:bg-black/5' }} transition-colors">
            {{ $c->title }}
          </a>
        </li>
      @empty
        <li><p class="p-3 muted">Belum ada bab lain.</p></li>
      @endforelse
    </ul>
  </div>
  <div id="sidebar-overlay" class="fixed top-0 left-0 w-full h-full bg-black/50 z-50 hidden" aria-hidden="true"></div>
  {{-- ===================== SIDEBAR + OVERLAY : END ======================= --}}

  {{-- ===================== UI WRAPPER (HEADER + CONTROLS) : START ===================== --}}
  <div id="ui-wrapper">
    {{-- ========== MAIN HEADER (Brand/Home, Titles, Chapter List) : START ========== --}}
    <header id="main-header" class="surface-bar shadow-weak">
      {{-- --- NAV WRAPPER : START --- --}}
      <nav class="mx-auto max-w-screen-xl px-4 lg:px-8 py-4 flex justify-between items-center">
        {{-- --- BRAND / HOME LINK : START --- --}}
        <a href="{{ route('home') }}" class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-teal-500 text-transparent bg-clip-text">
          CeritaKita
        </a>
        {{-- --- BRAND / HOME LINK : END --- --}}

        {{-- --- CENTERED TITLES (Story & Chapter) : START --- --}}
        <div class="text-center hidden md:block">
          <h1 class="font-semibold">{{ $story->title }}</h1>
          <p class="text-sm muted">{{ $chapter->title }}</p>
        </div>
        {{-- --- CENTERED TITLES (Story & Chapter) : END --- --}}

        {{-- --- OPEN SIDEBAR BUTTON (Daftar Bab) : START --- --}}
        <button id="open-sidebar-btn"
                class="px-4 py-2 text-sm rounded-full soft-border hover:bg-black/5 transition-colors"
                aria-label="Buka daftar bab">
          <i class="fa-solid fa-list-ul sm:mr-2"></i>
          <span class="hidden sm:inline">Daftar Bab</span>
        </button>
        {{-- --- OPEN SIDEBAR BUTTON (Daftar Bab) : END --- --}}
      </nav>
      {{-- --- NAV WRAPPER : END --- --}}
    </header>
    {{-- ========== /MAIN HEADER : END ========== --}}

    {{-- ========== READER CONTROLS (Prev / Info / Next + Font/Theme) : START ========== --}}
    <div id="reader-controls" class="surface-bar">
      <div class="mx-auto max-w-screen-xl px-4 lg:px-8 py-3 flex justify-between items-center">
        {{-- --- NAV BUTTONS (Prev, Info, Next) : START --- --}}
        <div class="flex items-center gap-2">
          @if($chapter->previous)
          <a href="{{ route('stories.chapter', ['storySlug'=>$story->slug,'chapterSlug'=>$chapter->previous->slug]) }}"
             class="px-3 py-2 text-sm rounded-full soft-border hover:bg-black/5 transition-colors">
            <i class="fa-solid fa-chevron-left sm:mr-2"></i><span class="hidden sm:inline">Bab Sebelumnya</span>
          </a>
          @endif

          <a href="{{ route('stories.show', $story->slug) }}"
             class="px-3 sm:px-4 py-2 text-sm rounded-full soft-border hover:bg-black/5 transition-colors"
             title="Kembali ke Detail Cerita">Info</a>

          @if($chapter->next)
          <a href="{{ route('stories.chapter', ['storySlug'=>$story->slug,'chapterSlug'=>$chapter->next->slug]) }}"
             class="px-3 py-2 text-sm rounded-full"
             style="background:#2563eb;color:white">
            <span class="hidden sm:inline">Bab Selanjutnya</span><i class="fa-solid fa-chevron-right sm:ml-2"></i>
          </a>
          @endif
        </div>
        {{-- --- NAV BUTTONS (Prev, Info, Next) : END --- --}}

        {{-- --- QUICK SETTINGS (Font size & Theme) : START --- --}}
        <div class="flex items-center gap-3 sm:gap-4">
          {{-- font popup --}}
          <div class="relative">
            <button id="font-popup-btn" class="muted hover:text-blue-600 transition-colors" aria-haspopup="true" aria-expanded="false" aria-controls="font-popup">
              <i class="fa-solid fa-font text-lg sm:text-xl"></i>
            </button>
            <div id="font-popup" class="absolute right-0 mt-2 p-2 w-44 surface-card rounded-lg shadow-weak soft-border hidden">
              <p class="text-xs font-semibold muted px-2 mb-1">Ukuran Font</p>
              <button id="decrease-font" class="w-full text-left px-2 py-1 rounded hover:bg-black/5">Kecil</button>
              <button id="reset-font"    class="w-full text-left px-2 py-1 rounded hover:bg-black/5">Normal</button>
              <button id="increase-font" class="w-full text-left px-2 py-1 rounded hover:bg-black/5">Besar</button>
            </div>
          </div>
          {{-- theme popup --}}
          <div class="relative">
            <button id="theme-popup-btn" class="muted hover:text-blue-600 transition-colors" aria-haspopup="true" aria-expanded="false" aria-controls="theme-popup">
              <i class="fa-solid fa-palette text-lg sm:text-xl"></i>
            </button>
            <div id="theme-popup" class="absolute right-0 mt-2 p-2 w-44 surface-card rounded-lg shadow-weak soft-border hidden">
              <p class="text-xs font-semibold muted px-2 mb-1">Tema</p>
              <button data-theme="light" class="theme-changer-btn w-full text-left px-2 py-1 rounded hover:bg-black/5">Cerah</button>
              <button data-theme="sepia"  class="theme-changer-btn w-full text-left px-2 py-1 rounded hover:bg-black/5">Sepia</button>
              <button data-theme="dark"   class="theme-changer-btn w-full text-left px-2 py-1 rounded hover:bg-black/5">Gelap</button>
            </div>
          </div>
        </div>
        {{-- --- QUICK SETTINGS (Font size & Theme) : END --- --}}
      </div>
    </div>
    {{-- ========== /READER CONTROLS : END ========== --}}
  </div>
  {{-- ===================== /UI WRAPPER : END ===================== --}}

  {{-- ===================== STORY CONTENT : START ===================== --}}
  <main class="mx-auto max-w-screen-xl px-4 lg:px-8 py-10 sm:py-12 reader-content-area">
    <article id="story-content-wrapper" class="max-w-prose mx-auto text-[17px] sm:text-lg leading-relaxed story-content transition-all duration-300">
      <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-6 sm:mb-8 text-center">{{ $chapter->title }}</h2>

      {!! $chapter->content !!}

      @if(!$chapter->next)
      <p class="mb-6 italic text-center muted">--- Bersambung ---</p>
      @endif
    </article>
  </main>
  {{-- ===================== STORY CONTENT : END ======================= --}}

  {{-- ===================== AUTHOR NOTE (OPTIONAL) : START ===================== --}}
  @if ($chapter->author_note)
  <section class="mx-auto max-w-screen-xl px-4 lg:px-8 py-8 reader-content-area">
    <div class="max-w-prose mx-auto">
      <h3 class="text-xl font-bold mb-4">Catatan Penulis</h3>
      <div class="protected p-4 rounded-lg surface-card soft-border">
        <p class="italic">{!! $chapter->author_note !!}</p>
      </div>
    </div>
  </section>
  @endif
  {{-- ===================== AUTHOR NOTE (OPTIONAL) : END ======================= --}}

  {{-- ===================== DONATION CARD : START ===================== --}}
  <section class="mx-auto max-w-screen-xl px-4 lg:px-8 py-8 reader-content-area">
    <div class="donation-card max-w-prose mx-auto text-center p-6 sm:p-7 rounded-lg">
      <h3 class="text-xl font-bold mb-2">Dukung Penulis</h3>
      <p class="mb-4 muted">Jika Anda menikmati cerita ini, pertimbangkan untuk memberikan donasi untuk mendukung karya selanjutnya.</p>
      <div class="flex justify-center items-center gap-3 sm:gap-4 flex-wrap">
        <a href="#" class="btn" style="background:#ef4444;color:#fff;padding:.5rem 1rem;border-radius:9999px"> <i class="fa-solid fa-mug-hot mr-2"></i>Trakteer </a>
        <a href="#" class="btn" style="background:#3b82f6;color:#fff;padding:.5rem 1rem;border-radius:9999px"> <i class="fa-solid fa-coffee mr-2"></i>Ko-fi </a>
      </div>
    </div>
  </section>
  {{-- ===================== DONATION CARD : END ======================= --}}

  {{-- ===================== BOTTOM NAV : START ===================== --}}
  <section class="mx-auto max-w-screen-xl px-4 lg:px-8 py-8 reader-content-area" style="border-top:1px solid var(--border)">
    <div class="max-w-prose mx-auto grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
      @if($chapter->previous)
      <a href="{{ route('stories.chapter', ['storySlug'=>$story->slug,'chapterSlug'=>$chapter->previous->slug]) }}"
         class="w-full sm:w-auto px-5 py-3 rounded-full soft-border hover:bg-black/5 transition-colors">
        <i class="fa-solid fa-chevron-left mr-2"></i> Bab Sebelumnya
      </a>
      @else
      <div class="hidden sm:block"></div>
      @endif

      <a href="{{ route('stories.show', $story->slug) }}#comment-section"
         class="font-semibold text-center">Beri Ulasan Cerita</a>

      @if($chapter->next)
      <a href="{{ route('stories.chapter', ['storySlug'=>$story->slug,'chapterSlug'=>$chapter->next->slug]) }}"
         class="w-full sm:w-auto px-5 py-3 rounded-full"
         style="background:#2563eb;color:white;justify-self:end;text-align:center">
        Bab Selanjutnya <i class="fa-solid fa-chevron-right ml-2"></i>
      </a>
      @endif
    </div>
  </section>
  {{-- ===================== BOTTOM NAV : END ======================= --}}

  {{-- ===================== COMMENTS (SIMULASI DISQUS) : START ===================== --}}
  <section id="comment-section" class="mx-auto max-w-screen-xl px-4 lg:px-8 py-12 reader-content-area" style="border-top:1px solid var(--border)">
    <div class="max-w-prose mx-auto">
      <h2 class="text-2xl font-bold mb-6">Komentar (Simulasi Disqus)</h2>
      <div id="disqus_thread" class="surface-card p-6 rounded-xl soft-border">
        <p class="text-center muted">
          <i class="fa-solid fa-comments text-3xl mb-3 block"></i>
          Area ini akan dimuat oleh <strong>Disqus</strong> saat aplikasi di-deploy.
        </p>
      </div>
    </div>
  </section>
  {{-- ===================== COMMENTS (SIMULASI DISQUS) : END ======================= --}}

  {{-- ===================== SCROLL-TO-TOP BUTTON : START ===================== --}}
  <button id="scrollToTopBtn"
    class="hidden fixed bottom-6 right-6 sm:bottom-8 sm:right-8"
    style="background:#2563eb;color:#fff;padding:.75rem;border-radius:9999px;box-shadow:0 10px 20px rgba(0,0,0,.12)">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m-7 7l7-7 7 7"/>
    </svg>
  </button>
  {{-- ===================== SCROLL-TO-TOP BUTTON : END ======================= --}}
</div>
{{-- ===================== /READER WRAPPER (SCOPE TEMA) : END ===================== --}}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  /* ============================================================
   *  CACHE DOM
   * ============================================================ */
  const wrapper = document.getElementById('reader-wrapper'); // SCOPED THEME TARGET
  const storyContent = document.getElementById('story-content-wrapper');
  const uiWrapper    = document.getElementById('ui-wrapper');
  const openSidebarBtn  = document.getElementById('open-sidebar-btn');
  const closeSidebarBtn = document.getElementById('close-sidebar-btn');
  const sidebar  = document.getElementById('chapter-sidebar');
  const overlay  = document.getElementById('sidebar-overlay');

  const fontPopupBtn = document.getElementById('font-popup-btn');
  const fontPopup    = document.getElementById('font-popup');
  const themePopupBtn= document.getElementById('theme-popup-btn');
  const themePopup   = document.getElementById('theme-popup');

  const decFont = document.getElementById('decrease-font');
  const norFont = document.getElementById('reset-font');
  const incFont = document.getElementById('increase-font');
  const themeBtns = document.querySelectorAll('.theme-changer-btn');

  const progressEl = document.getElementById('read-progress');
  const toTopBtn   = document.getElementById('scrollToTopBtn');

  /* ============================================================
   *  THEME INIT + PERSIST (SCOPED KE WRAPPER)
   * ============================================================ */
  function systemTheme(){ return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'; }
  function applyTheme(t){ wrapper?.setAttribute('data-theme', t); try{ localStorage.setItem('ck_theme', t);}catch{} }
  const saved = (()=>{ try{ return localStorage.getItem('ck_theme'); }catch{return null} })();
  applyTheme(saved || systemTheme());

  themeBtns.forEach(btn=>{
    btn.addEventListener('click', ()=>{
      applyTheme(btn.dataset.theme || 'light');
      themePopup?.classList.add('hidden');
    });
  });

  /* ============================================================
   *  FONT SIZE
   * ============================================================ */
  const setFontSize = px => { if (storyContent) storyContent.style.fontSize = px; };
  setFontSize('18px');
  decFont?.addEventListener('click', () => { setFontSize('16px'); fontPopup?.classList.add('hidden'); });
  norFont?.addEventListener('click', () => { setFontSize('18px'); fontPopup?.classList.add('hidden'); });
  incFont?.addEventListener('click', () => { setFontSize('22px'); fontPopup?.classList.add('hidden'); });

  /* ============================================================
   *  POPUPS (FONT & THEME)
   * ============================================================ */
  function wirePopup(btn, panel){
    if (!btn || !panel) return;
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      if (panel !== fontPopup)  fontPopup?.classList.add('hidden');
      if (panel !== themePopup) themePopup?.classList.add('hidden');
      panel.classList.toggle('hidden');
    });
  }
  wirePopup(fontPopupBtn,  fontPopup);
  wirePopup(themePopupBtn, themePopup);
  window.addEventListener('click', () => { fontPopup?.classList.add('hidden'); themePopup?.classList.add('hidden'); });

  /* ============================================================
   *  SIDEBAR OPEN/CLOSE
   * ============================================================ */
  const openSidebar = () => { sidebar?.classList.remove('-translate-x-full'); overlay?.classList.remove('hidden'); };
  const closeSidebar = () => { sidebar?.classList.add('-translate-x-full');  overlay?.classList.add('hidden');  };
  openSidebarBtn?.addEventListener('click', openSidebar);
  closeSidebarBtn?.addEventListener('click', closeSidebar);
  overlay?.addEventListener('click', () => { if (!sidebar?.classList.contains('-translate-x-full')) closeSidebar(); });

  /* ============================================================
   *  HIDE/SHOW TOP UI ON SCROLL
   * ============================================================ */
  if (uiWrapper) {
    let lastY = window.pageYOffset || document.documentElement.scrollTop || 0;
    const TOP_SAFE = 64, DELTA = 6;
    const showUI = () => { uiWrapper.style.transform = 'translateY(0)'; };
    const hideUI = () => {
      const y = window.pageYOffset || document.documentElement.scrollTop || 0;
      if (y > TOP_SAFE) uiWrapper.style.transform = 'translateY(-110%)';
    };
    window.addEventListener('scroll', () => {
      const y  = window.pageYOffset || document.documentElement.scrollTop || 0;
      const dy = y - lastY;
      if (y <= TOP_SAFE)      showUI();
      else if (dy >  DELTA)   hideUI();
      else if (dy < -DELTA)   showUI();
      lastY = y <= 0 ? 0 : y;
    }, { passive: true });
  }

  /* ============================================================
   *  BACK TO TOP
   * ============================================================ */
  if (toTopBtn) {
    window.addEventListener('scroll', () => {
      const show = (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100);
      toTopBtn.classList.toggle('hidden', !show);
    }, { passive: true });
    toTopBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }

  /* ============================================================
   *  READING PROGRESS
   * ============================================================ */
  function updateProgress(){
    if (!progressEl || !storyContent) return;
    const rect = storyContent.getBoundingClientRect();
    const vh   = window.innerHeight || document.documentElement.clientHeight;
    const total    = rect.height - vh + 1;
    const scrolled = Math.min(Math.max(-rect.top, 0), total);
    const pct      = Math.max(0, Math.min(100, (scrolled / Math.max(total,1)) * 100));
    progressEl.style.width = pct + '%';
  }
  window.addEventListener('scroll', updateProgress, { passive: true });
  window.addEventListener('resize', updateProgress);
  updateProgress();

  /* ============================================================
   *  KEYBOARD NAV ← →
   * ============================================================ */
  function findInfoUrl() {
    const links = Array.from(document.querySelectorAll('#reader-controls a'));
    const info = links.find(a => a.textContent && a.textContent.trim().toLowerCase() === 'info');
    return info ? info.href : (links[0] ? links[0].href : '/');
  }
  const prevLink = document.querySelector('#reader-controls i.fa-chevron-left')?.closest('a');
  const nextLink = document.querySelector('#reader-controls i.fa-chevron-right')?.closest('a');
  const infoUrl  = findInfoUrl();
  const prevUrl  = prevLink ? prevLink.href : infoUrl;
  const nextUrl  = nextLink ? nextLink.href : infoUrl;

  function isTypingField(el){ return el && (el.isContentEditable || ['INPUT','TEXTAREA','SELECT'].includes(el.tagName)); }
  function isPopupOpen(){ return (fontPopup && !fontPopup.classList.contains('hidden')) || (themePopup && !themePopup.classList.contains('hidden')); }
  function isSidebarOpen(){ return sidebar && !sidebar.classList.contains('-translate-x-full'); }

  window.addEventListener('keydown', (e) => {
    if (e.altKey || e.ctrlKey || e.metaKey || e.shiftKey) return;
    if (isTypingField(document.activeElement)) return;
    if (isSidebarOpen() || isPopupOpen()) return;
    if (e.key === 'ArrowLeft')  { e.preventDefault(); window.location.assign(prevUrl); }
    if (e.key === 'ArrowRight') { e.preventDefault(); window.location.assign(nextUrl); }
  }, { passive: false });
});

function syncPageBackgroundToTheme() {
    if (!wrapper) return;
    // Ambil nilai --back yang sedang aktif di .reader-scope
    const cs = getComputedStyle(wrapper);
    const back = (cs.getPropertyValue('--back') || '').trim();
    if (back) {
      // Terapkan ke body agar area luar wrapper tidak putih
      document.body.style.backgroundColor = back;
    }
  }

  // Ubah applyTheme agar sekalian sinkron background halaman
  document.body.classList.add('reader-bg-sync');

  /* === THEME INIT + PERSIST (SCOPED KE WRAPPER) === */
  function systemTheme(){ return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'; }

  // 🔥 BARU: fungsi sync yang benar2 membaca --back dari wrapper aktif
  function syncPageBackgroundToTheme() {
    if (!wrapper) return;
    const cs   = getComputedStyle(wrapper);
    const back = (cs.getPropertyValue('--back') || '').trim();
    if (back) {
      // pakai CSS var agar bisa di-!important lewat kelas body.reader-bg-sync
      document.body.style.setProperty('--ck-page-back', back);
    }
  }

  function applyTheme(t){
    wrapper?.setAttribute('data-theme', t);
    try{ localStorage.setItem('ck_theme', t);}catch{}
    syncPageBackgroundToTheme(); // <-- penting
  }

  const saved = (()=>{ try{ return localStorage.getItem('ck_theme'); }catch{return null} })();
  applyTheme(saved || systemTheme());      // <-- langsung sinkron saat load

  themeBtns.forEach(btn=>{
    btn.addEventListener('click', ()=>{
      applyTheme(btn.dataset.theme || 'light'); // <-- sinkron tiap ganti tema
      themePopup?.classList.add('hidden');
    });
  });

  try {
    // jika user ubah theme OS dan tidak ada preferensi tersimpan
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
      const saved = localStorage.getItem('ck_theme');
      if (!saved) applyTheme(systemTheme());
    });
  } catch {}


</script>
@endpush
