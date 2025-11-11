@extends('layouts.app')

@section('title', 'Detail Laporan #'.$report->id)

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

/* Badges untuk status di tempat lain (opsional) */
.badge{display:inline-flex;align-items:center;gap:.35rem;padding:.2rem .55rem;border-radius:9999px;font-size:.75rem;font-weight:700}
.badge-open{background:#fee2e2;color:#991b1b;border:1px solid #fecaca}
.badge-inprogress{background:#fef9c3;color:#854d0e;border:1px solid #fde68a}
.badge-resolved{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}
</style>
@endpush

@section('content')
<section class="container mx-auto px-4 lg:px-8 py-6">
  <div class="mb-5 flex items-center justify-between">
    <h1 class="text-2xl font-bold">Detail Laporan #{{ $report->id }}</h1>
    <a href="{{ route('admin.reports.index') }}" class="text-sm text-blue-600 hover:underline">← Kembali</a>
  </div>

  @if(session('ok'))
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-700 px-4 py-3">
      {{ session('ok') }}
    </div>
  @endif

  <div class="grid md:grid-cols-3 gap-6">
    {{-- Kolom kiri: informasi utama --}}
    <div class="md:col-span-2 space-y-4">
      <div class="bg-white border rounded-xl p-4">
        <h2 class="font-semibold mb-2">Informasi Laporan</h2>
        <dl class="text-sm text-gray-700 grid grid-cols-3 gap-2">
          <dt class="text-gray-500">Dibuat</dt>
          <dd class="col-span-2">{{ $report->created_at->format('Y-m-d H:i:s') }}</dd>

          <dt class="text-gray-500">Kategori</dt>
          <dd class="col-span-2">{{ $report->category }}</dd>

          <dt class="text-gray-500">Status</dt>
          <dd class="col-span-2">
            <form action="{{ route('admin.reports.update', $report) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-3 items-center">
              @csrf @method('PATCH')

              {{-- Hidden native select untuk submit nilai --}}
              <select id="sel-status" name="status" class="hidden">
                <option value="open"        {{ $report->status==='open'?'selected':'' }}>open</option>
                <option value="in_progress" {{ $report->status==='in_progress'?'selected':'' }}>in_progress</option>
                <option value="resolved"    {{ $report->status==='resolved'?'selected':'' }}>resolved</option>
              </select>

              {{-- Fancy dropdown --}}
              <div class="dd" data-dd="sel-status">
                <button type="button" class="dd-trigger" aria-haspopup="listbox" aria-expanded="false">
                  <span class="dd-label">Status</span>
                  <span class="dd-caret">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                  </span>
                </button>
                <ul class="dd-menu" role="listbox" tabindex="-1"></ul>
              </div>

              <button class="justify-self-start sm:justify-self-auto inline-flex items-center px-4 py-2 rounded-full bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                Simpan
              </button>
            </form>
          </dd>

          <dt class="text-gray-500">URL Halaman</dt>
          <dd class="col-span-2">
            @if($report->page_url)
              <a href="{{ $report->page_url }}" class="text-blue-600 hover:underline break-all" target="_blank" rel="noopener">
                {{ $report->page_url }}
              </a>
            @else
              <span class="text-gray-400">-</span>
            @endif
          </dd>
        </dl>
      </div>

      <div class="bg-white border rounded-xl p-4">
        <h2 class="font-semibold mb-2">Deskripsi</h2>
        <p class="text-gray-800 whitespace-pre-line">{{ $report->description }}</p>
      </div>
    </div>

    {{-- Kolom kanan: metadata --}}
    <div class="space-y-4">
      <div class="bg-white border rounded-xl p-4">
        <h2 class="font-semibold mb-2">Konteks</h2>
        <dl class="text-sm text-gray-700">
          <dt class="text-gray-500">Story ID</dt>
          <dd class="mb-2">{{ $report->story_id ?? '-' }}</dd>
          <dt class="text-gray-500">Chapter ID</dt>
          <dd class="mb-2">{{ $report->chapter_id ?? '-' }}</dd>
          <dt class="text-gray-500">User ID</dt>
          <dd class="mb-2">{{ $report->user_id ?? '-' }}</dd>
          <dt class="text-gray-500">IP Address</dt>
          <dd class="mb-2">{{ $report->ip_address ?? '-' }}</dd>
          <dt class="text-gray-500">User Agent</dt>
          <dd class="mb-2 break-all">{{ $report->user_agent ?? '-' }}</dd>
        </dl>
      </div>

      <div class="bg-white border rounded-xl p-4">
        <h2 class="font-semibold mb-2">Aksi</h2>
        <form action="{{ route('admin.reports.destroy', $report) }}" method="POST"
              onsubmit="return confirm('Hapus laporan ini?');">
          @csrf @method('DELETE')
          <button class="w-full bg-red-50 text-red-700 border border-red-200 rounded-full px-4 py-2 hover:bg-red-100">
            Hapus Laporan
          </button>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Init fancy select untuk status
  document.querySelectorAll('.dd[data-dd]').forEach(initFancySelect);

  function initFancySelect(wrap){
    const selectId = wrap.getAttribute('data-dd');
    const selectEl = document.getElementById(selectId);
    const trigger  = wrap.querySelector('.dd-trigger');
    const labelEl  = wrap.querySelector('.dd-label');
    const menu     = wrap.querySelector('.dd-menu');
    if(!selectEl||!trigger||!labelEl||!menu) return;

    // Render opsi dari <select>
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

    // Label awal sesuai value terpilih
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
