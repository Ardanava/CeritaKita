@extends('layouts.clean')

@section('title', $chapter->title . ' - ' . $story->title)

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

<style>
  /* ============================================================
     THEME SYSTEM — SCOPE DI .reader-scope
     ============================================================ */
  .reader-scope{
    --back:#F9FAFB; --text:#1F2937; --muted:#6B7280;
    --surface: rgba(255,255,255,.9); --card:#FFFFFF; --border:#E5E7EB; --blockquote:#cbd5e1; --link:#2563eb;
  }
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

  /* Sinkron warna body (area luar wrapper) */
  body.reader-bg-sync{ background-color: var(--ck-page-back) !important; }
  main{ background: transparent; }

  /* ============================================================
     Kunci UI & Sidebar ke tema LIGHT
     ============================================================ */
  .reader-scope .reader-content-area { color: var(--text); }
  .reader-scope #chapter-sidebar, .reader-scope .surface-bar {
    --text:#1F2937; --muted:#6B7280; --surface:rgba(255,255,255,.9);
    --card:#FFFFFF; --border:#E5E7EB; --link:#2563eb; --blockquote:#cbd5e1;
    color: var(--text);
  }
  .reader-scope #chapter-sidebar { background: var(--card); border-right:1px solid var(--border); }
  .reader-scope .surface-bar { background: var(--surface); border-bottom:1px solid var(--border); }

  /* Base dalam reader */
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

  /* Permukaan tematik */
  .reader-scope .surface-card{ background: var(--card); color: var(--text); }
  .reader-scope .soft-border{ border:1px solid var(--border); }
  .reader-scope .shadow-weak{ box-shadow: 0 1px 2px rgba(0,0,0,.04); }

  /* Progress */
  .reader-scope .progress-bar-wrap{ position:sticky; top:0; z-index:50; height:3px; }
  .reader-scope .progress-bar{ width:0%; height:3px; background:linear-gradient(90deg,#3B82F6,#8B5CF6); transition:width .1s linear; }

  /* Donation */
  .reader-scope .donation-card{ background: var(--card); color: var(--text); border:1px solid var(--border); }

  /* Sticky UI */
  .reader-scope #ui-wrapper{ position:sticky; top:0; z-index:40; transition:transform .25s ease; will-change:transform; transform:translateY(0); }

  /* ============================================================
     REPORT MODAL — Fixed palette (tidak ikut tema)
     ============================================================ */
  #reportModal .modal-card{
    background:#ffffff !important;
    color:#1f2937 !important;            /* slate-800 */
    border:1px solid #e5e7eb;            /* slate-200 */
    border-radius: 1rem;
  }
  #reportModal .modal-divider{ border-bottom:1px solid #e5e7eb; }
  #reportModal .modal-muted{ color:#6b7280; }
  #reportModal .modal-overlay{ background: rgba(0,0,0,.5); }

  /* Inputs */
  #reportModal .ck-input,
  #reportModal .ck-textarea,
  #reportModal .ck-select-input{
    background:#ffffff; color:#1f2937;
    border:1.5px solid #e5e7eb; border-radius:.75rem;
    padding:.6rem .8rem; width:100%;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.04);
  }
  #reportModal .ck-input:focus,
  #reportModal .ck-textarea:focus,
  #reportModal .ck-select-input:focus{
    outline:none; border-color:#60a5fa;            /* blue-400 */
    box-shadow:0 0 0 3px rgba(96,165,250,.35);
  }

  /* Buttons (kontras) */
  #reportModal .ck-btn{
    border-radius:9999px; padding:.5rem 1rem; font-weight:600;
    transition: background-color .15s ease, border-color .15s ease, box-shadow .15s ease, filter .15s ease;
  }
  #reportModal .ck-btn-outline{
    background:#ffffff; color:#111827; border:1.5px solid #d1d5db;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.6), 0 1px 2px rgba(16,24,40,.05);
  }
  #reportModal .ck-btn-outline:hover{ background:#f9fafb; border-color:#cbd5e1; }
  #reportModal .ck-btn-outline:active{ background:#f3f4f6; border-color:#94a3b8; }
  #reportModal .ck-btn-outline:focus-visible{
    outline:none; border-color:#60a5fa; box-shadow:0 0 0 3px rgba(96,165,250,.35);
  }

  #reportModal .ck-btn-primary{ background:#2563eb; color:#fff; border:1px solid transparent; }
  #reportModal .ck-btn-primary:hover{ filter:brightness(.95); }
  #reportModal .ck-btn-primary:focus-visible{ outline:none; box-shadow:0 0 0 3px rgba(96,165,250,.35); }
  #reportModal .ck-btn-primary:disabled{ opacity:.7; cursor:not-allowed; }

  /* === Custom Select (full styled) === */
  #reportModal .cselect { position: relative; }
  #reportModal .cselect-button{
    display:flex; align-items:center; justify-content:space-between; gap:.5rem;
    width:100%; padding:.6rem .9rem; border-radius:.75rem;
    background:#fff; color:#1f2937; border:1.5px solid #e5e7eb;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.04);
    transition: border-color .15s ease, box-shadow .15s ease;
  }
  #reportModal .cselect-button:focus-visible{
    outline:none; border-color:#60a5fa; box-shadow:0 0 0 3px rgba(96,165,250,.35);
  }
  #reportModal .cselect-button .chev{
    width:16px; height:16px; flex:none; opacity:.7; transition: transform .15s ease;
    background: no-repeat center/16px 16px url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 20 20'%3E%3Cpath fill='%236b7280' d='M5.23 7.21a.75.75 0 011.06.02L10 10.17l3.71-2.94a.75.75 0 111.06 1.06l-4.24 3.36a.75.75 0 01-.94 0L5.21 8.29a.75.75 0 01.02-1.08z'/%3E%3C/svg%3E");
  }
  #reportModal .cselect[aria-expanded="true"] .chev{ transform: rotate(180deg); }

  #reportModal .cselect-panel{
    position:absolute; left:0; right:0; margin-top:.35rem; z-index:10;
    max-height:260px; overflow:auto;
    background:#fff; border:1.5px solid #e5e7eb; border-radius:.75rem;
    box-shadow: 0 10px 25px rgba(0,0,0,.12), 0 4px 8px rgba(0,0,0,.06);
  }
  #reportModal .cselect-option{
    padding:.55rem .9rem; cursor:pointer; white-space:nowrap; display:flex; align-items:center; gap:.5rem;
  }
  #reportModal .cselect-option:hover{ background:#f3f4f6; }
  #reportModal .cselect-option[aria-selected="true"]{
    background:#eff6ff; color:#1e40af;
  }
  #reportModal .cselect-option .tick{
    width:16px; height:16px; flex:none; visibility:hidden;
    background:no-repeat center/16px 16px url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%231e40af' d='M16.704 5.29a1 1 0 010 1.414l-7.071 7.07a1 1 0 01-1.415 0L3.296 8.853a1 1 0 111.415-1.414l3.122 3.121 6.364-6.364a1 1 0 011.414 0z'/%3E%3C/svg%3E");
  }
  #reportModal .cselect-option[aria-selected="true"] .tick{ visibility:visible; }

  /* FAB hover */
  #scrollToTopBtn:hover, #reportFabBtn:hover { filter: brightness(.92); }

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
    {{-- ========== MAIN HEADER : START ========== --}}
    <header id="main-header" class="surface-bar shadow-weak">
      <nav class="mx-auto max-w-screen-xl px-4 lg:px-8 py-4 flex justify-between items-center">
        <a href="{{ route('home') }}" class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-teal-500 text-transparent bg-clip-text">
          VerseGate
        </a>
        <div class="text-center hidden md:block">
          <h1 class="font-semibold">{{ $story->title }}</h1>
          <p class="text-sm muted">{{ $chapter->title }}</p>
        </div>
        <button id="open-sidebar-btn" class="px-4 py-2 text-sm rounded-full soft-border hover:bg-black/5 transition-colors" aria-label="Buka daftar bab">
          <i class="fa-solid fa-list-ul sm:mr-2"></i>
          <span class="hidden sm:inline">Daftar Bab</span>
        </button>
      </nav>
    </header>
    {{-- ========== /MAIN HEADER : END ========== --}}

    {{-- ========== READER CONTROLS : START ========== --}}
    <div id="reader-controls" class="surface-bar">
      <div class="mx-auto max-w-screen-xl px-4 lg:px-8 py-3 flex justify-between items-center">
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

      <a href="{{ route('stories.show', $story->slug) }}#comment-section" class="font-semibold text-center">Beri Ulasan Cerita</a>

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

  {{-- ===================== FABs ===================== --}}
  <button id="scrollToTopBtn"
    class="hidden fixed bottom-6 right-6 sm:bottom-8 sm:right-8
           bg-blue-600 text-white rounded-full shadow-lg w-14 h-14
           flex items-center justify-center"
    aria-label="Kembali ke atas">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m-7 7l7-7 7 7"/>
    </svg>
  </button>

  <button id="reportFabBtn"
    class="fixed bottom-24 right-6 sm:bottom-28 sm:right-8 z-[70]
           w-14 h-14 rounded-full bg-red-500 text-white shadow-lg
           flex items-center justify-center"
    aria-label="Laporkan masalah" title="Laporkan masalah">
    <i class="fa-solid fa-flag text-xl"></i>
  </button>

  {{-- ===================== REPORT MODAL ===================== --}}
  <div id="reportModal" class="fixed inset-0 z-[80] hidden" aria-modal="true" role="dialog" aria-labelledby="reportModalTitle">
    <div id="reportBackdrop" class="absolute inset-0 modal-overlay"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="surface-card soft-border shadow-weak w-full max-w-lg modal-card">
        <div class="p-4 modal-divider">
          <div class="flex items-center justify-between">
            <h3 id="reportModalTitle" class="text-lg font-semibold">Laporkan masalah</h3>
            <button id="reportCloseBtn" class="modal-muted hover:text-blue-600" aria-label="Tutup dialog laporan">
              <i class="fa-solid fa-times text-xl"></i>
            </button>
          </div>
        </div>

        <form id="reportForm" class="p-4 space-y-3" method="POST" action="{{ route('reports.store') }}">
          @csrf
          <input type="hidden" name="story_id" value="{{ $story->id }}">
          <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
          <input type="hidden" name="page_url" value="{{ url()->current() }}">
          <input type="text" name="hp_field" class="hidden" tabindex="-1" autocomplete="off">

          <label class="block">
            <span class="text-sm modal-muted">Kategori</span>
            {{-- Custom Select --}}
            <div class="cselect mt-1" id="catSelect" role="combobox" aria-haspopup="listbox" aria-expanded="false">
              <button type="button" class="cselect-button" id="catSelectBtn" aria-labelledby="catSelectBtn catSelectLabel">
                <span id="catSelectLabel">Pilih…</span>
                <i class="chev" aria-hidden="true"></i>
              </button>
              <input type="hidden" name="category" id="catSelectValue" value="">
              <ul class="cselect-panel hidden" id="catSelectList" role="listbox" aria-labelledby="catSelectBtn" tabindex="-1">
                <li class="cselect-option" role="option" data-value="Typo"><i class="tick"></i><span>Typo</span></li>
                <li class="cselect-option" role="option" data-value="Link rusak"><i class="tick"></i><span>Link rusak</span></li>
                <li class="cselect-option" role="option" data-value="Bug UI/UX"><i class="tick"></i><span>Bug UI/UX</span></li>
                <li class="cselect-option" role="option" data-value="Konten tidak sesuai"><i class="tick"></i><span>Konten tidak sesuai</span></li>
                <li class="cselect-option" role="option" data-value="Lainnya"><i class="tick"></i><span>Lainnya</span></li>
              </ul>
            </div>
          </label>

          <label class="block">
            <span class="text-sm modal-muted">Jelaskan masalahnya</span>
            <textarea name="description" rows="4" required minlength="10" maxlength="2000"
              class="ck-textarea mt-1"
              placeholder="Contoh: paragraf ke-3 ada typo, tombol 'Bab Selanjutnya' tidak bisa diklik, dsb."></textarea>
          </label>

          <div class="flex items-center justify-end gap-2 pt-2">
            <button type="button" id="reportCancelBtn" class="ck-btn ck-btn-outline">Batal</button>
            <button type="submit" id="reportSubmitBtn" class="ck-btn ck-btn-primary">Kirim</button>
          </div>

          <p id="reportSuccess" class="hidden text-green-600 text-sm mt-1">
            Terima kasih! Laporan kamu sudah terkirim.
          </p>
          <p id="reportError" class="hidden text-red-600 text-sm mt-1">
            Gagal mengirim laporan. Coba lagi.
          </p>
        </form>
      </div>
    </div>
  </div>
</div>
{{-- ===================== /READER WRAPPER ===================== --}}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  /* ============================================================
   *  CACHE DOM
   * ============================================================ */
  const wrapper = document.getElementById('reader-wrapper');
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

  // Report elements
  const reportFabBtn     = document.getElementById('reportFabBtn');
  const reportModal      = document.getElementById('reportModal');
  const reportBackdrop   = document.getElementById('reportBackdrop');
  const reportCloseBtn   = document.getElementById('reportCloseBtn');
  const reportCancelBtn  = document.getElementById('reportCancelBtn');
  const reportForm       = document.getElementById('reportForm');
  const reportSuccess    = document.getElementById('reportSuccess');
  const reportError      = document.getElementById('reportError');
  const reportSubmitBtn  = document.getElementById('reportSubmitBtn');

  /* ============================================================
   *  THEME INIT + PERSIST + SYNC BODY
   * ============================================================ */
  document.body.classList.add('reader-bg-sync');

  function systemTheme(){
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function syncPageBackgroundToTheme() {
    if (!wrapper) return;
    const cs   = getComputedStyle(wrapper);
    const back = (cs.getPropertyValue('--back') || '').trim();
    if (back) document.body.style.setProperty('--ck-page-back', back);
  }

  function applyTheme(t){
    wrapper?.setAttribute('data-theme', t);
    try { localStorage.setItem('ck_theme', t); } catch {}
    syncPageBackgroundToTheme();
  }

  const savedTheme = (()=>{ try { return localStorage.getItem('ck_theme'); } catch { return null } })();
  applyTheme(savedTheme || systemTheme());

  themeBtns.forEach(btn=>{
    btn.addEventListener('click', ()=>{
      applyTheme(btn.dataset.theme || 'light');
      themePopup?.classList.add('hidden');
    });
  });

  try {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
      const saved = localStorage.getItem('ck_theme');
      if (!saved) applyTheme(systemTheme());
    });
  } catch {}

  /* ============================================================
   *  FONT SIZE
   * ============================================================ */
  const setFontSize = px => { if (storyContent) storyContent.style.fontSize = px; };
  setFontSize('18px');
  decFont?.addEventListener('click', () => { setFontSize('16px'); fontPopup?.classList.add('hidden'); });
  norFont?.addEventListener('click', () => { setFontSize('18px'); fontPopup?.classList.add('hidden'); });
  incFont?.addEventListener('click', () => { setFontSize('22px'); fontPopup?.classList.add('hidden'); });

  /* ============================================================
   *  POPUPS
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

  /* ============================================================
   *  REPORT MODAL (open/close + a11y + submit)
   * ============================================================ */
  function lockScroll(lock) { document.body.style.overflow = lock ? 'hidden' : ''; }
  function trapFocus(container, evt) {
    const focusables = container.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    if (!focusables.length) return;
    const first = focusables[0], last = focusables[focusables.length - 1];
    if (evt.key !== 'Tab') return;
    if (evt.shiftKey && document.activeElement === first) { last.focus(); evt.preventDefault(); }
    else if (!evt.shiftKey && document.activeElement === last) { first.focus(); evt.preventDefault(); }
  }

  function openReport(){
    reportModal?.classList.remove('hidden');
    lockScroll(true);
    reportModal.querySelector('button, select, textarea, input, [href]')?.focus();
  }
  function closeReport(){
    reportModal?.classList.add('hidden');
    lockScroll(false);
    reportFabBtn?.focus();
  }

  reportFabBtn?.addEventListener('click', openReport);
  reportBackdrop?.addEventListener('click', closeReport);
  reportCloseBtn?.addEventListener('click', closeReport);
  reportCancelBtn?.addEventListener('click', closeReport);

  reportModal?.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') { e.preventDefault(); closeReport(); }
    trapFocus(reportModal, e);
  });

  function showFieldErrors(form, errors){
    form.querySelectorAll('.field-error').forEach(el => el.remove());
    Object.entries(errors || {}).forEach(([name, msgs]) => {
      const input = form.querySelector(`[name="${name}"]`);
      if (!input) return;
      const p = document.createElement('p');
      p.className = 'field-error text-sm text-red-600 mt-1';
      p.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
      input.closest('label')?.appendChild(p);
    });
  }

  reportForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    reportSuccess?.classList.add('hidden');
    reportError?.classList.add('hidden');
    showFieldErrors(reportForm, {});

    try {
      reportSubmitBtn.disabled = true;
      const formData = new FormData(reportForm);
      const res = await fetch(reportForm.action, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': formData.get('_token'),
        },
        body: formData
      });

      if (res.status === 422) {
        const json = await res.json();
        showFieldErrors(reportForm, json.errors);
        reportError?.classList.remove('hidden');
        return;
      }

      if (!res.ok) throw new Error('Bad status: ' + res.status);
      const json = await res.json();
      if (json?.ok) {
        reportSuccess?.classList.remove('hidden');
        reportForm.reset();
        // reset custom select label juga
        const catLabel = document.getElementById('catSelectLabel');
        const catHidden = document.getElementById('catSelectValue');
        if (catLabel && catHidden){ catLabel.textContent = 'Pilih…'; catHidden.value = ''; }
        setTimeout(closeReport, 900);
      } else {
        throw new Error('Invalid response');
      }
    } catch (err) {
      reportError?.classList.remove('hidden');
      console.error(err);
    } finally {
      reportSubmitBtn.disabled = false;
    }
  });

  /* ===== Custom Select: kategori ===== */
  (function(){
    const root   = document.getElementById('catSelect');
    if (!root) return;
    const btn    = document.getElementById('catSelectBtn');
    const list   = document.getElementById('catSelectList');
    const label  = document.getElementById('catSelectLabel');
    const hidden = document.getElementById('catSelectValue');
    const options= Array.from(list.querySelectorAll('.cselect-option'));

    let open = false, activeIndex = -1;

    function setOpen(v){
      open = !!v;
      root.setAttribute('aria-expanded', open ? 'true' : 'false');
      list.classList.toggle('hidden', !open);
      if (open) { list.focus(); highlightByValue(hidden.value); }
    }
    function selectByIndex(i){
      if (i < 0 || i >= options.length) return;
      const opt = options[i];
      options.forEach(o => o.setAttribute('aria-selected','false'));
      opt.setAttribute('aria-selected','true');
      const val = opt.getAttribute('data-value');
      hidden.value = val;
      label.textContent = opt.innerText.trim();
      activeIndex = i;
      setOpen(false);
      btn.focus();
    }
    function highlightByValue(val){
      const i = options.findIndex(o => o.getAttribute('data-value') === val);
      activeIndex = i >= 0 ? i : 0;
      options.forEach((o, idx) => o.classList.toggle('is-active', idx === activeIndex));
    }

    btn.addEventListener('click', () => setOpen(!open));
    document.addEventListener('click', (e) => { if (!root.contains(e.target)) setOpen(false); });

    options.forEach((o, idx) => {
      o.addEventListener('mouseenter', () => activeIndex = idx);
      o.addEventListener('click', () => selectByIndex(idx));
    });

    btn.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
        e.preventDefault(); setOpen(true);
      }
    });

    list.addEventListener('keydown', (e) => {
      if (e.key === 'Escape'){ e.preventDefault(); setOpen(false); btn.focus(); return; }
      if (e.key === 'Enter'){ e.preventDefault(); selectByIndex(activeIndex); return; }
      if (e.key === 'ArrowDown'){ e.preventDefault(); activeIndex = Math.min(options.length-1, activeIndex+1); options[activeIndex].scrollIntoView({block:'nearest'}); return; }
      if (e.key === 'ArrowUp'){ e.preventDefault(); activeIndex = Math.max(0, activeIndex-1); options[activeIndex].scrollIntoView({block:'nearest'}); return; }
      if (e.key === 'Home'){ e.preventDefault(); activeIndex = 0; options[activeIndex].scrollIntoView({block:'nearest'}); return; }
      if (e.key === 'End'){ e.preventDefault(); activeIndex = options.length-1; options[activeIndex].scrollIntoView({block:'nearest'}); return; }
    });

    reportForm?.addEventListener('submit', (e) => {
      if (!hidden.value) { setOpen(true); list.focus(); }
    });
  })();
});
</script>
@endpush
