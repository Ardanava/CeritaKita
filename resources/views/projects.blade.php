{{-- resources/views/projects/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Proyek Kami')

@push('styles')
<style>
/* ===== START: Fancy Select (single) ===== */
.dd{position:relative;width:100%}
.dd.is-open .dd-trigger{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.25)}
.dd-trigger{width:100%;display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.625rem .875rem;border:1px solid #d1d5db;border-radius:.75rem;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.03);transition:border-color .15s,box-shadow .15s;cursor:pointer}
.dd-label{display:inline-flex;align-items:center;gap:.5rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.dd-caret{flex:0 0 auto;display:inline-flex}
.dd-menu{position:absolute;left:0;right:0;top:calc(100% + .25rem);z-index:50;background:#fff;border:1px solid #e5e7eb;border-radius:.75rem;box-shadow:0 12px 28px rgba(0,0,0,.12);padding:.35rem;display:none;max-height:16rem;overflow:auto}
.dd.is-open .dd-menu{display:block}
.dd-item{display:flex;align-items:center;gap:.6rem;padding:.55rem .65rem;border-radius:.5rem;cursor:pointer;transition:background-color .12s,color .12s}
.dd-item:hover{background:#f3f4f6}
.dd-item[aria-selected="true"]{background:#eff6ff;color:#1e40af;font-weight:600}
/* ===== END: Fancy Select (single) ===== */

/* ===== START: Badges ===== */
.badge{display:inline-flex;align-items:center;gap:.35rem;padding:.2rem .55rem;border-radius:9999px;font-size:.75rem;font-weight:700}
.badge-status-ongoing{background:#dcfce7;color:#166534}
.badge-status-completed{background:#e0e7ff;color:#3730a3}
.badge-status-hiatus{background:#fef9c3;color:#854d0e}
.badge-status-dropped{background:#fee2e2;color:#991b1b}
.badge-count{background:#eef2ff;color:#3730a3}
.genre-chip{background:#f1f5f9;color:#334155}
/* ===== END: Badges ===== */

/* ===== START: Collapsible filter (mobile) ===== */
.filter-panel{transition:max-height .3s ease,opacity .2s ease,transform .2s ease}
.filter-collapsed{max-height:0;opacity:0;transform:translateY(-4px);pointer-events:none}
.filter-expanded{max-height:2000px;opacity:1;transform:translateY(0);pointer-events:auto}
@media (min-width:768px){.filter-panel{max-height:none!important;opacity:1!important;transform:none!important;pointer-events:auto!important}}
/* ===== END: Collapsible filter (mobile) ===== */
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-10">
  <h1 class="text-3xl font-bold text-gray-900 mb-6">Proyek Kami</h1>

  {{-- ===== START: FILTER FORM ===== --}}
  <form method="GET" action="{{ route('proyek.kami') }}" class="space-y-3" x-data>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">

      {{-- ---- START: Filter Header ---- --}}
      <div class="px-4 md:px-5 py-3 flex items-center justify-between md:justify-start md:gap-6 border-b">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-sliders text-gray-500"></i>
          <span class="font-semibold text-gray-800">Filter</span>
        </div>

        {{-- ~~ START: Toggle (mobile) ~~ --}}
        <button type="button" class="md:hidden inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition" data-filter-btn aria-controls="filter-panel" aria-expanded="false">
          <span>Atur</span>
          <i class="fa-solid fa-chevron-down transition-transform" data-filter-caret></i>
        </button>
        {{-- ~~ END: Toggle (mobile) ~~ --}}

        {{-- ~~ START: Header Actions (desktop) ~~ --}}
        <div class="hidden md:flex items-center gap-3 ml-auto">
          <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">Terapkan</button>
          <a href="{{ route('proyek.kami') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-gray-200 text-gray-800 font-semibold hover:bg-gray-300">Reset</a>
        </div>
        {{-- ~~ END: Header Actions (desktop) ~~ --}}
      </div>
      {{-- ---- END: Filter Header ---- --}}

      {{-- ---- START: Filter Panel (collapsible) ---- --}}
      <div id="filter-panel" class="filter-panel filter-collapsed md:filter-expanded px-4 md:px-5 py-4 space-y-4" data-filter-panel>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

          {{-- ~~ START: Cari ~~ --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Cari</label>
            <input type="text" name="q" value="{{ request('q') }}" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" autocomplete="off" placeholder="Cari judul atau penulis…">
          </div>
          {{-- ~~ END: Cari ~~ --}}

          {{-- ~~ START: Status (fancy select) ~~ --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
            @php $status = request('status','all'); @endphp
            <select id="sel-status" name="status" class="hidden">
              <option value="all"       {{ $status==='all' ? 'selected':'' }}>Semua</option>
              <option value="ongoing"   {{ $status==='ongoing' ? 'selected':'' }}>Berlanjut</option>
              <option value="completed" {{ $status==='completed' ? 'selected':'' }}>Tamat</option>
              <option value="hiatus"    {{ $status==='hiatus' ? 'selected':'' }}>Hiatus</option>
              <option value="dropped"   {{ $status==='dropped' ? 'selected':'' }}>Drop</option>
            </select>
            <div class="dd" data-dd="sel-status">
              <button type="button" class="dd-trigger" aria-haspopup="listbox" aria-expanded="false">
                <span class="dd-label">Semua</span>
                <span class="dd-caret">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </span>
              </button>
              <ul class="dd-menu" role="listbox" tabindex="-1"></ul>
            </div>
          </div>
          {{-- ~~ END: Status (fancy select) ~~ --}}

          {{-- ~~ START: Genre (fancy select) ~~ --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Genre</label>
            @php $genre = request('genre','all'); @endphp
            <select id="sel-genre" name="genre" class="hidden">
              <option value="all" {{ $genre==='all' ? 'selected':'' }}>Semua Genre</option>
              @foreach($allGenres as $g)
                <option value="{{ $g }}" {{ $genre===$g ? 'selected':'' }}>{{ $g }}</option>
              @endforeach
            </select>
            <div class="dd" data-dd="sel-genre">
              <button type="button" class="dd-trigger" aria-haspopup="listbox" aria-expanded="false">
                <span class="dd-label">Semua Genre</span>
                <span class="dd-caret">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </span>
              </button>
              <ul class="dd-menu" role="listbox" tabindex="-1"></ul>
            </div>
          </div>
          {{-- ~~ END: Genre (fancy select) ~~ --}}

          {{-- ~~ START: Min/Maks Bab ~~ --}}
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Min Bab</label>
              <input type="number" min="0" name="min_chapters" value="{{ request('min_chapters',0) }}" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Maks Bab</label>
              <input type="number" min="0" name="max_chapters" value="{{ request('max_chapters',999) }}" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="999">
            </div>
          </div>
          {{-- ~~ END: Min/Maks Bab ~~ --}}
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
          {{-- ~~ START: Urutkan (fancy select) ~~ --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Urutkan</label>
            @php $sort = request('sort','latest'); @endphp
            <select id="sel-sort" name="sort" class="hidden">
              <option value="latest"   {{ $sort==='latest' ? 'selected':'' }}>Terbaru</option>
              <option value="chapters" {{ $sort==='chapters' ? 'selected':'' }}>Bab terbanyak</option>
              <option value="title_az" {{ $sort==='title_az' ? 'selected':'' }}>Judul A–Z</option>
            </select>
            <div class="dd" data-dd="sel-sort">
              <button type="button" class="dd-trigger" aria-haspopup="listbox" aria-expanded="false">
                <span class="dd-label">Terbaru</span>
                <span class="dd-caret">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </span>
              </button>
              <ul class="dd-menu" role="listbox" tabindex="-1"></ul>
            </div>
          </div>
          {{-- ~~ END: Urutkan (fancy select) ~~ --}}

          {{-- ~~ START: Panel Actions (mobile) ~~ --}}
          <div class="md:col-span-2 flex gap-3 items-end md:hidden">
            <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 w-full">Terapkan</button>
            <a href="{{ route('proyek.kami') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-gray-200 text-gray-800 font-semibold hover:bg-gray-300 w-full">Reset</a>
          </div>
          {{-- ~~ END: Panel Actions (mobile) ~~ --}}
        </div>
      </div>
      {{-- ---- END: Filter Panel (collapsible) ---- --}}
    </div>
  </form>
  {{-- ===== END: FILTER FORM ===== --}}

  {{-- ===== START: TABEL LIST ===== --}}
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-6 overflow-hidden">
    {{-- ---- START: Table Wrapper ---- --}}
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
            <th class="px-6 py-3">Judul Cerita</th>
            <th class="px-6 py-3">Author</th>
            <th class="px-6 py-3">Status</th>
            <th class="px-6 py-3">Jumlah Bab</th>
            <th class="px-6 py-3">Update Terakhir</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @forelse($stories as $story)
            @php
              $statusKey = match(strtolower($story->status ?? '')) {
                'berlanjut','ongoing'  => 'ongoing',
                'tamat','completed'    => 'completed',
                'hiatus'               => 'hiatus',
                'drop','dropped'       => 'dropped',
                default                => 'ongoing',
              };
              $statusText = match($statusKey){
                'ongoing'   => 'Berlanjut',
                'completed' => 'Tamat',
                'hiatus'    => 'Hiatus',
                'dropped'   => 'Drop',
              };
            @endphp

            {{-- ~~ START: Table Row ~~ --}}
            <tr>
              <td class="px-6 py-4">
                <a href="{{ route('stories.show', $story->slug) }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 hover:underline">{{ $story->title }}</a>
                <div class="mt-2 flex flex-wrap gap-2">
                  @foreach((array)($story->genres ?? []) as $g)
                    <span class="badge genre-chip">{{ $g }}</span>
                  @endforeach
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ $story->user->name ?? 'Admin' }}</td>
              <td class="px-6 py-4"><span class="badge {{ 'badge-status-'.$statusKey }}">{{ $statusText }}</span></td>
              <td class="px-6 py-4"><span class="badge badge-count">{{ $story->chapters_count }} Bab</span></td>
              <td class="px-6 py-4 text-sm text-gray-500">{{ ($story->updated_at ?? $story->created_at)->format('d M Y') }}</td>
            </tr>
            {{-- ~~ END: Table Row ~~ --}}
          @empty
            {{-- ~~ START: Empty Row ~~ --}}
            <tr>
              <td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada cerita yang cocok dengan filter.</td>
            </tr>
            {{-- ~~ END: Empty Row ~~ --}}
          @endforelse
        </tbody>
      </table>
    </div>
    {{-- ---- END: Table Wrapper ---- --}}

    {{-- ---- START: Pagination ---- --}}
    @if(method_exists($stories, 'hasPages') && $stories->hasPages())
      <div class="p-4 flex justify-center border-t">
        {{ $stories->appends(request()->query())->links() }}
      </div>
    @endif
    {{-- ---- END: Pagination ---- --}}
  </div>
  {{-- ===== END: TABEL LIST ===== --}}
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // ===== START: Fancy Select init =====
  document.querySelectorAll('.dd[data-dd]').forEach(initFancySelect);

  function initFancySelect(wrap){
    const selectId = wrap.getAttribute('data-dd');
    const selectEl = document.getElementById(selectId);
    const trigger  = wrap.querySelector('.dd-trigger');
    const labelEl  = wrap.querySelector('.dd-label');
    const menu     = wrap.querySelector('.dd-menu');
    if(!selectEl||!trigger||!labelEl||!menu) return;

    menu.innerHTML = '';
    Array.from(selectEl.options).forEach(opt => {
      const li = document.createElement('li');
      li.className = 'dd-item';
      li.setAttribute('role','option');
      li.setAttribute('data-value', opt.value);
      li.setAttribute('aria-selected', String(opt.selected));
      li.textContent = opt.textContent;
      li.addEventListener('click', () => choose(opt.value));
      menu.appendChild(li);
    });

    setLabel(selectEl.selectedOptions[0]?.textContent || '');

    function open(){ wrap.classList.add('is-open'); trigger.setAttribute('aria-expanded','true'); menu.focus(); }
    function close(){ wrap.classList.remove('is-open'); trigger.setAttribute('aria-expanded','false'); }
    function choose(val){
      if (selectEl.value !== val){
        selectEl.value = val;
        selectEl.dispatchEvent(new Event('change', { bubbles:true }));
      }
      setLabel(selectEl.selectedOptions[0]?.textContent || '');
      updateMenuSelected();
      close();
    }
    function setLabel(text){ labelEl.textContent = text; }
    function updateMenuSelected(){
      const v = selectEl.value;
      menu.querySelectorAll('.dd-item').forEach(it => it.setAttribute('aria-selected', String(it.getAttribute('data-value')===v)));
    }

    trigger.addEventListener('click', e => { e.preventDefault(); wrap.classList.contains('is-open') ? close() : open(); });
    selectEl.addEventListener('change', updateMenuSelected);
    document.addEventListener('click', e => { if(!wrap.contains(e.target)) close(); });
    document.addEventListener('keydown', e => { if(e.key==='Escape') close(); });
  }
  // ===== END: Fancy Select init =====

  // ===== START: Collapsible filter (mobile) =====
  const panel = document.querySelector('[data-filter-panel]');
  const btn   = document.querySelector('[data-filter-btn]');
  const caret = document.querySelector('[data-filter-caret]');
  let isOpen = false;

  function setState(open){
    isOpen = open;
    if (!panel) return;
    panel.classList.toggle('filter-expanded', open);
    panel.classList.toggle('filter-collapsed', !open);
    btn?.setAttribute('aria-expanded', String(open));
    if (caret) caret.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
  }

  function syncToViewport(){ window.innerWidth >= 768 ? setState(true) : setState(false); }
  syncToViewport();
  window.addEventListener('resize', syncToViewport);
  btn?.addEventListener('click', () => setState(!isOpen));
  // ===== END: Collapsible filter (mobile) =====
});
</script>
@endpush
