@extends('layouts.app')

@section('title', 'Tambah Bab Baru - CeritaKita')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,700;1,400&family=Roboto+Mono&family=Poppins:wght@400;500;600&family=Cinzel:wght@400;700&family=Dancing+Script:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.snow.css" rel="stylesheet">

    <style>
        /* ===================== QUILL EDITOR STYLING: START ===================== */
        #editor-container { height: 500px; display: flex; flex-direction: column; overflow: hidden; }
        .ql-editor img, #preview-body img { width: auto; max-width: min(100%, 640px); height: auto; display: block; margin: .5rem auto; border-radius: .5rem; }
        .ql-editor img{ max-width:100%; height:auto; display:block; margin: .5rem auto; }
        #editor-container{ overflow:hidden; border-radius: .5rem; } .ql-container{ overflow-y:auto; }
        #editor { display: flex; flex-direction: column; flex-grow: 1; min-height: 0; }
        .ql-container { flex-grow: 1; font-size: 1.125rem; line-height: 1.75; overflow-y: auto; min-height: 0; }
        .ql-editor { font-family: 'Lora', serif; }
        /* Font label di picker */
        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value=serif]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value=serif]::before { font-family: 'Lora', serif; content: 'Serif'; }
        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value=monospace]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value=monospace]::before { font-family: 'Roboto Mono', monospace; content: 'Monospace'; }
        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value=poppins]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value=poppins]::before { font-family: 'Poppins', sans-serif; content: 'Poppins'; }
        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value=cinzel]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value=cinzel]::before { font-family: 'Cinzel', serif; content: 'Hermes-style'; }
        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value=dancing]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value=dancing]::before { font-family: 'Dancing Script', cursive; content: 'Loenese-style'; }
        .ql-font-serif { font-family: 'Lora', serif; }
        .ql-font-monospace { font-family: 'Roboto Mono', monospace; }
        .ql-font-poppins { font-family: 'Poppins', sans-serif; }
        .ql-font-cinzel { font-family: 'Cinzel', serif; }
        .ql-font-dancing { font-family: 'Dancing Script', cursive; }
        /* ===================== QUILL EDITOR STYLING: END ======================= */
    </style>
@endpush

@section('content')
    {{-- ===================== PAGE MAIN CONTENT: START ===================== --}}
    <main class="container mx-auto px-4 lg:px-8 py-12">
        <div class="max-w-4xl mx-auto">
            {{-- === BACK LINK: START === --}}
            <a href="{{ route('chapters.create', $story) }}" class="nav-link text-sm font-semibold text-blue-600 hover:underline flex items-center gap-2 mb-4">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Atur Cerita
            </a>
            {{-- === BACK LINK: END === --}}

            {{-- === PAGE TITLE + SUBTITLE: START === --}}
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">Tambah Bab Baru</h1>
            <p class="text-gray-600 mt-2">Untuk cerita: <span class="font-semibold">{{ $story->title }}</span></p>
            {{-- === PAGE TITLE + SUBTITLE: END === --}}
            
            {{-- ================= FORM WRAPPER (NEW CHAPTER): START ================= --}}
            <form id="new-chapter-form" action="{{ route('chapters.store', $story->slug) }}" method="POST" class="mt-8 bg-white rounded-2xl shadow-lg">
                @csrf
                
                {{-- --- HIDDEN INPUTS (content + status): START --- --}}
                <input type="hidden" name="chapter_content" id="chapter_content">
                <input type="hidden" name="status" id="chapter_status" value="draft">
                {{-- --- HIDDEN INPUTS: END --- --}}

                {{-- --- FORM BODY: START --- --}}
                <div class="p-6 md:p-8 space-y-6">
                    {{-- Field: Judul Bab: START --}}
                    <div>
                        <label for="chapter-title" class="text-lg font-semibold text-gray-700">Judul Bab</label>
                        <input id="chapter-title" autocomplete="off" name="chapter_title" type="text" placeholder="Contoh: Penemuan di Gua Kristal" class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg" required>
                    </div>
                    {{-- Field: Judul Bab: END --}}

                    {{-- Field: Isi Bab + Status Bar: START --}}
                    <div>
                        <label class="text-lg font-semibold text-gray-700">Isi Bab</label>
                        <div id="editor-container" class="mt-2 rounded-lg border border-gray-300">
                            <div id="editor"></div>
                        </div>
                        <div id="editor-status" class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-500">
                            <span id="wc">0 kata</span>
                            <span>•</span>
                            <span id="rt">0 menit baca</span>
                            <span>•</span>
                            <span id="autosave" class="inline-flex items-center gap-1">
                                <i class="fa-regular fa-circle" id="autosave-dot"></i> belum tersimpan
                            </span>
                            <button type="button" id="preview-btn" class="ml-auto text-blue-600 font-semibold hover:underline">
                                <i class="fa-solid fa-eye"></i> Pratinjau
                            </button>
                        </div>
                    </div>
                    {{-- Field: Isi Bab + Status Bar: END --}}

                    {{-- Field: Catatan Penulis: START --}}
                    <div>
                        <label for="author-note" class="text-lg font-semibold text-gray-700">Catatan Penulis (Opsional)</label>
                        <textarea id="author-note" name="author_note" rows="4" class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-base" placeholder="Sampaikan sesuatu kepada pembacamu..."></textarea>
                    </div>
                    {{-- Field: Catatan Penulis: END --}}
                </div>
                {{-- --- FORM BODY: END --- --}}

                {{-- --- FORM ACTIONS (FOOTER BUTTONS): START --- --}}
                <div class="px-6 md:px-8 pb-6 md:pb-8 flex flex-col sm:flex-row justify-end gap-4 pt-4 border-t">
                    <a href="{{ route('stories.manage', $story->slug) }}" class="nav-link text-center bg-gray-200 text-gray-800 font-semibold px-8 py-3 rounded-full hover:bg-gray-300 transition-colors">Batal</a>
                    <button type="submit" id="publish-btn" class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-full hover:bg-blue-700 transition-colors">Terbitkan Bab</button>
                </div>
                {{-- --- FORM ACTIONS: END --- --}}
            </form>
            {{-- ================= FORM WRAPPER: END ================= --}}
        </div>
    </main>
    {{-- ===================== PAGE MAIN CONTENT: END ===================== --}}

    {{-- ===================== MODAL: DISCARD CHANGES: START ===================== --}}
    <div id="discard-changes-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-sm text-center p-6">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                <i class="fa-solid fa-triangle-exclamation text-2xl text-yellow-500"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mt-4">Buang Perubahan?</h3>
            <p class="text-gray-600 mt-2 text-sm">Anda memiliki perubahan yang belum disimpan. Apakah Anda yakin ingin membatalkannya?</p>
            <div class="mt-6 flex justify-center gap-4">
                <button id="keep-editing-btn" class="bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-full hover:bg-gray-300">Tetap di Sini</button>
                <a id="discard-confirm-btn" href="#" class="bg-red-600 text-white font-semibold px-6 py-2 rounded-full hover:bg-red-700 flex items-center justify-center">Buang</a>
            </div>
        </div>
    </div>
    {{-- ===================== MODAL: DISCARD CHANGES: END ======================= --}}

    {{-- ===================== MODAL: PREVIEW: START ===================== --}}
    <div id="preview-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl">
            <div class="px-5 py-3 border-b flex items-center justify-between">
                <h3 class="font-bold">Pratinjau Bab</h3>
                <button id="preview-close" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-xmark"></i></button>
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
    {{-- ===================== MODAL: PREVIEW: END ======================= --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  let formChanged = false;

  /* ================= AUTOSAVE KEYS & COMMON ELEMENTS: START ================= */
  const storyKey = @json($story->slug ?? ('story-' . ($story->id ?? 'new')));
  const LS_KEY   = `ck_chapter_draft_${storyKey}`;
  const form         = document.getElementById('new-chapter-form');
  const titleInput   = document.getElementById('chapter-title');
  const noteInput    = document.getElementById('author-note');
  const contentInput = document.getElementById('chapter_content');
  const statusInput  = document.getElementById('chapter_status');
  const publishBtn   = document.getElementById('publish-btn');
  const wcEl  = document.getElementById('wc');
  const rtEl  = document.getElementById('rt');
  const asEl  = document.getElementById('autosave');
  const asDot = document.getElementById('autosave-dot');
  /* ================= AUTOSAVE KEYS & COMMON ELEMENTS: END =================== */

  /* ================= QUILL INIT: START ================= */
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
      clipboard: { matchers: [['*',(node,delta)=>{ node.removeAttribute?.('style'); node.removeAttribute?.('class'); return delta; }], ['span',(node,delta)=>delta]] }
    },
    placeholder: 'Mulai tulis ceritamu di sini...'
  });
  /* ================= QUILL INIT: END =================== */

  /* ================= IMAGE UPLOAD HANDLER: START ================= */
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  quill.getModule('toolbar')?.addHandler('image', () => { const i=document.createElement('input'); i.type='file'; i.accept='image/*'; i.onchange = async () => { if (i.files?.[0]) await upsertImage(i.files[0]); }; i.click(); });
  async function upsertImage(file){ try{ const formData=new FormData(); formData.append('image', file, file.name); const res = await fetch(@json(route('editor.image.upload')), { method:'POST', headers:{'X-CSRF-TOKEN': csrf}, body: formData }); if(!res.ok) throw new Error('Upload gagal'); const { url } = await res.json(); if(!url) throw new Error('Respon tidak valid'); const range = quill.getSelection(true) || { index: quill.getLength(), length: 0 }; quill.insertEmbed(range.index, 'image', url, Quill.sources.USER); quill.setSelection(range.index + 1, 0, Quill.sources.SILENT); requestAnimationFrame(()=>{ const imgs=document.querySelectorAll('.ql-editor img'); const img=imgs[imgs.length-1]; if(img){ img.loading='lazy'; img.removeAttribute('width'); img.removeAttribute('height'); img.style.maxWidth='100%'; img.style.height='auto'; img.style.display='block'; img.style.margin='.5rem auto'; img.style.borderRadius='.5rem'; } }); touchChanged(); }catch(e){ console.error(e); alert('Gagal mengunggah gambar.'); } }
  /* ================= IMAGE UPLOAD HANDLER: END =================== */

  /* ================= STATUS BAR (WORDS/MINUTES): START ================= */
  function updateStats(){ const words=(quill.getText().trim().match(/\S+/g)||[]).length; const mins=Math.max(1, Math.round(words/200)); if(wcEl) wcEl.textContent=`${words} kata`; if(rtEl) rtEl.textContent=`${mins} menit baca`; }
  updateStats();
  /* ================= STATUS BAR: END =============================== */

  /* ================= AUTOSAVE (LOCALSTORAGE): START ================= */
  let autosaveTimer=null;
  function setAutosaveUI(saved){ if(!asDot||!asEl) return; if(saved){ asDot.className='fa-solid fa-circle text-emerald-500'; asEl.lastChild && (asEl.lastChild.nodeValue=' tersimpan otomatis'); } else { asDot.className='fa-solid fa-circle text-amber-500'; asEl.lastChild && (asEl.lastChild.nodeValue=' belum tersimpan'); } }
  function doAutosave(){ try{ const payload={ title:titleInput?.value||'', content:quill.root.innerHTML||'', note:noteInput?.value||'', ts:Date.now() }; localStorage.setItem(LS_KEY, JSON.stringify(payload)); setAutosaveUI(true); }catch(e){ console.warn('Autosave gagal:', e); } }
  function touchChanged(){ formChanged=true; setAutosaveUI(false); if(autosaveTimer) clearTimeout(autosaveTimer); autosaveTimer=setTimeout(doAutosave, 1200); }
  try{ const raw=localStorage.getItem(LS_KEY); if(raw){ const saved=JSON.parse(raw); if(saved?.title && titleInput && !titleInput.value) titleInput.value=saved.title; if(saved?.content) quill.root.innerHTML=saved.content; if(saved?.note && noteInput && !noteInput.value) noteInput.value=saved.note; setAutosaveUI(true); updateStats(); } else { setAutosaveUI(false); } }catch{ setAutosaveUI(false); }
  quill.on('text-change', (_, __, src)=>{ if(src==='user'){ updateStats(); touchChanged(); } });
  form?.addEventListener('input', touchChanged);
  /* ================= AUTOSAVE: END ================================ */

  /* ================= SUBMIT/PUBLISH: START ======================== */
  publishBtn?.addEventListener('click', ()=>{ if(statusInput) statusInput.value='published'; });
  form?.addEventListener('submit', (e)=>{ const minChars=20; if(quill.getText().trim().length < minChars){ e.preventDefault(); alert(`Isi bab terlalu pendek. Minimal ${minChars} karakter.`); return; } if(statusInput) statusInput.value='published'; contentInput.value = quill.root.innerHTML; formChanged=false; try{ localStorage.removeItem(LS_KEY); }catch{} });
  document.addEventListener('keydown', (e)=>{ const isMac=/Mac/i.test(navigator.platform); const mod=isMac ? e.metaKey : e.ctrlKey; if(mod && e.key==='Enter'){ e.preventDefault(); if(statusInput) statusInput.value='published'; contentInput.value=quill.root.innerHTML; form?.requestSubmit(); } });
  /* ================= SUBMIT/PUBLISH: END ========================== */

  /* ================= PREVIEW MODAL: START ========================= */
  const previewBtn=document.getElementById('preview-btn');
  const previewModal=document.getElementById('preview-modal');
  const pClose1=document.getElementById('preview-close');
  const pClose2=document.getElementById('preview-close-bottom');
  const pTitle=document.getElementById('preview-title');
  const pBody=document.getElementById('preview-body');
  const pNoteWrap=document.getElementById('preview-author-note');
  const pNoteBody=document.getElementById('preview-note-body');
  function openPreview(){ if(!previewModal) return; pTitle && (pTitle.textContent = titleInput?.value?.trim() || '(Tanpa judul)'); if(pBody){ pBody.innerHTML = quill.root.innerHTML || '<p><em>(Kosong)</em></p>'; pBody.querySelectorAll('img').forEach(img=>{ img.loading='lazy'; img.style.maxWidth='100%'; img.style.height='auto'; img.style.display='block'; img.style.margin='.5rem auto'; img.style.borderRadius='.5rem'; }); } const note = noteInput?.value?.trim(); if(pNoteWrap && pNoteBody){ if(note){ pNoteBody.textContent = note; pNoteWrap.classList.remove('hidden'); } else { pNoteWrap.classList.add('hidden'); } } previewModal.classList.remove('hidden'); previewModal.classList.add('flex'); }
  function closePreview(){ previewModal?.classList.add('hidden'); previewModal?.classList.remove('flex'); }
  previewBtn?.addEventListener('click', openPreview); pClose1?.addEventListener('click', closePreview); pClose2?.addEventListener('click', closePreview); previewModal?.addEventListener('click', (e)=>{ if(e.target===previewModal) closePreview(); });
  /* ================= PREVIEW MODAL: END =========================== */

  /* ================= DISCARD CHANGES MODAL: START ================= */
  const discardModal=document.getElementById('discard-changes-modal');
  const discardConfirm=document.getElementById('discard-confirm-btn');
  const keepEditingBtn=document.getElementById('keep-editing-btn');
  const navLinks=document.querySelectorAll('.nav-link');
  let navigateUrl='#';
  function showDiscard(url){ if(formChanged){ navigateUrl=url; discardModal.classList.remove('hidden'); discardModal.classList.add('flex'); } else window.location.href = url; }
  navLinks.forEach(a=> a.addEventListener('click', (e)=>{ e.preventDefault(); showDiscard(a.href); }));
  discardConfirm?.addEventListener('click', (e)=>{ e.preventDefault(); formChanged=false; window.location.href=navigateUrl; });
  const closeDiscard=()=>{ discardModal.classList.add('hidden'); discardModal.classList.remove('flex'); };
  keepEditingBtn?.addEventListener('click', closeDiscard);
  discardModal?.addEventListener('click', (e)=>{ if(e.target===discardModal) closeDiscard(); });
  /* ================= DISCARD CHANGES MODAL: END =================== */

  /* ================= BEFORE UNLOAD GUARD: START =================== */
  window.onbeforeunload = function(e){ if(formChanged){ e.preventDefault(); e.returnValue=''; return ''; } };
  /* ================= BEFORE UNLOAD GUARD: END ===================== */
});
</script>
@endpush
