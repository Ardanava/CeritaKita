@extends('layouts.app')

@section('title', 'Edit Postingan - CeritaKita')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.snow.css" rel="stylesheet">
<style>
  /* === START: Quill === */
  #editor-wrap{border:1px solid #d1d5db;border-radius:.75rem;overflow:hidden;background:#fff}
  .ql-toolbar{border:0;border-bottom:1px solid #e5e7eb}
  .ql-container{border:0;min-height:350px}
  .ql-editor{font-family:'Lora',serif;min-height:350px;padding:16px}
  .ql-editor:empty::before{content:attr(data-placeholder);color:#9ca3af}
  .ql-editor img{max-width:100%;height:auto;display:block;margin:.5rem auto;border-radius:.5rem}
  .ql-align-center img{margin-left:auto;margin-right:auto}
  .ql-align-right img{margin-left:auto;margin-right:0}
  /* === END: Quill === */

  /* === START: Pills & Chip === */
  .pill-base{display:inline-flex;align-items:center;gap:.5rem;padding:.35rem .8rem;border:1px solid #d1d5db;border-radius:9999px;cursor:pointer;user-select:none;transition:all .15s ease}
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
  /* === END: Pills & Chip === */

  /* === START: Inputs & Nice Select === */
  .nice-input,.nice-textarea{width:100%;padding:.75rem;border:1px solid #d1d5db;border-radius:.75rem;transition:border-color .15s,box-shadow .15s}
  .nice-input:focus,.nice-textarea:focus{outline:0;border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.25)}
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
  /* === END: Inputs & Nice Select === */
</style>
@endpush

@section('content')
{{-- ===== START: Main ===== --}}
<main class="container mx-auto px-4 lg:px-8 py-12">
  <div class="max-w-3xl mx-auto bg-white p-6 sm:p-8 rounded-xl shadow-lg border border-gray-200">

    {{-- ---- START: Header ---- --}}
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Edit Postingan</h1>
      <div id="typeChip" class="chip chip-inf">
        <i class="fa-solid fa-circle-info"></i><span>Info</span>
      </div>
    </div>
    {{-- ---- END: Header ---- --}}

    {{-- ---- START: Error Alert ---- --}}
    @if ($errors->any())
      <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
        <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
        <ul class="mt-2 list-disc list-inside text-sm">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif
    {{-- ---- END: Error Alert ---- --}}

    {{-- ---- START: Form ---- --}}
    <form action="{{ route('posts.update', $post) }}" method="POST" id="post-form">
      @csrf
      @method('PUT')

      <div class="space-y-8">
        {{-- ~~ START: Judul ~~ --}}
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Postingan <span class="text-red-500">*</span></label>
          <input type="text" name="title" class="nice-input" placeholder="Judul…" required value="{{ old('title', $post->title) }}">
        </div>
        {{-- ~~ END: Judul ~~ --}}

        {{-- ~~ START: Jenis Post (pills + radio) ~~ --}}
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Post <span class="text-red-500">*</span></label>

          <div id="typeGroup" class="flex flex-wrap items-center gap-2">
            <input class="sr-only" type="radio" name="type" id="type-ann" value="announcement" @checked(old('type', $post->type ?? 'info')==='announcement')>
            <input class="sr-only" type="radio" name="type" id="type-upd" value="update" @checked(old('type', $post->type ?? 'info')==='update')>
            <input class="sr-only" type="radio" name="type" id="type-mnt" value="maintenance" @checked(old('type', $post->type ?? 'info')==='maintenance')>
            <input class="sr-only" type="radio" name="type" id="type-inf" value="info" @checked(old('type', $post->type ?? 'info')==='info')}>

            <label for="type-ann" data-type="announcement" id="pill-ann" class="pill-base pill-inactive"><i class="fa-solid fa-bullhorn"></i> Pengumuman</label>
            <label for="type-upd" data-type="update" id="pill-upd" class="pill-base pill-inactive"><i class="fa-solid fa-arrows-rotate"></i> Pembaruan</label>
            <label for="type-mnt" data-type="maintenance" id="pill-mnt" class="pill-base pill-inactive"><i class="fa-solid fa-screwdriver-wrench"></i> Perawatan</label>
            <label for="type-inf" data-type="info" id="pill-inf" class="pill-base pill-inactive"><i class="fa-solid fa-circle-info"></i> Info</label>
          </div>
        </div>
        {{-- ~~ END: Jenis Post (pills + radio) ~~ --}}

        {{-- ~~ START: Visibility / Pin / Priority ~~ --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          {{-- === Visibility (nice select) === --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Visibilitas</label>

            <select name="visibility" id="visibility-select" class="nice-input" style="display:none">
              <option value="public"  {{ old('visibility', $post->visibility ?? 'public')==='public' ? 'selected' : '' }}>Publik</option>
              <option value="private" {{ old('visibility', $post->visibility ?? 'public')==='private' ? 'selected' : '' }}>Privat (hanya admin)</option>
            </select>

            <div id="visibility-nice" class="ns-select" data-target="visibility-select">
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

          {{-- === Pin === --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Sematkan?</label>
            <input type="hidden" name="is_pinned" value="0">
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" name="is_pinned" value="1" style="width:20px;height:20px;border:1px solid #d1d5db;border-radius:.25rem" @checked(old('is_pinned', (int) ($post->is_pinned ?? 0)))>
              <span class="text-sm text-gray-600">Ya</span>
            </label>
          </div>

          {{-- === Priority === --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Prioritas</label>
            <input type="number" name="priority" class="nice-input" value="{{ old('priority', $post->priority ?? 0) }}" min="0" max="10" step="1">
            <p class="text-xs text-gray-500 mt-1">Semakin tinggi, semakin di atas.</p>
          </div>
        </div>
        {{-- ~~ END: Visibility / Pin / Priority ~~ --}}

        {{-- ~~ START: Summary ~~ --}}
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Ringkasan (otomatis, bisa diedit)</label>
          <textarea name="summary" id="summary-input" rows="3" class="nice-textarea" placeholder="Ringkasan singkat…">{{ old('summary', $post->summary) }}</textarea>
          <p class="text-xs text-gray-500 mt-1">Kosongkan untuk otomatis dari konten (≈160–200 karakter).</p>
        </div>
        {{-- ~~ END: Summary ~~ --}}

        {{-- ~~ START: Content (Quill) ~~ --}}
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Isi Konten <span class="text-red-500">*</span></label>
          <div id="editor-wrap"><div id="editor"></div></div>
          <input type="hidden" name="content" id="content-input">
        </div>
        {{-- ~~ END: Content (Quill) ~~ --}}
      </div>

      {{-- ~~ START: Form Actions ~~ --}}
      <div class="flex justify-end items-center gap-4 mt-8 pt-6 border-t">
        <a href="{{ route('workdesk') }}" class="text-gray-600 font-medium py-2 px-5 rounded-lg hover:bg-gray-100">Batal</a>
        <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-5 rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
      </div>
      {{-- ~~ END: Form Actions ~~ --}}
    </form>
    {{-- ---- END: Form ---- --}}

  </div>
</main>
{{-- ===== END: Main ===== --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.js"></script>
<script>
  // seed
  window.oldContent = @json(old('content', $post->content));
  window.oldSummary = @json(old('summary', $post->summary));
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  /* === START: Type pills === */
  const radios = {
    announcement : document.getElementById('type-ann'),
    update       : document.getElementById('type-upd'),
    maintenance  : document.getElementById('type-mnt'),
    info         : document.getElementById('type-inf'),
  };
  const pills = {
    announcement : document.getElementById('pill-ann'),
    update       : document.getElementById('pill-upd'),
    maintenance  : document.getElementById('pill-mnt'),
    info         : document.getElementById('pill-inf'),
  };
  const badgeEl = document.getElementById('typeChip');

  const TYPE_CLS = {
    announcement: { pill: 'pill-ann', chip: 'chip-ann', label: 'Pengumuman',  icon: 'fa-bullhorn' },
    update      : { pill: 'pill-upd', chip: 'chip-upd', label: 'Pembaruan',   icon: 'fa-arrows-rotate' },
    maintenance : { pill: 'pill-mnt', chip: 'chip-mnt', label: 'Perawatan',   icon: 'fa-screwdriver-wrench' },
    info        : { pill: 'pill-inf', chip: 'chip-inf', label: 'Info',        icon: 'fa-circle-info' },
  };

  function setActiveType(type) {
    Object.keys(pills).forEach(t => {
      const el = pills[t]; if (!el) return;
      el.classList.remove('pill-active', TYPE_CLS[t].pill);
      el.classList.add('pill-inactive');
      if (radios[t]) radios[t].checked = false;
    });
    const active = pills[type];
    if (active) { active.classList.remove('pill-inactive'); active.classList.add('pill-active', TYPE_CLS[type].pill); }
    if (radios[type]) radios[type].checked = true;

    if (badgeEl) {
      badgeEl.className = 'chip ' + TYPE_CLS[type].chip;
      const iconEl = badgeEl.querySelector('i'); if (iconEl) iconEl.className = 'fa-solid ' + TYPE_CLS[type].icon;
      const textSpan = badgeEl.querySelector('span'); if (textSpan) textSpan.textContent = TYPE_CLS[type].label;
    }
  }
  Object.keys(pills).forEach(t => pills[t]?.addEventListener('click', () => setActiveType(t)));
  Object.keys(radios).forEach(t => radios[t]?.addEventListener('change', () => setActiveType(t)));
  const initialType =
    (radios.announcement?.checked && 'announcement') ||
    (radios.update?.checked && 'update') ||
    (radios.maintenance?.checked && 'maintenance') ||
    (radios.info?.checked && 'info') || 'info';
  setActiveType(initialType);
  /* === END: Type pills === */

  /* === START: Nice Select (visibility) === */
  (function initNiceSelect() {
    const wrap = document.getElementById('visibility-nice'); if (!wrap) return;
    const selectId = wrap.getAttribute('data-target');
    const selectEl = document.getElementById(selectId);
    const trigger  = wrap.querySelector('.ns-trigger');
    const labelEl  = wrap.querySelector('.ns-trigger-label');
    const items    = Array.from(wrap.querySelectorAll('.ns-item'));

    function setLabel(val) {
      labelEl.innerHTML = (val === 'private')
        ? '<i class="fa-solid fa-lock"></i> Privat (hanya admin)'
        : '<i class="fa-solid fa-earth-americas"></i> Publik';
    }
    function close(){ wrap.classList.remove('ns-open'); trigger?.setAttribute('aria-expanded','false'); }
    function open(){  wrap.classList.add('ns-open');    trigger?.setAttribute('aria-expanded','true'); }

    const initVal = selectEl?.value || '{{ old('visibility', $post->visibility ?? 'public') }}';
    setLabel(initVal);
    items.forEach(li => li.setAttribute('aria-selected', String(li.getAttribute('data-value') === initVal)));

    trigger?.addEventListener('click', e => { e.preventDefault(); wrap.classList.contains('ns-open') ? close() : open(); });
    items.forEach(li => {
      li.addEventListener('click', () => {
        const val = li.getAttribute('data-value');
        if (selectEl) { selectEl.value = val; selectEl.dispatchEvent(new Event('change', { bubbles: true })); }
        setLabel(val);
        items.forEach(x => x.setAttribute('aria-selected', String(x === li)));
        close();
      });
    });
    document.addEventListener('click', e => { if (!wrap.contains(e.target)) close(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
  })();
  /* === END: Nice Select (visibility) === */

  /* === START: Quill + Upload + Auto Summary === */
  function useTextareaFallback() {
    const editorEl = document.getElementById('editor'); if (!editorEl) return;
    const ta = document.createElement('textarea'); ta.name = 'content'; ta.rows = 12; ta.className = 'nice-textarea';
    ta.value = window.oldContent || ''; editorEl.replaceWith(ta); document.getElementById('content-input')?.remove();
  }

  function initQuill() {
    const editorEl = document.getElementById('editor');
    const hiddenContent = document.getElementById('content-input');
    const summaryInput  = document.getElementById('summary-input');
    if (!editorEl) return;
    if (!window.Quill) { useTextareaFallback(); return; }

    const quill = new Quill('#editor', {
      theme: 'snow',
      modules: {
        toolbar: [
          [{ header: [1,2,3,false] }],
          ['bold','italic','underline','strike'],
          [{ list:'ordered' },{ list:'bullet' }],
          [{ align:[] }],
          ['link','blockquote','code-block','image'],
          [{ color:[] },{ background:[] }],
          ['clean']
        ]
      },
      placeholder: 'Tulis konten postingan di sini...'
    });

    // seed content
    if (window.oldContent) {
      try { const maybe = JSON.parse(window.oldContent); maybe?.ops ? quill.setContents(maybe) : quill.root.innerHTML = window.oldContent; }
      catch { quill.root.innerHTML = window.oldContent; }
    }

    // image upload (toolbar/paste/drop)
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const MAX_FILE_MB = 10, COMPRESS_AFTER_MB = 2.5, MAX_WIDTH = 1600;

    quill.getModule('toolbar')?.addHandler('image', () => selectLocalImage());
    function selectLocalImage(){ const i=document.createElement('input'); i.type='file'; i.accept='image/*'; i.onchange=()=>i.files?.[0]&&processAndInsertImage(i.files[0]); i.click(); }
    quill.root.addEventListener('paste', async e => {
      const item = Array.from(e.clipboardData?.items||[]).find(i=>i.type.startsWith('image/')); if(!item) return;
      e.preventDefault(); const file=item.getAsFile(); if(file) await processAndInsertImage(file);
    });
    quill.root.addEventListener('drop', async e => {
      const file = e.dataTransfer?.files?.[0]; if(!file||!file.type.startsWith('image/')) return;
      e.preventDefault(); e.stopPropagation(); await processAndInsertImage(file);
    });

    async function processAndInsertImage(file){
      if(file.size > MAX_FILE_MB*1024*1024){ alert(`Ukuran gambar > ${MAX_FILE_MB}MB`); return; }
      let blobToSend=file, filename=file.name;
      if(file.size > COMPRESS_AFTER_MB*1024*1024){
        try{ const c=await compressImage(file,MAX_WIDTH,0.85); blobToSend=c.blob; filename=c.filename; }catch{}
      }
      const url = await uploadToServer(blobToSend, filename);
      const range = quill.getSelection(true) || { index: quill.getLength(), length: 0 };
      quill.insertEmbed(range.index,'image',url,Quill.sources.USER);
      quill.setSelection(range.index+1,0,Quill.sources.SILENT);
      requestAnimationFrame(()=>{ const imgs=document.querySelectorAll('.ql-editor img'); const img=imgs[imgs.length-1]; if(img){img.setAttribute('loading','lazy'); img.removeAttribute('width'); img.removeAttribute('height');} });
    }

    async function uploadToServer(blob, filename){
      const form=new FormData(); form.append('image', blob, filename);
      const res=await fetch('{{ route('editor.image.upload') }}',{method:'POST',headers:{'X-CSRF-TOKEN':csrf},body:form});
      if(!res.ok) throw new Error('Upload gagal');
      const data=await res.json(); if(!data?.url) throw new Error('Respon server tidak valid'); return data.url;
    }

    function compressImage(file,maxWidth,quality=0.85){
      return new Promise((resolve,reject)=>{
        const img=new Image();
        img.onload=()=>{
          let {width,height}=img;
          if(width>height && width>maxWidth){height=Math.round(height*maxWidth/width); width=maxWidth;}
          else if(height>=width && height>maxWidth){width=Math.round(width*maxWidth/height); height=maxWidth;}
          const canvas=document.createElement('canvas'); canvas.width=width; canvas.height=height;
          const ctx=canvas.getContext('2d'); ctx.drawImage(img,0,0,width,height);
          canvas.toBlob((blob)=>{ if(!blob) return reject(new Error('Gagal kompres'));
            const ext=(file.name.split('.').pop()||'jpg').toLowerCase();
            const out=file.name.replace(/\.(\w+)$/,'')+'-compressed.'+(ext==='png'?'png':'jpg');
            resolve({blob,filename:out});
          }, (file.type==='image/png'?'image/png':'image/jpeg'), quality);
        };
        img.onerror=()=>reject(new Error('Tidak bisa membaca gambar'));
        const fr=new FileReader(); fr.onload=()=>img.src=fr.result; fr.onerror=()=>reject(new Error('FileReader gagal')); fr.readAsDataURL(file);
      });
    }

    // auto-summary
    let summaryDirty=false;
    if(summaryInput){ if(window.oldSummary) summaryInput.value=window.oldSummary; summaryInput.addEventListener('input',()=>{summaryDirty=true;}); }
    const summarize=(t,max=190)=>{ if(!t) return ''; let s=t.replace(/\s+/g,' ').trim(); if(s.length<=max) return s; let cut=s.slice(0,max); const last=cut.lastIndexOf(' '); if(last>60) cut=cut.slice(0,last); return cut+'…'; };
    const sync=()=>{ if(hiddenContent) hiddenContent.value=quill.root.innerHTML; if(!summaryDirty&&summaryInput) summaryInput.value=summarize(quill.getText(),190); };
    quill.on('text-change', sync); sync();

    // submit
    document.getElementById('post-form')?.addEventListener('submit', ()=>{
      if(hiddenContent) hiddenContent.value=quill.root.innerHTML.trim();
      if(summaryInput && !summaryInput.value.trim()) summaryInput.value=summarize(quill.getText(),190);
    });
  }

  initQuill();
  /* === END: Quill + Upload + Auto Summary === */
});
</script>
@endpush
