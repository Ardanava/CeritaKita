@extends('layouts.app')

@section('title','Saran & Masukan')

@push('styles')
<style>
/* Fancy select (same as your projects/reports) */
.dd{position:relative;width:100%}
.dd.is-open .dd-trigger{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.25)}
.dd-trigger{width:100%;display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.625rem .875rem;border:1px solid #d1d5db;border-radius:.75rem;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.03);cursor:pointer}
.dd-label{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.dd-menu{position:absolute;left:0;right:0;top:calc(100% + .25rem);z-index:50;background:#fff;border:1px solid #e5e7eb;border-radius:.75rem;box-shadow:0 12px 28px rgba(0,0,0,.12);padding:.35rem;display:none;max-height:16rem;overflow:auto}
.dd.is-open .dd-menu{display:block}
.dd-item{padding:.55rem .65rem;border-radius:.5rem;cursor:pointer}
.dd-item:hover{background:#f3f4f6}
.dd-item[aria-selected="true"]{background:#eff6ff;color:#1e40af;font-weight:600}
.badge{display:inline-flex;align-items:center;padding:.2rem .55rem;border-radius:9999px;font-size:.75rem;font-weight:700}
.badge-open{background:#fee2e2;color:#991b1b;border:1px solid #fecaca}
.badge-inprogress{background:#fef9c3;color:#854d0e;border:1px solid #fde68a}
.badge-resolved{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}
</style>
@endpush

@section('content')
<section class="container mx-auto px-4 lg:px-8 py-6">
  <div class="flex items-center justify-between mb-5">
    <h1 class="text-2xl font-bold">Saran &amp; Masukan</h1>
    <a href="{{ route('admin.feedback.index') }}" class="text-sm text-blue-600 hover:underline">Refresh</a>
  </div>

  {{-- Stats --}}
  <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded-xl border p-4"><p class="text-sm text-gray-500">Open</p><p class="text-2xl font-bold">{{ $stats['open'] }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-sm text-gray-500">In Progress</p><p class="text-2xl font-bold">{{ $stats['in_progress'] }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-sm text-gray-500">Resolved</p><p class="text-2xl font-bold">{{ $stats['resolved'] }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-sm text-gray-500">Total</p><p class="text-2xl font-bold">{{ $stats['total'] }}</p></div>
  </div>

  {{-- Filter --}}
  <form method="GET" class="bg-white border rounded-xl p-4 mb-6">
    <div class="grid md:grid-cols-4 gap-4">
      <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Cari</label>
        <input type="text" name="q" value="{{ $q }}" placeholder="Pesan, email, IP, User-Agent"
               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" autocomplete="off">
      </div>

      {{-- Status fancy --}}
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
        <select id="sel-status" name="status" class="hidden">
          <option value="" @selected($status==='')>Semua</option>
          <option value="open" @selected($status==='open')>Open</option>
          <option value="in_progress" @selected($status==='in_progress')>In Progress</option>
          <option value="resolved" @selected($status==='resolved')>Resolved</option>
        </select>
        <div class="dd mt-1" data-dd="sel-status">
          <button type="button" class="dd-trigger"><span class="dd-label">Semua</span><i class="fa-solid fa-chevron-down text-xs"></i></button>
          <ul class="dd-menu"></ul>
        </div>
      </div>

      {{-- Kategori fancy --}}
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori</label>
        <select id="sel-category" name="category" class="hidden">
          <option value="" @selected($category==='')>Semua</option>
          @foreach($categories as $cat)
            <option value="{{ $cat }}" @selected($category===$cat)>{{ $cat }}</option>
          @endforeach
        </select>
        <div class="dd mt-1" data-dd="sel-category">
          <button type="button" class="dd-trigger"><span class="dd-label">Semua</span><i class="fa-solid fa-chevron-down text-xs"></i></button>
          <ul class="dd-menu"></ul>
        </div>
      </div>
    </div>

    <div class="mt-4 flex items-center justify-between gap-3 flex-wrap">
      {{-- Urutkan fancy --}}
      <div class="w-full md:w-auto">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Urutkan</label>
        <select id="sel-sort" name="sort" class="hidden">
          <option value="-created" @selected($sort==='-created')>Terbaru</option>
          <option value="+created" @selected($sort==='+created')>Terlama</option>
        </select>
        <div class="dd mt-1 md:w-56" data-dd="sel-sort">
          <button type="button" class="dd-trigger"><span class="dd-label">Terbaru</span><i class="fa-solid fa-chevron-down text-xs"></i></button>
          <ul class="dd-menu"></ul>
        </div>
      </div>

      <div class="ml-auto flex items-center gap-3">
        <a href="{{ route('admin.feedback.index') }}" class="px-5 py-2.5 rounded-xl bg-gray-200 text-gray-800 font-semibold hover:bg-gray-300">Reset</a>
        <button class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">Terapkan</button>
      </div>
    </div>
  </form>

  {{-- Tabel --}}
  <div class="overflow-hidden bg-white border rounded-xl">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-700">
          <tr>
            <th class="px-4 py-3 text-left font-semibold">Waktu</th>
            <th class="px-4 py-3 text-left font-semibold">Kategori</th>
            <th class="px-4 py-3 text-left font-semibold">Ringkas Pesan</th>
            <th class="px-4 py-3 text-left font-semibold">Pengirim</th>
            <th class="px-4 py-3 text-left font-semibold">Status</th>
            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($feedback as $f)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ $f->created_at->format('Y-m-d H:i') }}</td>
              <td class="px-4 py-3"><span class="badge" style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe">{{ $f->category }}</span></td>
              <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($f->message, 80) }}</td>
              <td class="px-4 py-3">
                @if($f->name || $f->email) {{ $f->name ?? '—' }} <span class="text-gray-400">·</span> {{ $f->email ?? '—' }}
                @else <span class="text-gray-400">Guest</span> @endif
              </td>
              <td class="px-4 py-3">
                @php $cls = $f->status==='open' ? 'badge-open' : ($f->status==='in_progress'?'badge-inprogress':'badge-resolved'); @endphp
                <span class="badge {{ $cls }}">{{ $f->status }}</span>
              </td>
              <td class="px-4 py-3 text-right">
                <a href="{{ route('admin.feedback.show', $f) }}" class="inline-flex items-center px-3 py-1.5 rounded-full border text-gray-700 hover:bg-gray-50">Detail</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">Belum ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-4 border-t">
      {{ $feedback->links() }}
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.dd[data-dd], .dd').forEach(initFancy);
  function initFancy(wrap){
    const selectId = wrap.getAttribute('data-dd');
    const selectEl = selectId ? document.getElementById(selectId) : null;
    const trigger  = wrap.querySelector('.dd-trigger');
    const labelEl  = wrap.querySelector('.dd-label');
    const menu     = wrap.querySelector('.dd-menu');
    if(!trigger || !menu) return;

    if (selectEl) {
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
      selectEl.addEventListener('change', updateMenuSelected);
    }

    function setLabel(t){ if(labelEl) labelEl.textContent = t; }
    function updateMenuSelected(){ const v = selectEl.value; menu.querySelectorAll('.dd-item').forEach(it => it.setAttribute('aria-selected', String(it.getAttribute('data-value')===v))); }
    function choose(val){
      if (selectEl && selectEl.value !== val){ selectEl.value = val; selectEl.dispatchEvent(new Event('change', { bubbles:true })); }
      setLabel(selectEl?.selectedOptions[0]?.textContent || '');
      updateMenuSelected(); close();
    }
    function open(){ wrap.classList.add('is-open'); trigger.setAttribute('aria-expanded','true'); menu.focus(); }
    function close(){ wrap.classList.remove('is-open'); trigger.setAttribute('aria-expanded','false'); }
    trigger.addEventListener('click', e => { e.preventDefault(); wrap.classList.contains('is-open') ? close() : open(); });
    document.addEventListener('click', e => { if(!wrap.contains(e.target)) close(); });
    document.addEventListener('keydown', e => { if(e.key==='Escape') close(); });
  }
});
</script>
@endpush
