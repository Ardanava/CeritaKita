{{-- resources/views/feedback/create.blade.php --}}
@extends('layouts.app')

@section('title','Saran & Masukan')

@push('styles')
<style>
/* ===== Card & inputs ===== */
.form-card{background:#fff;border:1px solid #e5e7eb;border-radius:1rem;padding:1rem}
.ck-input, .ck-textarea{
  width:100%;border:1px solid #d1d5db;border-radius:.75rem;padding:.625rem .875rem;
  transition:border-color .15s, box-shadow .15s;
}
.ck-input:focus, .ck-textarea:focus{
  outline:none;box-shadow:0 0 0 3px rgba(59,130,246,.25);border-color:#60a5fa
}

/* ===== Fancy Select (single) – sama dengan yang dipakai sebelumnya ===== */
.dd{position:relative;width:100%}
.dd.is-open .dd-trigger{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.25)}
.dd-trigger{
  width:100%;display:flex;align-items:center;justify-content:space-between;gap:.75rem;
  padding:.625rem .875rem;border:1px solid #d1d5db;border-radius:.75rem;background:#fff;
  box-shadow:0 1px 2px rgba(0,0,0,.03);cursor:pointer;transition:border-color .15s,box-shadow .15s
}
.dd-label{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.dd-caret{flex:0 0 auto;display:inline-flex}
.dd-menu{
  position:absolute;left:0;right:0;top:calc(100% + .25rem);z-index:50;background:#fff;
  border:1px solid #e5e7eb;border-radius:.75rem;box-shadow:0 12px 28px rgba(0,0,0,.12);
  padding:.35rem;display:none;max-height:16rem;overflow:auto
}
.dd.is-open .dd-menu{display:block}
.dd-item{
  padding:.55rem .65rem;border-radius:.5rem;cursor:pointer;
  transition:background-color .12s,color .12s
}
.dd-item:hover{background:#f3f4f6}
.dd-item[aria-selected="true"]{background:#eff6ff;color:#1e40af;font-weight:600}
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-10 max-w-3xl">
  <h1 class="text-3xl font-bold mb-6">Saran &amp; Masukan</h1>

  @if(session('ok'))
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-700 px-4 py-3">
      {{ session('ok') }}
    </div>
  @endif

  <div class="form-card">
    <form method="POST" action="{{ route('feedback.store') }}">
      @csrf

      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm text-gray-600">Nama (opsional)</label>
          <input type="text" name="name" class="ck-input mt-1" autocomplete="off">
          @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="text-sm text-gray-600">Email (opsional)</label>
          <input type="email" name="email" class="ck-input mt-1" autocomplete="off">
          @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
      </div>

      {{-- ==== Kategori (Fancy Select) ==== --}}
      <div class="mt-4">
        <label class="block text-sm text-gray-600 mb-1">Kategori</label>

        {{-- select asli disembunyikan untuk submit value --}}
        <select id="fb-category" name="category" class="hidden" required>
          <option value="">Pilih…</option>
          @foreach($categories as $cat)
            <option value="{{ $cat }}" @selected(old('category')===$cat)>{{ $cat }}</option>
          @endforeach
        </select>

        {{-- komponen UI fancy --}}
        <div class="dd mt-1" data-dd="fb-category">
          <button type="button" class="dd-trigger" aria-haspopup="listbox" aria-expanded="false">
            <span class="dd-label">{{ old('category') ?: 'Pilih…' }}</span>
            <span class="dd-caret">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </span>
          </button>
          <ul class="dd-menu" role="listbox" tabindex="-1"></ul>
        </div>

        @error('category')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="mt-4">
        <label class="text-sm text-gray-600">Pesan</label>
        <textarea name="message" rows="6" class="ck-textarea mt-1" required minlength="10" maxlength="3000"
          placeholder="Tulis saran/masukan kamu di sini…">{{ old('message') }}</textarea>
        @error('message')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
      </div>

      <input type="hidden" name="page_url" value="{{ url()->current() }}">
      <input type="text" name="hp_field" class="hidden" tabindex="-1" autocomplete="off">

      <div class="mt-5 flex items-center justify-end gap-3">
        <a href="{{ url()->previous() }}" class="px-4 py-2 rounded-full border">Batal</a>
        <button class="px-5 py-2.5 rounded-full bg-blue-600 text-white font-semibold hover:bg-blue-700">
          Kirim
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Inisialisasi Fancy Select untuk elemen dengan atribut data-dd
  document.querySelectorAll('.dd[data-dd]').forEach(initFancySelect);

  function initFancySelect(wrap){
    const selectId = wrap.getAttribute('data-dd');
    const selectEl = document.getElementById(selectId);
    const trigger  = wrap.querySelector('.dd-trigger');
    const labelEl  = wrap.querySelector('.dd-label');
    const menu     = wrap.querySelector('.dd-menu');
    if(!selectEl || !trigger || !labelEl || !menu) return;

    // Build menu dari <option>
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

    // Label awal
    setLabel(selectEl.selectedOptions[0]?.textContent || 'Pilih…');

    // Helpers
    function open(){ wrap.classList.add('is-open'); trigger.setAttribute('aria-expanded','true'); menu.focus(); }
    function close(){ wrap.classList.remove('is-open'); trigger.setAttribute('aria-expanded','false'); }
    function setLabel(text){ labelEl.textContent = text; }
    function updateSelected(){
      const v = selectEl.value;
      menu.querySelectorAll('.dd-item')
        .forEach(it => it.setAttribute('aria-selected', String(it.getAttribute('data-value')===v)));
    }
    function choose(val){
      if (selectEl.value !== val){
        selectEl.value = val;
        selectEl.dispatchEvent(new Event('change', { bubbles:true }));
      }
      setLabel(selectEl.selectedOptions[0]?.textContent || 'Pilih…');
      updateSelected();
      close();
    }

    // Events
    trigger.addEventListener('click', e => {
      e.preventDefault();
      wrap.classList.contains('is-open') ? close() : open();
    });
    selectEl.addEventListener('change', updateSelected);
    document.addEventListener('click', e => { if(!wrap.contains(e.target)) close(); });
    document.addEventListener('keydown', e => { if(e.key==='Escape') close(); });
  }
});
</script>
@endpush
