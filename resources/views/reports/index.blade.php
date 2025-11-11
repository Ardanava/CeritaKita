@extends('layouts.app')

@section('title', 'Laporan Pengguna')

@push('styles')
<style>
/* ===== Fancy Select (single) ===== */
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

/* ===== Badge helper ===== */
.badge{display:inline-flex;align-items:center;gap:.35rem;padding:.2rem .55rem;border-radius:9999px;font-size:.75rem;font-weight:700}
.badge-open{background:#fee2e2;color:#991b1b;border:1px solid #fecaca}
.badge-inprogress{background:#fef9c3;color:#854d0e;border:1px solid #fde68a}
.badge-resolved{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}
</style>
@endpush

@section('content')
<section class="container mx-auto px-4 lg:px-8 py-6">
  <div class="flex items-center justify-between mb-5">
    <h1 class="text-2xl font-bold">Laporan Pengguna</h1>
    <a href="{{ route('admin.reports.index') }}" class="text-sm text-blue-600 hover:underline">Refresh</a>
  </div>

  {{-- Stats --}}
  <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded-xl border p-4">
      <p class="text-sm text-gray-500">Open</p>
      <p class="text-2xl font-bold">{{ $stats['open'] }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
      <p class="text-sm text-gray-500">In Progress</p>
      <p class="text-2xl font-bold">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
      <p class="text-sm text-gray-500">Resolved</p>
      <p class="text-2xl font-bold">{{ $stats['resolved'] }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
      <p class="text-sm text-gray-500">Total</p>
      <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
    </div>
  </div>

  {{-- Filter/Search --}}
  <form method="GET" class="bg-white border rounded-xl p-4 mb-6">
    <div class="grid md:grid-cols-4 gap-4">
      {{-- Cari --}}
      <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Cari</label>
        <input
          type="text"
          name="q"
          value="{{ $q }}"
          placeholder="Deskripsi, URL, IP, User-Agent"
          class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
          autocomplete="off"
        />
      </div>

      {{-- Status (fancy) --}}
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
        @php $currentStatus = $status ?? ''; @endphp
        <select id="sel-status" name="status" class="hidden">
          <option value="" {{ $currentStatus===''?'selected':'' }}>Semua</option>
          <option value="open" {{ $currentStatus==='open'?'selected':'' }}>Open</option>
          <option value="in_progress" {{ $currentStatus==='in_progress'?'selected':'' }}>In Progress</option>
          <option value="resolved" {{ $currentStatus==='resolved'?'selected':'' }}>Resolved</option>
        </select>
        <div class="dd mt-1" data-dd="sel-status">
          <button type="button" class="dd-trigger" aria-haspopup="listbox" aria-expanded="false">
            <span class="dd-label">Semua</span>
            <span class="dd-caret">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </span>
          </button>
          <ul class="dd-menu" role="listbox" tabindex="-1"></ul>
        </div>
      </div>

      {{-- Kategori (fancy) --}}
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori</label>
        @php $currentCat = $category ?? ''; @endphp
        <select id="sel-category" name="category" class="hidden">
          <option value="" {{ $currentCat===''?'selected':'' }}>Semua</option>
          @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ $currentCat===$cat?'selected':'' }}>{{ $cat }}</option>
          @endforeach
        </select>
        <div class="dd mt-1" data-dd="sel-category">
          <button type="button" class="dd-trigger" aria-haspopup="listbox" aria-expanded="false">
            <span class="dd-label">Semua</span>
            <span class="dd-caret">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </span>
          </button>
          <ul class="dd-menu" role="listbox" tabindex="-1"></ul>
        </div>
      </div>
    </div>

    <div class="mt-4 flex items-center justify-between gap-3 flex-wrap">
      {{-- Urutkan (fancy) --}}
      <div class="w-full md:w-auto">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Urutkan</label>
        @php $currentSort = $sort ?? '-created'; @endphp
        <select id="sel-sort" name="sort" class="hidden">
          <option value="-created" {{ $currentSort==='-created'?'selected':'' }}>Terbaru</option>
          <option value="+created" {{ $currentSort==='+created'?'selected':'' }}>Terlama</option>
        </select>
        <div class="dd mt-1 md:w-56" data-dd="sel-sort">
          <button type="button" class="dd-trigger" aria-haspopup="listbox" aria-expanded="false">
            <span class="dd-label">Terbaru</span>
            <span class="dd-caret">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </span>
          </button>
          <ul class="dd-menu" role="listbox" tabindex="-1"></ul>
        </div>
      </div>

      <div class="ml-auto flex items-center gap-3">
        <a href="{{ route('admin.reports.index') }}"
           class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-gray-200 text-gray-800 font-semibold hover:bg-gray-300">
          Reset
        </a>
        <button class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">
          Terapkan
        </button>
      </div>
    </div>
  </form>

  {{-- Flash --}}
  @if(session('ok'))
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-700 px-4 py-3">
      {{ session('ok') }}
    </div>
  @endif

  {{-- Tabel --}}
  <div class="overflow-hidden bg-white border rounded-xl">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-700">
          <tr>
            <th class="px-4 py-3 text-left font-semibold">Waktu</th>
            <th class="px-4 py-3 text-left font-semibold">Kategori</th>
            <th class="px-4 py-3 text-left font-semibold">Ringkas Deskripsi</th>
            <th class="px-4 py-3 text-left font-semibold">Halaman</th>
            <th class="px-4 py-3 text-left font-semibold">Status</th>
            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($reports as $r)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                {{ $r->created_at->format('Y-m-d H:i') }}
              </td>
              <td class="px-4 py-3">
                <span class="badge" style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe">{{ $r->category }}</span>
              </td>
              <td class="px-4 py-3">
                {{ \Illuminate\Support\Str::limit($r->description, 80) }}
              </td>
              <td class="px-4 py-3">
                @if($r->page_url)
                  <a class="text-blue-600 hover:underline" href="{{ $r->page_url }}" target="_blank" rel="noopener">Kunjungi</a>
                @else
                  <span class="text-gray-400">-</span>
                @endif
              </td>
              <td class="px-4 py-3">
                @php
                  $cls = $r->status==='open' ? 'badge-open' : ($r->status==='in_progress' ? 'badge-inprogress' : 'badge-resolved');
                @endphp
                <span class="badge {{ $cls }}">{{ $r->status }}</span>
              </td>
              <td class="px-4 py-3 text-right space-x-2">
                <a href="{{ route('admin.reports.show', $r) }}"
                   class="inline-flex items-center px-3 py-1.5 rounded-full border text-gray-700 hover:bg-gray-50">
                  Detail
                </a>

                @if($r->status !== 'in_progress')
                  <form action="{{ route('admin.reports.update', $r) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="in_progress">
                    <button class="inline-flex items-center px-3 py-1.5 rounded-full border text-amber-700 border-amber-300 bg-amber-50 hover:bg-amber-100">
                      In Progress
                    </button>
                  </form>
                @endif

                @if($r->status !== 'resolved')
                  <form action="{{ route('admin.reports.update', $r) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="resolved">
                    <button class="inline-flex items-center px-3 py-1.5 rounded-full border text-emerald-700 border-emerald-300 bg-emerald-50 hover:bg-emerald-100">
                      Resolved
                    </button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-10 text-center text-gray-500">Belum ada laporan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4 border-t">
      {{ $reports->links() }}
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Inisialisasi semua fancy select
  document.querySelectorAll('.dd[data-dd]').forEach(initFancySelect);

  function initFancySelect(wrap){
    const selectId = wrap.getAttribute('data-dd');
    const selectEl = document.getElementById(selectId);
    const trigger  = wrap.querySelector('.dd-trigger');
    const labelEl  = wrap.querySelector('.dd-label');
    const menu     = wrap.querySelector('.dd-menu');
    if(!selectEl||!trigger||!labelEl||!menu) return;

    // render opsi
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

    // label awal
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
});
</script>
@endpush
