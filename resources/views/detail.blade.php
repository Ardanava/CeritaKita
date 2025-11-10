@extends('layouts.app')

@section('title', 'Detail Cerita - ' . $story->title)

@push('styles')
<style>
    /* ---- Quill display ---- */
    .quill-content-display { font-family: 'Lora', serif; }
    .quill-content-display .ql-align-center { text-align: center; }
    .quill-content-display .ql-align-right { text-align: right; }
    .quill-content-display .ql-align-justify { text-align: justify; }
    .quill-content-display .ql-align-left { text-align: left; }
    .quill-content-display blockquote {
        border-left: 4px solid #ccc;
        margin: 6px 0;
        padding-left: 16px;
        font-style: italic;
    }
    /* pastikan elemen di sinopsis tidak bikin overflow horizontal */
    .quill-content-display img { max-width: 100%; height: auto; }
    .quill-content-display table { display:block; width:100%; overflow-x:auto; }

    /* ---- Badges & status ---- */
    .status-berlanjut { animation: pulse-green 2s cubic-bezier(0.4,0,0.6,1) infinite; }
    @keyframes pulse-green { 0%,100%{opacity:1} 50%{opacity:.5} }

    .badge {
        display:inline-flex; align-items:center; gap:.4rem;
        font-size:.8125rem; font-weight:700;
        padding:.35rem .75rem; border-radius:9999px;
    }
    .badge-blue   { background:#DBEAFE; color:#1E40AF; }
    .badge-gray   { background:#E5E7EB; color:#374151; }
    .badge-green  { background:#DCFCE7; color:#166534; }
    .badge-gold   { background:#FEF3C7; color:#92400E; }
    .chip         { font-weight:600; font-size:.8125rem; padding:.35rem .75rem; border-radius:9999px; }

    /* Hilangkan spinner number (kalau suatu saat dipakai) */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance:none; margin:0; }
    input[type=number] { -moz-appearance:textfield; }
</style>
@endpush


@section('content')
<main class="container mx-auto px-4 lg:px-8 py-12">
    {{-- ===========================
         START: Informasi Cerita
    ============================ --}}
    <section class="bg-white p-6 sm:p-8 rounded-2xl shadow-lg">
        <div class="flex flex-col md:flex-row gap-8">

            {{-- ---- START: Cover Cerita ---- --}}
            <div class="w-full md:w-1/3 lg:w-1/4 flex-shrink-0">
                <img
                    src="{{ $story->cover_image_path ? Storage::url($story->cover_image_path) : 'https://placehold.co/600x800/3B82F6/FFFFFF?text=' . Str::limit($story->title, 10) }}"
                    alt="Cover Cerita {{ $story->title }}"
                    class="w-full h-auto rounded-lg shadow-md object-cover aspect-[2/3] bg-gray-100">
            </div>
            {{-- ---- END: Cover Cerita ---- --}}

            {{-- ---- START: Detail Cerita ---- --}}
            <div class="w-full md:w-2/3 lg:w-3/4 flex flex-col">
                {{-- ~~ START: Judul + Penulis ~~ --}}
                <div class="flex justify-between items-start">
                    <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight text-gray-900">{{ $story->title }}</h1>
                </div>

                <p class="mt-2 text-lg text-gray-600">
                    oleh
                    <a href="#" class="font-semibold text-blue-600 hover:underline">{{ $story->author_name }}</a>
                </p>
                {{-- ~~ END: Judul + Penulis ~~ --}}

                {{-- ~~ START: Badges / Tagline ~~ --}}
                <div class="mt-4 flex flex-wrap items-center gap-2 sm:gap-3">
                    @if($story->genres)
                        @foreach($story->genres as $genre)
                            <span class="badge badge-blue">{{ $genre }}</span>
                        @endforeach
                    @endif

                    @if(!empty($story->type))
                        <span class="badge badge-gray" title="Jenis Cerita">
                            <i class="fa-solid fa-book"></i>{{ $story->type }}
                        </span>
                    @endif

                    @if(!empty($story->original_language))
                        <span class="badge badge-gold" title="Asal Bahasa">
                            <i class="fa-solid fa-language"></i>{{ $story->original_language }}
                        </span>
                    @endif

                    @if($story->status === 'Berlanjut')
                        <span class="badge badge-green">
                            <i class="fa-solid fa-circle status-berlanjut text-xs"></i> Berlanjut
                        </span>
                    @else
                        <span class="badge badge-gray">
                            <i class="fa-solid fa-circle text-xs"></i> Tamat
                        </span>
                    @endif
                </div>
                {{-- ~~ END: Badges / Tagline ~~ --}}

                {{-- ~~ START: Info kecil (artist/translator/proofreader) ~~ --}}
                <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    @if(!empty($story->artist))
                        <div class="flex items-center text-gray-700">
                            <i class="fa-solid fa-paintbrush mr-2 text-gray-400"></i>
                            <span><span class="font-semibold">Artist:</span> {{ $story->artist }}</span>
                        </div>
                    @endif
                    @if(!empty($story->translator))
                        <div class="flex items-center text-gray-700">
                            <i class="fa-solid fa-language mr-2 text-gray-400"></i>
                            <span><span class="font-semibold">Translator:</span> {{ $story->translator }}</span>
                        </div>
                    @endif
                    @if(!empty($story->proofreader))
                        <div class="flex items-center text-gray-700">
                            <i class="fa-solid fa-spell-check mr-2 text-gray-400"></i>
                            <span><span class="font-semibold">Proofreader:</span> {{ $story->proofreader }}</span>
                        </div>
                    @endif
                </div>
                {{-- ~~ END: Info kecil ~~ --}}

                {{-- ~~ START: Statistik ~~ --}}
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <div class="flex flex-wrap items-center gap-6 text-gray-600">
                        @if($rank && $primaryGenre)
                            <div class="flex items-center">
                                <i class="fa-solid fa-fire-flame-curved text-xl text-red-500 mr-3"></i>
                                <div>
                                    <p class="font-bold text-lg leading-tight">#{{ $rank }}</p>
                                    <p class="text-xs">dalam
                                        <a href="#" class="font-semibold text-blue-600 hover:underline">{{ $primaryGenre }}</a>
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center">
                            <i class="fa-solid fa-eye text-xl text-gray-500 mr-3"></i>
                            <div>
                                <p class="font-bold text-lg leading-tight">{{ number_format($story->views, 0, ',', '.') }}</p>
                                <p class="text-xs">Kali Dibaca</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <i class="fa-solid fa-file-lines text-xl text-gray-500 mr-3"></i>
                            <div>
                                <p class="font-bold text-lg leading-tight">{{ $chapters->count() }}</p>
                                <p class="text-xs">Total Bab Ditampilkan</p>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ~~ END: Statistik ~~ --}}

                {{-- ~~ START: Sinopsis ~~ --}}
                <div class="mt-6 quill-content-display">
                    <h2 class="text-xl font-bold mb-2">Sinopsis</h2>
                    <div class="text-gray-700 leading-relaxed">
                        {!! $story->synopsis !!}
                    </div>
                </div>
                {{-- ~~ END: Sinopsis ~~ --}}

                {{-- ~~ START: Tombol Aksi ~~ --}}
                <div class="mt-auto pt-6 flex flex-col sm:flex-row flex-wrap gap-4">
                    @if($firstChapter)
                        <a href="{{ route('stories.chapter', ['storySlug' => $story->slug, 'chapterSlug' => $firstChapter->slug]) }}"
                           class="w-full sm:w-auto flex-grow text-center bg-blue-600 text-white font-bold py-3 px-8 rounded-full hover:bg-blue-700 transition-transform hover:scale-105 shadow-lg">
                            <i class="fa-solid fa-book-open mr-2"></i>
                            Mulai Membaca
                        </a>
                    @else
                        <span class="w-full sm:w-auto flex-grow text-center bg-gray-400 text-white font-bold py-3 px-8 rounded-full shadow-lg opacity-70 cursor-not-allowed">
                            <i class="fa-solid fa-book-open mr-2"></i>
                            Belum Ada Bab
                        </span>
                    @endif

                    <a href="#" class="w-full sm:w-auto text-center bg-green-500 text-white font-bold py-3 px-8 rounded-full hover:bg-green-600 transition-transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                        <i class="fa-solid fa-mug-hot"></i>
                        Donasi
                    </a>
                </div>
                {{-- ~~ END: Tombol Aksi ~~ --}}
            </div>
            {{-- ---- END: Detail Cerita ---- --}}

        </div>
    </section>
    {{-- ===========================
         END: Informasi Cerita
    ============================ --}}

    {{-- ===========================
         START: Daftar Isi
    ============================ --}}
    <section id="chapters" class="mt-12">
      {{-- ---- START: Header Daftar Isi + Filter ---- --}}
      <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold">Daftar Isi</h2>

        {{-- Form GET untuk search + sort (dipakai dropdown kustom) --}}
        <form id="chapter-filter-form" method="GET" action="{{ route('stories.show', $story->slug) }}"
              class="flex flex-col sm:flex-row w-full sm:w-auto gap-4">
          {{-- ~~ START: Pencarian ~~ --}}
          <div class="relative w-full sm:w-64">
            <input id="chapter-search-input" type="text" name="q" value="{{ old('q', $q) }}" placeholder="Cari judul bab..."
                   class="w-full bg-white border border-gray-300 rounded-full py-2 pl-10 pr-4
                          focus:outline-none focus:ring-2 focus:ring-blue-500">
            <i class="fa-solid fa-search text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
          </div>
          {{-- ~~ END: Pencarian ~~ --}}

          {{-- ~~ START: Dropdown kustom: Urutkan ~~ --}}
          <div id="sort-dropdown" class="relative w-full sm:w-auto">
            <button id="sort-dd-btn" type="button"
                    class="w-full sm:w-56 bg-white border border-gray-300 rounded-full py-2 pl-4 pr-10 text-left font-semibold
                           hover:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                    aria-haspopup="menu" aria-expanded="false">
              <span id="sort-dd-label">
                Urutkan: {{ ($sort === 'terbaru') ? 'Terbaru' : 'Terlama' }}
              </span>
              <i class="fa-solid fa-chevron-down text-gray-400 absolute right-3 top-1/2 -translate-y-1/2"></i>
            </button>

            <div id="sort-dd-menu"
                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl ring-1 ring-black/5 p-2 hidden
                        origin-top scale-95 opacity-0 transition"
                 role="menu" aria-labelledby="sort-dd-btn">
              <button type="button"
                      class="sort-option w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-gray-50 text-gray-700"
                      data-value="terlama" aria-selected="{{ $sort === 'terlama' ? 'true' : 'false' }}">
                <span>Urutkan: Terlama</span>
                <i class="fa-solid fa-check text-blue-600 {{ $sort === 'terlama' ? '' : 'hidden' }}"></i>
              </button>
              <button type="button"
                      class="sort-option w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-gray-50 text-gray-700"
                      data-value="terbaru" aria-selected="{{ $sort === 'terbaru' ? 'true' : 'false' }}">
                <span>Urutkan: Terbaru</span>
                <i class="fa-solid fa-check text-blue-600 {{ $sort === 'terbaru' ? '' : 'hidden' }}"></i>
              </button>
            </div>

            {{-- Hidden untuk nilai sort di form GET --}}
            <input type="hidden" name="sort" id="chapter-sort-value" value="{{ $sort }}">
          </div>
          {{-- ~~ END: Dropdown kustom: Urutkan ~~ --}}

          {{-- Tombol submit agar bisa submit manual di mobile (opsional) --}}
          <button type="submit"
                  class="sm:hidden inline-flex items-center justify-center bg-blue-600 text-white font-semibold px-4 py-2 rounded-full">
            Terapkan
          </button>
        </form>
      </div>
      {{-- ---- END: Header Daftar Isi + Filter ---- --}}

      {{-- ---- START: Daftar Bab ---- --}}
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div id="chapter-list-container" class="divide-y divide-gray-200">
          @forelse ($chapters as $chapter)
            <a href="{{ route('stories.chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug]) }}"
               class="chapter-item block p-5 hover:bg-gray-50 transition-colors group">
              <div class="flex justify-between items-center">
                <div>
                  <h3 class="font-semibold text-lg text-gray-900 group-hover:text-blue-600 transition-colors">
                    {{ $chapter->title }}
                  </h3>
                  <p class="text-sm text-gray-500 mt-1">
                    Diunggah pada {{ $chapter->created_at->format('d F Y') }}
                  </p>
                </div>
                <div class="flex items-center text-gray-400 group-hover:text-blue-600 transition-colors">
                  <span class="hidden sm:inline font-semibold text-sm mr-2">Baca</span>
                  <i class="fa-solid fa-chevron-right"></i>
                </div>
              </div>
            </a>
          @empty
            <div class="p-5 text-center text-gray-500">
              <i class="fa-solid fa-feather text-3xl mb-3"></i>
              <p>Tidak ada bab yang ditemukan.</p>
            </div>
          @endforelse
        </div>
      </div>
      {{-- ---- END: Daftar Bab ---- --}}

      {{-- ---- START: Pagination ---- --}}
      @if ($chapters->hasPages())
        <div class="mt-6 flex justify-center">
          <nav class="chapter-pagination flex items-center gap-2" aria-label="Pagination">
            {{-- Prev --}}
            @if ($chapters->onFirstPage())
              <span class="px-3 py-2 rounded-lg bg-gray-200 text-gray-500 font-semibold shadow-sm cursor-not-allowed">&laquo;</span>
            @else
              <a href="{{ $chapters->previousPageUrl() }}"
                 class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm">&laquo;</a>
            @endif

            {{-- Numbered --}}
            @php
              $start = max(1, $chapters->currentPage() - 2);
              $end   = min($chapters->lastPage(), $chapters->currentPage() + 2);
            @endphp
            @if ($start > 1)
              <a href="{{ $chapters->url(1) }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm">1</a>
              @if ($start > 2)
                <span class="px-2 text-gray-400">…</span>
              @endif
            @endif

            @for ($page = $start; $page <= $end; $page++)
              @if ($page == $chapters->currentPage())
                <span class="px-3 py-2 rounded-lg bg-blue-600 text-white font-semibold shadow-md">{{ $page }}</span>
              @else
                <a href="{{ $chapters->url($page) }}"
                   class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm">{{ $page }}</a>
              @endif
            @endfor

            @if ($end < $chapters->lastPage())
              @if ($end < $chapters->lastPage() - 1)
                <span class="px-2 text-gray-400">…</span>
              @endif
              <a href="{{ $chapters->url($chapters->lastPage()) }}"
                 class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm">{{ $chapters->lastPage() }}</a>
            @endif

            {{-- Next --}}
            @if ($chapters->hasMorePages())
              <a href="{{ $chapters->nextPageUrl() }}"
                 class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm">&raquo;</a>
            @else
              <span class="px-3 py-2 rounded-lg bg-gray-200 text-gray-500 font-semibold shadow-sm cursor-not-allowed">&raquo;</span>
            @endif
          </nav>
        </div>
      @endif
      {{-- ---- END: Pagination ---- --}}
    </section>
    {{-- ===========================
         END: Daftar Isi
    ============================ --}}

    {{-- ===========================
         START: Komentar (Disqus)
    ============================ --}}
    <section id="comment-section" class="mt-12">
        <h2 class="text-2xl font-bold mb-6">Diskusi & Komentar</h2>
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200">
            <div id="disqus_thread" class="bg-white p-6 rounded-xl">
                <p class="text-center text-gray-500">
                    <i class="fa-solid fa-comments text-3xl mb-3 block"></i>
                    Area ini akan dimuat oleh <strong>Disqus</strong> saat aplikasi Anda di-deploy.
                </p>
            </div>
        </div>
    </section>
    {{-- ===========================
         END: Komentar (Disqus)
    ============================ --}}
</main>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
    /* ===========================
    *  START: Utilitas kecil
    * =========================== */
    const $ = (sel, ctx = document) => ctx.querySelector(sel);
    const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
    const debounce = (fn, wait = 300) => {
        let t; 
        return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); };
    };
    const smoothScrollTo = (el) => {
        if (!el) return;
        const top = el.getBoundingClientRect().top + window.pageYOffset - 24; // offset kecil
        window.scrollTo({ top, behavior: 'smooth' });
    };
    /* ===========================
    *  END: Utilitas kecil
    * =========================== */

    /* ===========================
    *  START: Elemen yang dipakai
    * =========================== */
    const form       = $('#chapter-filter-form');
    const searchIn   = $('#chapter-search-input');  // FIX: id ditambahkan pada input
    const listWrap   = $('#chapter-list-container');

    // Dropdown Sort
    const ddWrap     = $('#sort-dropdown');
    const ddBtn      = $('#sort-dd-btn');
    const ddMenu     = $('#sort-dd-menu');
    const ddLabel    = $('#sort-dd-label');
    const sortInput  = $('#chapter-sort-value');
    /* ===========================
    *  END: Elemen yang dipakai
    * =========================== */

    /* ===========================
    *  START: Pencarian (auto-submit)
    * =========================== */
    if (form && searchIn) {
        const doSubmit = debounce(() => {
            form.requestSubmit ? form.requestSubmit() : form.submit();
        }, 400);
        searchIn.addEventListener('input', () => { doSubmit(); });
    }
    /* ===========================
    *  END: Pencarian
    * =========================== */

    /* ===========================
    *  START: Dropdown “Urutkan”
    * =========================== */
    function openMenu() {
        if (!ddMenu || !ddBtn) return;
        ddMenu.classList.remove('hidden');
        requestAnimationFrame(() => ddMenu.classList.remove('scale-95','opacity-0'));
        ddBtn.setAttribute('aria-expanded', 'true');

        const active = ddMenu.querySelector('.sort-option[aria-selected="true"]') || ddMenu.querySelector('.sort-option');
        active && active.focus();
    }
    function closeMenu() {
        if (!ddMenu || ddMenu.classList.contains('hidden')) return;
        ddMenu.classList.add('scale-95','opacity-0');
        setTimeout(() => ddMenu.classList.add('hidden'), 120);
        ddBtn.setAttribute('aria-expanded', 'false');
        ddBtn.focus();
    }
    function isOutside(target) { return !(ddWrap && ddWrap.contains(target)); }

    ddBtn && ddBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const willOpen = ddMenu.classList.contains('hidden');
        willOpen ? openMenu() : closeMenu();
    });

    if (ddMenu) {
        const options = $$('.sort-option', ddMenu);
        options.forEach(opt => {
            opt.setAttribute('role', 'menuitemradio');
            opt.setAttribute('tabindex', '0');

            opt.addEventListener('click', (e) => {
                e.preventDefault(); e.stopPropagation(); selectOption(opt);
            });
            opt.addEventListener('keydown', (e) => {
                const idx = options.indexOf(opt);
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); selectOption(opt); }
                else if (e.key === 'ArrowDown') { e.preventDefault(); (options[idx + 1] || options[0]).focus(); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); (options[idx - 1] || options[options.length - 1]).focus(); }
                else if (e.key === 'Escape') { e.preventDefault(); closeMenu(); }
            });
        });

        function selectOption(opt) {
            const val = opt.dataset.value;
            if (!val) return;

            ddLabel.textContent = 'Urutkan: ' + (val === 'terbaru' ? 'Terbaru' : 'Terlama');
            if (sortInput) sortInput.value = val;

            $$('.sort-option .fa-check', ddMenu).forEach(i => i.classList.add('hidden'));
            $$('.sort-option', ddMenu).forEach(o => o.setAttribute('aria-selected', 'false'));
            opt.setAttribute('aria-selected', 'true');
            opt.querySelector('.fa-check')?.classList.remove('hidden');

            closeMenu();
            if (form) { form.requestSubmit ? form.requestSubmit() : form.submit(); }
        }
    }

    document.addEventListener('click', (e) => { if (isOutside(e.target)) closeMenu(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMenu(); });

    let resizeTO;
    window.addEventListener('resize', () => { clearTimeout(resizeTO); resizeTO = setTimeout(() => closeMenu(), 150); });
    /* ===========================
    *  END: Dropdown “Urutkan”
    * =========================== */

    /* ===========================
    *  START: Scroll ke daftar bab
    * =========================== */
    if (location.hash === '#chapters' && listWrap) {
        setTimeout(() => smoothScrollTo(listWrap), 60);
    }
    /* ===========================
    *  END: Scroll ke daftar bab
    * =========================== */

    /* ===========================
    *  START: Intersep paginasi (tambahkan #chapters)
    * =========================== */
    $$('.chapter-pagination a').forEach(a => {
        a.addEventListener('click', () => {
            const url = new URL(a.href, location.origin);
            url.hash = 'chapters';
            a.href = url.toString();
        });
    });
    /* ===========================
    *  END: Intersep paginasi
    * =========================== */
    });
</script>
@endpush
