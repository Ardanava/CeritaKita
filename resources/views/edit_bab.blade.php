@extends('layouts.app')

@section('title', 'Edit Bab - '.$story->title)

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,700;1,400&family=Roboto+Mono&family=Poppins:wght@400;500;600&family=Cinzel:wght@400;700&family=Dancing+Script:wght@400;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.snow.css" rel="stylesheet">

<style>
  /* --- Editor look --- */
  #editor-container { height: 500px; display:flex; flex-direction:column; overflow:hidden; border-radius:.75rem; }
  #editor { display:flex; flex-direction:column; flex-grow:1; min-height:0; }
  .ql-container { flex-grow:1; font-size:1.125rem; line-height:1.75; overflow-y:auto; min-height:0; }
  .ql-editor { font-family:'Lora', serif; }

  /* Gambar (editor + preview) */
  .ql-editor img, #preview-body img{
    width:auto; max-width:min(100%, 640px); height:auto;
    display:block; margin:.5rem auto; border-radius:.5rem;
  }

  /* Drag-to-resize wrapper */
  .img-resizable { position:relative; display:inline-block; }
  .img-resizable img { display:block; }
  .img-resize-handle{
    position:absolute; right:-6px; bottom:-6px; width:12px; height:12px;
    border-radius:999px; background:#2563eb; cursor:nwse-resize; box-shadow:0 1px 4px rgba(0,0,0,.25);
  }

  /* Status bar */
  #editor-status { gap:.5rem; }
</style>
@endpush

@section('content')
<main class="container mx-auto px-4 lg:px-8 py-12">
  <div class="max-w-4xl mx-auto">

    {{-- ===== START: Back Link ===== --}}
    <a href="{{ route('stories.manage', $story->slug) }}"
       class="nav-link text-sm font-semibold text-blue-600 hover:underline flex items-center gap-2 mb-4">
      <i class="fa-solid fa-arrow-left"></i> Kembali ke Atur Cerita
    </a>
    {{-- ===== END: Back Link ===== --}}

    {{-- ===== START: Page Heading ===== --}}
    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">Edit Bab</h1>
    <p class="text-gray-600 mt-2">
      Cerita: <span class="font-semibold">{{ $story->title }}</span>
    </p>
    {{-- ===== END: Page Heading ===== --}}

    {{-- ===== START: Form ===== --}}
    <form id="edit-chapter-form"
          method="POST"
          action="{{ route('chapters.update', ['story'=>$story, 'chapter'=>$chapter]) }}"
          class="mt-8 bg-white rounded-2xl shadow-lg">
      @csrf
      @method('PUT')

      <input type="hidden" name="content" id="chapter_content">
      <input type="hidden" name="status"  id="chapter_status" value="{{ strtolower($chapter->status)==='published'?'published':'draft' }}">

      <div class="p-6 md:p-8 space-y-6">

        {{-- ~~ START: Title Field ~~ --}}
        <div>
          <label for="chapter-title" class="text-lg font-semibold text-gray-700">Judul Bab</label>
          <input id="chapter-title" name="title" type="text" value="{{ old('title', $chapter->title) }}" autocomplete="off" placeholder="Judul bab…" class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg" required>
          @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        {{-- ~~ END: Title Field ~~ --}}

        {{-- ~~ START: Editor ~~ --}}
        <div>
          <label class="text-lg font-semibold text-gray-700">Isi Bab</label>
          <div id="editor-container" class="mt-2 border border-gray-300">
            <div id="editor">{!! old('content', $chapter->content) !!}</div>
          </div>

          {{-- :: START: Editor Status Bar :: --}}
          <div id="editor-status" class="mt-2 flex flex-wrap items-center text-sm text-gray-500" aria-live="polite">
            <span id="wc">0 kata</span><span>•</span>
            <span id="rt">0 menit baca</span><span>•</span>
            <span id="autosave" class="inline-flex items-center gap-1">
              <i class="fa-regular fa-circle" id="autosave-dot" aria-hidden="true"></i> belum tersimpan
            </span>
            <button type="button" id="preview-btn" class="ml-auto text-blue-600 font-semibold hover:underline">
              <i class="fa-solid fa-eye"></i> Pratinjau
            </button>
          </div>
          {{-- :: END: Editor Status Bar :: --}}

          @error('content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        {{-- ~~ END: Editor ~~ --}}

        {{-- ~~ START: Author Note ~~ --}}
        <div>
          <label for="author-note" class="text-lg font-semibold text-gray-700">Catatan Penulis (Opsional)</label>
          <textarea id="author-note" name="author_note" rows="4"
                    class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-base"
                    placeholder="Sampaikan sesuatu kepada pembacamu...">{{ old('author_note', $chapter->author_note) }}</textarea>
          @error('author_note') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        {{-- ~~ END: Author Note ~~ --}}

      </div>

      {{-- ~~ START: Form Actions ~~ --}}
      <div class="px-6 md:px-8 pb-6 md:pb-8 flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t">
        <a href="{{ route('stories.manage', $story->slug) }}"
           class="nav-link text-center bg-gray-200 text-gray-800 font-semibold px-6 py-3 rounded-full hover:bg-gray-300 transition-colors">
          Batal
        </a>
        <button type="submit" id="publish-btn"
                class="bg-blue-600 text-white font-semibold px-6 py-3 rounded-full hover:bg-blue-700 transition-colors">
          Perbarui & Terbitkan
        </button>
      </div>
      {{-- ~~ END: Form Actions ~~ --}}
    </form>
    {{-- ===== END: Form ===== --}}
  </div>
</main>

{{-- ===== START: Discard Changes Modal ===== --}}
<div id="discard-changes-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4 bg-black/50">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-sm text-center p-6">
    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
      <i class="fa-solid fa-triangle-exclamation text-2xl text-yellow-500" aria-hidden="true"></i>
    </div>
    <h3 class="text-lg font-bold text-gray-800 mt-4">Buang Perubahan?</h3>
    <p class="text-gray-600 mt-2 text-sm">Anda memiliki perubahan yang belum disimpan. Buang perubahan?</p>
    <div class="mt-6 flex justify-center gap-4">
      <button id="keep-editing-btn" class="bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-full hover:bg-gray-300">Tetap</button>
      <a id="discard-confirm-btn" href="#" class="bg-red-600 text-white font-semibold px-6 py-2 rounded-full hover:bg-red-700">Buang</a>
    </div>
  </div>
</div>
{{-- ===== END: Discard Changes Modal ===== --}}

{{-- ===== START: Preview Modal ===== --}}
<div id="preview-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4 bg-black/50">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl">
    <div class="px-5 py-3 border-b flex items-center justify-between">
      <h3 class="font-bold">Pratinjau Bab</h3>
      <button id="preview-close" class="text-gray-500 hover:text-gray-700" aria-label="Tutup pratinjau"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="p-5 max-h-[70vh] overflow-auto">
      <h2 class="text-2xl font-bold mb-3" id="preview-title"></h2>
      <article id="preview-body" class="prose max-w-none"></article>
      <div id="preview-author-note" class="mt-6 rounded-lg border p-4 bg-blue-50 text-blue-800 hidden">
        <strong>Catatan Penulis</strong>
        <div id="preview-note-body" class="mt-1"></div>
      </div>
    </div>
    <div class="px-5 py-3 border-t flex justify-end">
      <button id="preview-close-bottom" class="bg-gray-200 text-gray-800 font-semibold px-4 py-2 rounded-full hover:bg-gray-300">Tutup</button>
    </div>
  </div>
</div>
{{-- ===== END: Preview Modal ===== --}}
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  let formChanged = false;

  /* ===== START: Autosave keys ===== */
  const storyKey   = @json($story->slug ?? ('story-' + ($story->id ?? 'new')));
  const chapterKey = @json($chapter->id ?? 'new');
  const LS_KEY     = `ck_edit_draft_${storyKey}_${chapterKey}`;
  /* ===== END: Autosave keys ===== */

  /* ===== START: Refs ===== */
  const form         = document.getElementById('edit-chapter-form');
  const titleInput   = document.getElementById('chapter-title');
  const noteInput    = document.getElementById('author-note');
  const contentInput = document.getElementById('chapter_content');
  const statusInput  = document.getElementById('chapter_status');
  const publishBtn   = document.getElementById('publish-btn');

  const wcEl  = document.getElementById('wc');
  const rtEl  = document.getElementById('rt');
  const asEl  = document.getElementById('autosave');
  const asDot = document.getElementById('autosave-dot');
  /* ===== END: Refs ===== */

  /* ===== START: Quill setup ===== */
  const Font = Quill.import('formats/font');
  Font.whitelist = ['serif','monospace','poppins','cinzel','dancing'];
  Quill.register(Font, true);

  const quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
      toolbar: [
        [{'font':['','serif','monospace','poppins','cinzel','dancing']},{'size':['small',false,'large','huge']}],
        ['bold','italic','underline','strike'],
        [{'color':[]},{'background':[]}],
        [{'align':[]}],
        [{'header':1},{'header':2}],
        ['blockquote','code-block'],
        [{'list':'ordered'},{'list':'bullet'},{'indent':'-1'},{'indent':'+1'}],
        ['link','image'],
        ['clean']
      ],
      clipboard: {
        matchers: [
          ['*',(node,delta)=>{ node.removeAttribute?.('style'); node.removeAttribute?.('class'); return delta; }],
          ['span',(node,delta)=>delta]
        ]
      }
    },
    placeholder: 'Mulai tulis ceritamu di sini...'
  });
  /* ===== END: Quill setup ===== */

  /* ===== START: Image upload ===== */
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  quill.getModule('toolbar')?.addHandler('image', () => {
    const i = document.createElement('input');
    i.type='file'; i.accept='image/*';
    i.onchange = async () => { if (i.files?.[0]) await upsertImage(i.files[0]); };
    i.click();
  });

  async function upsertImage(file){
    try{
      const fd = new FormData();
      fd.append('image', file, file.name);
      const res = await fetch(@json(route('editor.image.upload')), {
        method:'POST', headers:{'X-CSRF-TOKEN': csrf}, body: fd
      });
      if(!res.ok) throw new Error('Upload gagal');
      const { url } = await res.json();
      if(!url) throw new Error('Respon tidak valid');

      const range = quill.getSelection(true) || { index: quill.getLength(), length: 0 };
      quill.insertEmbed(range.index, 'image', url, Quill.sources.USER);
      quill.setSelection(range.index + 1, 0, Quill.sources.SILENT);

      requestAnimationFrame(() => {
        const imgs = document.querySelectorAll('.ql-editor img');
        const img  = imgs[imgs.length - 1];
        if (img) {
          img.loading='lazy';
          img.removeAttribute('width'); img.removeAttribute('height');
          img.style.maxWidth='100%'; img.style.height='auto';
          img.style.display='block'; img.style.margin='.5rem auto'; img.style.borderRadius='.5rem';
        }
      });
      touchChanged();
    }catch(e){ console.error(e); alert('Gagal mengunggah gambar.'); }
  }
  /* ===== END: Image upload ===== */

  /* ===== START: Stats ===== */
  function updateStats(){
    const words = (quill.getText().trim().match(/\S+/g) || []).length;
    const mins  = Math.max(1, Math.round(words/200));
    if (wcEl) wcEl.textContent = `${words} kata`;
    if (rtEl) rtEl.textContent = `${mins} menit baca`;
  }
  updateStats();
  /* ===== END: Stats ===== */

  /* ===== START: Autosave ===== */
  let autosaveTimer = null;
  function setAutosaveUI(saved){
    if (!asEl || !asDot) return;
    if (saved){
      asDot.className = 'fa-solid fa-circle text-emerald-500';
      asEl.lastChild && (asEl.lastChild.nodeValue = ' tersimpan otomatis');
    }else{
      asDot.className = 'fa-solid fa-circle text-amber-500';
      asEl.lastChild && (asEl.lastChild.nodeValue = ' belum tersimpan');
    }
  }
  function doAutosave(){
    try{
      const payload = {
        title: titleInput?.value || '',
        content: quill.root.innerHTML || '',
        note: noteInput?.value || '',
        ts: Date.now()
      };
      localStorage.setItem(LS_KEY, JSON.stringify(payload));
      setAutosaveUI(true);
    }catch(e){ console.warn('Autosave gagal:', e); }
  }
  function touchChanged(){
    formChanged = true;
    setAutosaveUI(false);
    if (autosaveTimer) clearTimeout(autosaveTimer);
    autosaveTimer = setTimeout(doAutosave, 1200);
  }

  try{
    const raw = localStorage.getItem(LS_KEY);
    if (raw){
      const saved = JSON.parse(raw);
      if (saved?.title && titleInput)  titleInput.value = saved.title;
      if (saved?.content)              quill.root.innerHTML = saved.content;
      if (saved?.note && noteInput)    noteInput.value = saved.note;
      setAutosaveUI(true);
      updateStats();
    }else{
      setAutosaveUI(false);
    }
  }catch{ setAutosaveUI(false); }

  quill.on('text-change', (_, __, src)=>{ if(src==='user'){ updateStats(); touchChanged(); } });
  form?.addEventListener('input', touchChanged);
  /* ===== END: Autosave ===== */

  /* ===== START: Submit/Publish ===== */
  publishBtn?.addEventListener('click', () => { if (statusInput) statusInput.value = 'published'; });

  form?.addEventListener('submit', (e) => {
    const minChars = 20;
    if (quill.getText().trim().length < minChars) {
      e.preventDefault();
      alert(`Isi bab terlalu pendek. Minimal ${minChars} karakter.`);
      return;
    }
    if (statusInput) statusInput.value = 'published';
    contentInput.value = quill.root.innerHTML;
    formChanged = false;
    try{ localStorage.removeItem(LS_KEY); }catch{}
  });

  // Ctrl/Cmd + Enter => publish
  document.addEventListener('keydown', (e) => {
    const isMac = /Mac/i.test(navigator.platform);
    const mod   = isMac ? e.metaKey : e.ctrlKey;
    if (mod && e.key === 'Enter') {
      e.preventDefault();
      if (statusInput) statusInput.value = 'published';
      contentInput.value = quill.root.innerHTML;
      form?.requestSubmit();
    }
  });
  /* ===== END: Submit/Publish ===== */

  /* ===== START: Preview ===== */
  const previewBtn   = document.getElementById('preview-btn');
  const previewModal = document.getElementById('preview-modal');
  const pClose1      = document.getElementById('preview-close');
  const pClose2      = document.getElementById('preview-close-bottom');
  const pTitle       = document.getElementById('preview-title');
  const pBody        = document.getElementById('preview-body');
  const pNoteWrap    = document.getElementById('preview-author-note');
  const pNoteBody    = document.getElementById('preview-note-body');

  function openPreview(){
    if (!previewModal) return;
    pTitle && (pTitle.textContent = titleInput?.value?.trim() || '(Tanpa judul)');
    if (pBody){
      pBody.innerHTML = quill.root.innerHTML || '<p><em>(Kosong)</em></p>';
      pBody.querySelectorAll('img').forEach(img=>{
        img.loading='lazy'; img.style.maxWidth='100%'; img.style.height='auto';
        img.style.display='block'; img.style.margin='.5rem auto'; img.style.borderRadius='.5rem';
      });
    }
    const note = noteInput?.value?.trim();
    if (pNoteWrap && pNoteBody){
      if (note){ pNoteBody.textContent = note; pNoteWrap.classList.remove('hidden'); }
      else     { pNoteWrap.classList.add('hidden'); }
    }
    previewModal.classList.remove('hidden'); previewModal.classList.add('flex');
  }
  function closePreview(){ previewModal?.classList.add('hidden'); previewModal?.classList.remove('flex'); }
  previewBtn?.addEventListener('click', openPreview);
  pClose1?.addEventListener('click', closePreview);
  pClose2?.addEventListener('click', closePreview);
  previewModal?.addEventListener('click', (e)=>{ if(e.target===previewModal) closePreview(); });
  /* ===== END: Preview ===== */

  /* ===== START: Discard changes modal ===== */
  const discardModal   = document.getElementById('discard-changes-modal');
  const discardConfirm = document.getElementById('discard-confirm-btn');
  const keepEditingBtn = document.getElementById('keep-editing-btn');
  const navLinks       = document.querySelectorAll('.nav-link');
  let navigateUrl = '#';

  function showDiscard(url){
    if(formChanged){ navigateUrl=url; discardModal.classList.remove('hidden'); discardModal.classList.add('flex'); }
    else window.location.href=url;
  }
  navLinks.forEach(a=>a.addEventListener('click',(e)=>{ e.preventDefault(); showDiscard(a.href); }));
  discardConfirm?.addEventListener('click',(e)=>{ e.preventDefault(); formChanged=false; window.location.href=navigateUrl; });
  const closeDiscard = ()=>{ discardModal.classList.add('hidden'); discardModal.classList.remove('flex'); };
  keepEditingBtn?.addEventListener('click', closeDiscard);
  discardModal?.addEventListener('click',(e)=>{ if(e.target===discardModal) closeDiscard(); });

  window.onbeforeunload = function(e){
    if(formChanged){ e.preventDefault(); e.returnValue=''; return ''; }
  };
  /* ===== END: Discard changes modal ===== */
});
</script>
@endpush
