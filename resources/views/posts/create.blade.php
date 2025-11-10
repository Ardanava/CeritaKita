@extends('layouts.app')

@section('title', 'Tulis Postingan Baru - CeritaKita')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.snow.css" rel="stylesheet">
<style>
  /* === START: Quill base === */
  .ql-container{font-family:'Lora',serif;min-height:350px}
  .ql-toolbar{border-top-left-radius:.5rem;border-top-right-radius:.5rem}
  .ql-container.ql-snow{border-bottom-left-radius:.5rem;border-bottom-right-radius:.5rem}
  #editor .ql-container{min-height:360px;position:relative;z-index:1}
  #editor .ql-editor{min-height:360px;padding:12px 16px;cursor:text}
  /* === END: Quill base === */

  /* === START: Pills & chip === */
  .pill-base{display:inline-flex;align-items:center;gap:.5rem;padding:.25rem .75rem;border:1px solid #d1d5db;border-radius:9999px;cursor:pointer;user-select:none;transition:all .15s ease}
  .pill-inactive{background:#fff;color:#6b7280;opacity:.7}
  .pill-active{opacity:1;box-shadow:0 0 0 2px #e5e7eb}
  .pill-ann{background:#eef2ff;border-color:#6366f1;color:#3730a3}
  .pill-upd{background:#ecfdf5;border-color:#10b981;color:#065f46}
  .pill-mnt{background:#fffbeb;border-color:#f59e0b;color:#92400e}
  .pill-inf{background:#eff6ff;border-color:#3b82f6;color:#1e40af}
  .chip{display:inline-flex;align-items:center;gap:.5rem;padding:.25rem .75rem;border:1px solid;border-radius:9999px;font-weight:600;font-size:.875rem}
  .chip-ann{background:#eef2ff;color:#3730a3;border-color:#6366f1}
  .chip-upd{background:#ecfdf5;color:#065f46;border-color:#10b981}
  .chip-mnt{background:#fffbeb;color:#92400e;border-color:#f59e0b}
  .chip-inf{background:#eff6ff;color:#1e40af;border-color:#3b82f6}
  /* === END: Pills & chip === */

  /* === START: Inputs & nice select === */
  .nice-input,.nice-textarea{width:100%;padding:.75rem;border:1px solid #d1d5db;border-radius:.75rem;transition:border-color .15s,box-shadow .15s}
  .nice-input:focus,.nice-textarea:focus{outline:0;border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.25)}
  .nice-select{width:100%;appearance:none;-webkit-appearance:none;-moz-appearance:none;padding:.5rem .75rem;border:1px solid #d1d5db;border-radius:.5rem;background:#fff;line-height:1.5;transition:border-color .15s,box-shadow .15s;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='gray'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");background-repeat:no-repeat;background-size:1rem;background-position:right .6rem center;padding-right:2rem}
  .nice-select:focus{outline:0;border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.25)}
  .ns-select{position:relative}
  .ns-trigger{width:100%;display:flex;align-items:center;justify-content:space-between;padding:.625rem .875rem;border:1px solid #d1d5db;border-radius:.75rem;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.03);transition:border-color .15s,box-shadow .15s;cursor:pointer}
  .ns-trigger:hover{border-color:#9ca3af}
  .ns-open .ns-trigger{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.25)}
  .ns-trigger-label{display:inline-flex;align-items:center;gap:.5rem}
  .ns-menu{position:absolute;left:0;right:0;top:calc(100% + .25rem);z-index:50;background:#fff;border:1px solid #e5e7eb;border-radius:.75rem;box-shadow:0 12px 28px rgba(0,0,0,.12);padding:.35rem;display:none;max-height:14rem;overflow:auto}
  .ns-open .ns-menu{display:block}
  .ns-item{display:flex;align-items:center;gap:.6rem;padding:.55rem .65rem;border-radius:.5rem;cursor:pointer;transition:background-color .12s,color .12s}
  .ns-item:hover{background:#f3f4f6}
  .ns-item[aria-selected="true"]{background:#eff6ff;color:#1e40af;font-weight:600}
  /* === END: Inputs & nice select === */
</style>
@endpush

@section('content')
{{-- ===== START: Main ===== --}}
<main class="container mx-auto px-4 lg:px-8 py-12">
  <div class="max-w-3xl mx-auto bg-white p-6 sm:p-8 rounded-xl shadow-lg border border-gray-200">

    {{-- ---- START: Header ---- --}}
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Buat Postingan / Pengumuman</h1>
      <div id="typeChip" class="chip chip-inf">
        <i class="fa-solid fa-circle-info"></i><span>Info</span>
      </div>
    </div>
    <p class="text-gray-600 mb-8 text-sm">Tulis pengumuman, pembaruan, atau info untuk tampil di Papan Info.</p>
    {{-- ---- END: Header ---- --}}

    {{-- ---- START: Errors ---- --}}
    @if ($errors->any())
      <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
        <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
        <ul class="mt-2 list-disc list-inside text-sm">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif
    {{-- ---- END: Errors ---- --}}

    {{-- ---- START: Form ---- --}}
    <form action="{{ route('posts.store') }}" method="POST" id="post-form">
      @csrf

      <div class="space-y-8">
        {{-- ~~ START: Judul ~~ --}}
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Postingan <span class="text-red-500">*</span></label>
          <input type="text" name="title" class="nice-input" placeholder="Contoh: Pembaruan Situs v1.3" required value="{{ old('title') }}">
        </div>
        {{-- ~~ END: Judul ~~ --}}

        {{-- ~~ START: Jenis Post (pills + radio) ~~ --}}
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Post <span class="text-red-500">*</span></label>

          <div id="typeGroup" class="flex flex-wrap items-center gap-2">
            {{-- radios --}}
            <input class="sr-only" type="radio" name="type" id="type-ann" value="announcement" @checked(old('type','info')==='announcement')>
            <input class="sr-only" type="radio" name="type" id="type-upd" value="update"        @checked(old('type','info')==='update')>
            <input class="sr-only" type="radio" name="type" id="type-mnt" value="maintenance"   @checked(old('type','info')==='maintenance')>
            <input class="sr-only" type="radio" name="type" id="type-inf" value="info"          @checked(old('type','info')==='info')>

            {{-- pills --}}
            <label for="type-ann" data-type="announcement" id="pill-ann" class="pill-base pill-inactive"><i class="fa-solid fa-bullhorn"></i> Pengumuman</label>
            <label for="type-upd" data-type="update" id="pill-upd" class="pill-base pill-inactive"><i class="fa-solid fa-arrows-rotate"></i> Pembaruan</label>
            <label for="type-mnt" data-type="maintenance" id="pill-mnt" class="pill-base pill-inactive"><i class="fa-solid fa-screwdriver-wrench"></i> Perawatan</label>
            <label for="type-inf" data-type="info" id="pill-inf" class="pill-base pill-inactive"><i class="fa-solid fa-circle-info"></i> Info</label>
          </div>
        </div>
        {{-- ~~ END: Jenis Post (pills + radio) ~~ --}}

        {{-- ~~ START: Visibility / Pin / Priority ~~ --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

          {{-- visibility --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Visibilitas</label>

            {{-- native select (hidden) --}}
            <select name="visibility" id="visibility-select" class="nice-input" style="display:none">
              <option value="public"  {{ old('visibility','public')==='public'  ? 'selected' : '' }}>Publik</option>
              <option value="private" {{ old('visibility')==='private' ? 'selected' : '' }}>Privat (hanya admin)</option>
            </select>

            {{-- pretty dropdown --}}
            <div id="visibility-nice" class="ns-select" data-target="visibility-select" style="width:100%">
              <button type="button" class="ns-trigger" aria-haspopup="listbox" aria-expanded="false">
                <span class="ns-trigger-label"><i class="fa-solid fa-earth-americas"></i> Publik</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
              <ul class="ns-menu" role="listbox" tabindex="-1">
                <li class="ns-item" role="option" data-value="public" aria-selected="true"><i class="fa-solid fa-earth-americas"></i> Publik</li>
                <li class="ns-item" role="option" data-value="private" aria-selected="false"><i class="fa-solid fa-lock"></i> Privat (hanya admin)</li>
              </ul>
            </div>
          </div>

          {{-- pin --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Sematkan?</label>
            <input type="hidden" name="is_pinned" value="0">
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned') ? 'checked' : '' }} style="width:20px;height:20px;border:1px solid #d1d5db;border-radius:.25rem">
              <span class="text-sm text-gray-600">Ya</span>
            </label>
          </div>

          {{-- priority --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Prioritas</label>
            <input type="number" name="priority" class="nice-input" value="{{ old('priority',0) }}" min="0" max="10" step="1">
            <p class="text-xs text-gray-500 mt-1">Semakin tinggi, semakin di atas.</p>
          </div>
        </div>
        {{-- ~~ END: Visibility / Pin / Priority ~~ --}}

        {{-- ~~ START: Summary ~~ --}}
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Ringkasan (otomatis, bisa diedit)</label>
          <textarea name="summary" id="summary-input" rows="3" class="nice-textarea" placeholder="Ringkasan singkat akan terisi otomatis...">{{ old('summary') }}</textarea>
          <p class="text-xs text-gray-500 mt-1">Tip: Biarkan kosong bila ingin diisi otomatis dari konten (≈ 160–200 karakter).</p>
        </div>
        {{-- ~~ END: Summary ~~ --}}

        {{-- ~~ START: Content (Quill) ~~ --}}
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Isi Konten <span class="text-red-500">*</span></label>
          <div id="editor"></div>
          <input type="hidden" name="content" id="content-input">
        </div>
        {{-- ~~ END: Content (Quill) ~~ --}}
      </div>

      {{-- ~~ START: Actions ~~ --}}
      <div class="flex justify-end items-center gap-4 mt-8 pt-6 border-t">
        <a href="{{ route('workdesk') }}" class="text-gray-600 font-medium py-2 px-5 rounded-lg hover:bg-gray-100">Batal</a>
        <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-5 rounded-lg hover:bg-blue-700">Terbitkan Postingan</button>
      </div>
      {{-- ~~ END: Actions ~~ --}}
    </form>
    {{-- ---- END: Form ---- --}}

  </div>
</main>
{{-- ===== END: Main ===== --}}
@endsection

@push('scripts')
<!-- Quill v2 -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.js"></script>

<!-- Seed dari server -->
<script>
  window.oldContent = @json(old('content'));
  window.oldSummary = @json(old('summary'));
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  /* === START: Fallback textarea === */
  function useTextareaFallback(){
    const editorEl = document.getElementById('editor'); if(!editorEl) return;
    const ta = document.createElement('textarea'); ta.name='content'; ta.rows=12; ta.className='nice-textarea'; ta.placeholder='Tulis konten di sini...';
    if(window.oldContent) ta.value = window.oldContent;
    editorEl.replaceWith(ta);
    document.getElementById('content-input')?.remove();
  }
  /* === END: Fallback textarea === */

  /* === START: Type pills === */
  const radios = {
    announcement: document.getElementById('type-ann'),
    update:       document.getElementById('type-upd'),
    maintenance:  document.getElementById('type-mnt'),
    info:         document.getElementById('type-inf'),
  };
  const pills = {
    announcement: document.getElementById('pill-ann'),
    update:       document.getElementById('pill-upd'),
    maintenance:  document.getElementById('pill-mnt'),
    info:         document.getElementById('pill-inf'),
  };
  const badgeEl = document.getElementById('typeChip');
  const TYPE_CLS = {
    announcement:{pill:'pill-ann',chip:'chip-ann',label:'Pengumuman', icon:'fa-bullhorn'},
    update:{pill:'pill-upd',chip:'chip-upd',label:'Pembaruan', icon:'fa-arrows-rotate'},
    maintenance:{pill:'pill-mnt',chip:'chip-mnt',label:'Perawatan', icon:'fa-screwdriver-wrench'},
    info:{pill:'pill-inf',chip:'chip-inf',label:'Info', icon:'fa-circle-info'},
  };
  function setActiveType(type){
    Object.keys(pills).forEach(t=>{
      const el=pills[t]; if(!el) return;
      el.classList.remove('pill-active', TYPE_CLS[t].pill);
      el.classList.add('pill-inactive');
      if(radios[t]) radios[t].checked=false;
    });
    const act=pills[type]; if(act){ act.classList.remove('pill-inactive'); act.classList.add('pill-active', TYPE_CLS[type].pill); }
    if(radios[type]) radios[type].checked=true;
    if(badgeEl){
      badgeEl.className='chip '+TYPE_CLS[type].chip;
      const icon=badgeEl.querySelector('i'); if(icon) icon.className='fa-solid '+TYPE_CLS[type].icon;
      const txt=badgeEl.querySelector('span'); if(txt) txt.textContent=TYPE_CLS[type].label;
    }
  }
  Object.keys(pills).forEach(t=>pills[t]?.addEventListener('click',()=>setActiveType(t)));
  Object.keys(radios).forEach(t=>radios[t]?.addEventListener('change',()=>setActiveType(t)));
  const initialType =
    (radios.announcement?.checked && 'announcement') ||
    (radios.update?.checked && 'update') ||
    (radios.maintenance?.checked && 'maintenance') ||
    (radios.info?.checked && 'info') || 'info';
  setActiveType(initialType);
  /* === END: Type pills === */

  /* === START: Pretty visibility select === */
  (function initNiceSelect(){
    const wrap=document.getElementById('visibility-nice'); if(!wrap) return;
    const selectId=wrap.getAttribute('data-target');
    const selectEl=document.getElementById(selectId);
    const trigger=wrap.querySelector('.ns-trigger');
    const labelEl=wrap.querySelector('.ns-trigger-label');
    const items=[...wrap.querySelectorAll('.ns-item')];

    function setLabel(val){
      labelEl.innerHTML = (val==='private')
        ? '<i class="fa-solid fa-lock"></i> Privat (hanya admin)'
        : '<i class="fa-solid fa-earth-americas"></i> Publik';
    }
    function close(){ wrap.classList.remove('ns-open'); trigger?.setAttribute('aria-expanded','false'); }
    function open(){  wrap.classList.add('ns-open');    trigger?.setAttribute('aria-expanded','true'); }

    const initVal = selectEl?.value || 'public';
    setLabel(initVal);
    items.forEach(li=>li.setAttribute('aria-selected', String(li.getAttribute('data-value')===initVal)));

    trigger?.addEventListener('click',e=>{e.preventDefault(); wrap.classList.contains('ns-open')?close():open();});
    items.forEach(li=>li.addEventListener('click',()=>{
      const val=li.getAttribute('data-value');
      if(selectEl){ selectEl.value=val; selectEl.dispatchEvent(new Event('change',{bubbles:true})); }
      setLabel(val);
      items.forEach(x=>x.setAttribute('aria-selected', String(x===li)));
      close();
    }));
    document.addEventListener('click',e=>{ if(!wrap.contains(e.target)) close(); });
    document.addEventListener('keydown',e=>{ if(e.key==='Escape') close(); });
  })();
  /* === END: Pretty visibility select === */

  /* === START: Quill + upload + auto summary === */
  function initQuill(){
    const editorEl=document.getElementById('editor');
    const hiddenContent=document.getElementById('content-input');
    const summaryInput=document.getElementById('summary-input');
    const csrf=document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')||'';
    if(!editorEl) return;

    if(!window.Quill){
      useTextareaFallback();
      if(summaryInput && window.oldSummary && !summaryInput.value.trim()) summaryInput.value = window.oldSummary;
      return;
    }

    const quill=new Quill('#editor',{
      theme:'snow',
      modules:{
        toolbar:[
          [{header:[1,2,3,false]}],
          ['bold','italic','underline','strike'],
          [{list:'ordered'},{list:'bullet'}],
          [{align:[]}],
          ['link','blockquote','code-block','image'],
          [{color:[]},{background:[]}],
          ['clean']
        ]
      },
      placeholder:'Tulis konten postingan di sini...'
    });

    // seed
    if(window.oldContent){
      try{const maybe=JSON.parse(window.oldContent); maybe?.ops?quill.setContents(maybe):quill.root.innerHTML=window.oldContent;}
      catch{quill.root.innerHTML=window.oldContent;}
    }

    // image upload (toolbar/paste/drop)
    quill.getModule('toolbar')?.addHandler('image',()=>{
      const i=document.createElement('input'); i.type='file'; i.accept='image/*';
      i.onchange=async()=>{ if(i.files?.[0]) await insertImage(i.files[0]); }; i.click();
    });
    quill.root.addEventListener('paste',async e=>{
      const it=[...(e.clipboardData?.items||[])].find(x=>x.type.startsWith('image/')); if(!it) return;
      e.preventDefault(); const f=it.getAsFile(); if(f) await insertImage(f);
    });
    quill.root.addEventListener('drop',async e=>{
      const f=e.dataTransfer?.files?.[0]; if(!f?.type?.startsWith('image/')) return;
      e.preventDefault(); e.stopPropagation(); await insertImage(f);
    });
    async function insertImage(file){
      try{
        if(file.size>12*1024*1024){ alert('Ukuran gambar melebihi 12MB.'); return; }
        const fd=new FormData(); fd.append('image',file,file.name);
        const r=await fetch(@json(route('editor.image.upload')),{method:'POST',headers:{'X-CSRF-TOKEN':csrf},body:fd});
        if(!r.ok) throw new Error('Upload gagal');
        const data=await r.json(); if(!data?.url) throw new Error('Respon tidak valid');
        const range=quill.getSelection(true)||{index:quill.getLength(),length:0};
        quill.insertEmbed(range.index,'image',data.url,Quill.sources.USER);
        quill.setSelection(range.index+1,0,Quill.sources.SILENT);
        requestAnimationFrame(()=>{
          const imgs=document.querySelectorAll('.ql-editor img'); const img=imgs[imgs.length-1];
          if(img){ img.loading='lazy'; img.style.maxWidth='100%'; img.style.height='auto'; img.style.display='block'; img.style.margin='.5rem auto'; img.style.borderRadius='.5rem'; }
        });
      }catch(e){ console.error(e); alert('Gagal mengunggah gambar.'); }
    }

    // auto-summary
    let dirty=false;
    if(summaryInput){ if(window.oldSummary) summaryInput.value=window.oldSummary; summaryInput.addEventListener('input',()=>dirty=true); }
    const makeSummary=(t,max=190)=>{ if(!t) return ''; let s=t.replace(/\s+/g,' ').trim(); if(s.length<=max) return s; let cut=s.slice(0,max); const last=cut.lastIndexOf(' '); if(last>60) cut=cut.slice(0,last); return cut+'…'; };
    const sync=()=>{ if(hiddenContent) hiddenContent.value=quill.root.innerHTML; if(!dirty&&summaryInput) summaryInput.value=makeSummary(quill.getText(),190); };
    quill.on('text-change',sync); sync();

    // submit
    document.getElementById('post-form')?.addEventListener('submit',()=>{
      if(hiddenContent) hiddenContent.value=quill.root.innerHTML.trim();
      if(summaryInput && !summaryInput.value.trim()) summaryInput.value=makeSummary(quill.getText(),190);
    });
  }
  initQuill();
  /* === END: Quill + upload + auto summary === */
});
</script>
@endpush
