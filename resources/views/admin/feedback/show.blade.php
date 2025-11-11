@extends('layouts.app')
@section('title','Detail Feedback #'.$feedback->id)

@push('styles')
<style>
.dd{position:relative;width:100%}
.dd.is-open .dd-trigger{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.25)}
.dd-trigger{width:100%;display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.625rem .875rem;border:1px solid #d1d5db;border-radius:.75rem;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.03);cursor:pointer}
.dd-label{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.dd-menu{position:absolute;left:0;right:0;top:calc(100% + .25rem);z-index:50;background:#fff;border:1px solid #e5e7eb;border-radius:.75rem;box-shadow:0 12px 28px rgba(0,0,0,.12);padding:.35rem;display:none;max-height:16rem;overflow:auto}
.dd.is-open .dd-menu{display:block}
.dd-item{padding:.55rem .65rem;border-radius:.5rem;cursor:pointer}
.dd-item:hover{background:#f3f4f6}
.dd-item[aria-selected="true"]{background:#eff6ff;color:#1e40af;font-weight:600}
</style>
@endpush

@section('content')
<section class="container mx-auto px-4 lg:px-8 py-6">
  <div class="mb-5 flex items-center justify-between">
    <h1 class="text-2xl font-bold">Detail Feedback #{{ $feedback->id }}</h1>
    <a href="{{ route('admin.feedback.index') }}" class="text-sm text-blue-600 hover:underline">← Kembali</a>
  </div>

  @if(session('ok'))
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-700 px-4 py-3">{{ session('ok') }}</div>
  @endif

  <div class="grid md:grid-cols-3 gap-6">
    <div class="md:col-span-2 space-y-4">
      <div class="bg-white border rounded-xl p-4">
        <h2 class="font-semibold mb-2">Informasi</h2>
        <dl class="text-sm text-gray-700 grid grid-cols-3 gap-2">
          <dt class="text-gray-500">Dibuat</dt><dd class="col-span-2">{{ $feedback->created_at->format('Y-m-d H:i:s') }}</dd>
          <dt class="text-gray-500">Kategori</dt><dd class="col-span-2">{{ $feedback->category }}</dd>
          <dt class="text-gray-500">Pengirim</dt>
          <dd class="col-span-2">{{ $feedback->name ?? 'Guest' }} <span class="text-gray-400">·</span> {{ $feedback->email ?? '—' }}</dd>

          <dt class="text-gray-500">Status</dt>
          <dd class="col-span-2">
            <form action="{{ route('admin.feedback.update', $feedback) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-3 items-center">
              @csrf @method('PATCH')

              <select id="sel-status" name="status" class="hidden">
                @foreach(\App\Models\Feedback::STATUSES as $st)
                  <option value="{{ $st }}" @selected($feedback->status===$st)>{{ $st }}</option>
                @endforeach
              </select>

              <div class="dd" data-dd="sel-status">
                <button type="button" class="dd-trigger"><span class="dd-label">Status</span><i class="fa-solid fa-chevron-down text-xs"></i></button>
                <ul class="dd-menu"></ul>
              </div>

              <button class="px-4 py-2 rounded-full bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">Simpan</button>
            </form>
          </dd>

          <dt class="text-gray-500">URL Halaman</dt>
          <dd class="col-span-2">
            @if($feedback->page_url)
              <a class="text-blue-600 hover:underline break-all" href="{{ $feedback->page_url }}" target="_blank" rel="noopener">
                {{ $feedback->page_url }}
              </a>
            @else
              <span class="text-gray-400">-</span>
            @endif
          </dd>
        </dl>
      </div>

      <div class="bg-white border rounded-xl p-4">
        <h2 class="font-semibold mb-2">Pesan</h2>
        <p class="text-gray-800 whitespace-pre-line">{{ $feedback->message }}</p>
      </div>
    </div>

    <div class="space-y-4">
      <div class="bg-white border rounded-xl p-4">
        <h2 class="font-semibold mb-2">Konteks</h2>
        <dl class="text-sm text-gray-700">
          <dt class="text-gray-500">User ID</dt><dd class="mb-2">{{ $feedback->user_id ?? '-' }}</dd>
          <dt class="text-gray-500">IP Address</dt><dd class="mb-2">{{ $feedback->ip_address ?? '-' }}</dd>
          <dt class="text-gray-500">User Agent</dt><dd class="mb-2 break-all">{{ $feedback->user_agent ?? '-' }}</dd>
        </dl>
      </div>

      <div class="bg-white border rounded-xl p-4">
        <h2 class="font-semibold mb-2">Aksi</h2>
        <form action="{{ route('admin.feedback.destroy', $feedback) }}" method="POST" onsubmit="return confirm('Hapus feedback ini?');">
          @csrf @method('DELETE')
          <button class="w-full bg-red-50 text-red-700 border border-red-200 rounded-full px-4 py-2 hover:bg-red-100">Hapus</button>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.dd[data-dd]').forEach(initFancy);
  function initFancy(wrap){
    const id = wrap.getAttribute('data-dd');
    const sel = document.getElementById(id);
    const trigger = wrap.querySelector('.dd-trigger');
    const label = wrap.querySelector('.dd-label');
    const menu = wrap.querySelector('.dd-menu');
    if(!sel || !trigger || !label || !menu) return;

    menu.innerHTML='';
    Array.from(sel.options).forEach(opt=>{
      const li=document.createElement('li');
      li.className='dd-item';
      li.setAttribute('role','option');
      li.setAttribute('data-value',opt.value);
      li.setAttribute('aria-selected',String(opt.selected));
      li.textContent=opt.textContent;
      li.addEventListener('click',()=>choose(opt.value));
      menu.appendChild(li);
    });

    setLabel(sel.selectedOptions[0]?.textContent || '');
    function setLabel(t){ label.textContent=t; }
    function open(){ wrap.classList.add('is-open'); trigger.setAttribute('aria-expanded','true'); menu.focus(); }
    function close(){ wrap.classList.remove('is-open'); trigger.setAttribute('aria-expanded','false'); }
    function choose(v){
      if(sel.value!==v){ sel.value=v; sel.dispatchEvent(new Event('change',{bubbles:true})); }
      setLabel(sel.selectedOptions[0]?.textContent||''); update(); close();
    }
    function update(){ const v=sel.value; menu.querySelectorAll('.dd-item').forEach(it=>it.setAttribute('aria-selected',String(it.getAttribute('data-value')===v))); }

    trigger.addEventListener('click',e=>{ e.preventDefault(); wrap.classList.contains('is-open')?close():open(); });
    sel.addEventListener('change',update);
    document.addEventListener('click',e=>{ if(!wrap.contains(e.target)) close(); });
    document.addEventListener('keydown',e=>{ if(e.key==='Escape') close(); });
  }
});
</script>
@endpush
