@extends('layouts.app')

@section('title', 'Edit Karya - CeritaKita')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<style>
  /* util */
  .custom-scrollbar::-webkit-scrollbar{width:8px}
  .custom-scrollbar::-webkit-scrollbar-track{background:#f1f5f9}
  .custom-scrollbar::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:10px}
  .image-preview{background-size:cover;background-position:center}
  .dropdown-menu{transition:opacity .15s ease,transform .15s ease}
  .dropdown-menu.hidden,.dropdown-menu.opacity-0,.dropdown-menu.scale-95{pointer-events:none}
  /* quill */
  #synopsis-editor{position:relative;z-index:10;min-height:150px;background:#fff;font-size:1rem}
  #synopsis-editor .ql-editor{min-height:180px}
  #synopsis-editor .ql-toolbar{border:1px solid #d1d5db;border-bottom:none;background:#fff!important}
  #synopsis-editor .ql-container.ql-snow{border:1px solid #d1d5db!important;border-top:none!important}
</style>
@endpush

@section('content')
<main class="container mx-auto px-4 lg:px-8 py-12">
  <div class="max-w-6xl mx-auto">
    {{-- ===== START: Page Title ===== --}}
    <h1 class="text-4xl font-bold text-gray-900 mb-8">Edit Karya</h1>
    {{-- ===== END: Page Title ===== --}}

    {{-- ===== START: Form ===== --}}
    <form id="new-story-form" action="{{ route('stories.update', $story) }}" method="POST" class="bg-white rounded-2xl shadow-xl">
      @csrf
      @method('PUT')

      {{-- ===== START: Cover + Core Info ===== --}}
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-6 md:p-10">
        {{-- ~~ START: Cover Pane ~~ --}}
        <div class="lg:col-span-1 space-y-4">
          <h2 class="text-xl font-bold text-gray-800 border-b pb-3">Sampul Cerita</h2>

          {{-- :: START: Cover Preview :: --}}
          <div id="cover-preview"
               class="w-full aspect-[2/3] bg-blue-50 border-4 border-dashed border-blue-300 rounded-xl flex flex-col items-center justify-center text-gray-400 relative image-preview hover:border-blue-500 transition cursor-pointer group"
               style="background-image:url('{{ $story->cover_image_path ? Storage::url($story->cover_image_path) : '' }}'); background-size:cover; background-position:center; {{ $story->cover_image_path ? 'border:0;' : '' }}">
            <span class="absolute top-3 right-3 text-xs font-medium bg-white px-2 py-1 rounded-full text-blue-600 border border-blue-300 shadow-sm">Rasio 2:3</span>

            <div id="cover-placeholder" class="text-center transition-all group-hover:scale-[1.02] p-6 {{ $story->cover_image_path ? 'hidden' : '' }}">
              <i class="fa-solid fa-cloud-arrow-up text-7xl text-blue-400 opacity-80 group-hover:text-blue-600"></i>
              <p class="mt-4 text-lg font-bold text-gray-800">Unggah Sampul Anda</p>
              <p class="text-sm text-gray-500">Rekomendasi: 600x800px</p>
            </div>
          </div>
          {{-- :: END: Cover Preview :: --}}

          <input type="hidden" name="cover_image_data" id="cover_image_data">

          {{-- :: START: Cover Actions :: --}}
          <div class="flex gap-3">
            <label for="cover-upload" class="flex-grow text-center cursor-pointer bg-blue-50 border border-blue-600 text-blue-600 px-4 py-2 text-base font-semibold rounded-full hover:bg-blue-100">
              <i class="fa-solid fa-arrow-up-from-bracket mr-2"></i> Ganti Sampul
              <input id="cover-upload" data-target="cover" type="file" class="hidden image-upload-input" accept="image/*">
            </label>
            <button id="adjust-cover-btn" data-target="cover" type="button" class="adjust-image-btn w-1/3 text-center bg-white border border-gray-300 text-gray-700 px-4 py-2 text-base font-semibold rounded-full hover:bg-gray-50" title="Sesuaikan">
              <i class="fa-solid fa-crop-simple"></i>
            </button>
          </div>
          {{-- :: END: Cover Actions :: --}}
        </div>
        {{-- ~~ END: Cover Pane ~~ --}}

        {{-- ~~ START: Core Info Pane ~~ --}}
        <div class="lg:col-span-2 space-y-8">
          <h2 class="text-xl font-bold text-gray-800 border-b pb-3">Informasi Inti</h2>

          {{-- :: START: Title :: --}}
          <div>
            <label class="text-base font-semibold text-gray-700">Judul Karya <span class="text-red-500">*</span></label>
            <input name="title" type="text" value="{{ old('title', $story->title) }}"
                   class="mt-1 w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" required>
          </div>
          {{-- :: END: Title :: --}}

          {{-- :: START: Synopsis (Quill) :: --}}
          <div>
            <label class="text-base font-semibold text-gray-700">Sinopsis</label>
            <div id="synopsis-editor" class="mt-1"></div>
            <input type="hidden" name="synopsis" id="synopsis-input">
          </div>
          {{-- :: END: Synopsis (Quill) :: --}}
        </div>
        {{-- ~~ END: Core Info Pane ~~ --}}
      </div>
      {{-- ===== END: Cover + Core Info ===== --}}

      {{-- ===== START: Category & Status ===== --}}
      <div class="w-full">
        <div class="p-6 md:p-10 border-t">
          <h2 class="text-2xl font-bold text-gray-800 border-b pb-4 mb-8">Kategori & Status Publikasi</h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- ~~ START: Type Dropdown ~~ --}}
            <div id="type-selection" class="relative dropdown">
              <label class="text-base font-semibold text-gray-700 mb-2 block">Jenis Cerita</label>
              <button type="button" class="dropdown-btn w-full flex items-center justify-between bg-white border border-gray-300 rounded-xl py-3 px-4 hover:border-blue-500" aria-haspopup="listbox" aria-expanded="false">
                <span class="dropdown-label text-base">{{ old('type', $story->type) }}</span>
                <i class="fa-solid fa-chevron-down text-gray-400 ml-4"></i>
              </button>
              <div class="dropdown-menu absolute mt-2 w-full bg-white rounded-xl shadow-xl z-10 hidden opacity-0 scale-95">
                @foreach(['Novel Web','Cerpen','Light Novel','Ebook/Novel Fisik'] as $type)
                  <a href="#" class="dropdown-item block px-4 py-3 hover:bg-gray-100" data-value="{{ $type }}" role="option">{{ $type }}</a>
                @endforeach
              </div>
              <input type="hidden" name="type" id="type-input" value="{{ old('type', $story->type) }}">
            </div>
            {{-- ~~ END: Type Dropdown ~~ --}}

            {{-- ~~ START: Status Dropdown ~~ --}}
            <div id="status-selection" class="relative dropdown">
              <label class="text-base font-semibold text-gray-700 mb-2 block">Status Cerita</label>
              <button type="button" class="dropdown-btn w-full flex items-center justify-between bg-white border border-gray-300 rounded-xl py-3 px-4 hover:border-blue-500" aria-haspopup="listbox" aria-expanded="false">
                <span class="dropdown-label text-base">{{ old('status', $story->status) }}</span>
                <i class="fa-solid fa-chevron-down text-gray-400 ml-4"></i>
              </button>
              <div class="dropdown-menu absolute mt-2 w-full bg-white rounded-xl shadow-xl z-10 hidden opacity-0 scale-95">
                @foreach(['Berlanjut','Tamat','Hiatus','Drop'] as $s)
                  <a href="#" class="dropdown-item block px-4 py-3 hover:bg-gray-100" data-value="{{ $s }}" role="option">{{ $s }}</a>
                @endforeach
              </div>
              <input type="hidden" name="status" id="status-input" value="{{ old('status', $story->status) }}">
            </div>
            {{-- ~~ END: Status Dropdown ~~ --}}

            {{-- ~~ START: Genre Multi-select ~~ --}}
            <div id="genre-selection" class="space-y-2">
              <label class="text-base font-semibold text-gray-700 mb-2 block">Genre (maks. 3)</label>

              {{-- ... Trigger --}}
              <div class="relative dropdown">
                <button type="button" class="dropdown-btn w-full flex items-center justify-between bg-white border border-gray-300 rounded-xl py-3 px-4 hover:border-blue-500" aria-haspopup="listbox" aria-expanded="false">
                  <span class="dropdown-label text-gray-500 text-base">Pilih genre...</span>
                  <i class="fa-solid fa-chevron-down text-gray-400 ml-4"></i>
                </button>

                {{-- ... Menu --}}
                <div class="dropdown-menu absolute mt-2 w-full bg-white rounded-xl shadow-xl z-10 hidden opacity-0 scale-95 p-3">
                  <input id="genre-search" type="text" placeholder="Cari genre..." class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-3 focus:ring-1 focus:ring-blue-500" aria-label="Cari genre">

                  @php
                    $allGenres = ['Fantasi','Romansa','Misteri','Horor','Sci-Fi','Aksi','Drama','Komedi','Thriller','Historical','Slice of Life','Supernatural','Game','Wuxia/Xianxia'];
                    $selectedGenres = old('genres', $story->genres ?? []);
                  @endphp

                  <div class="max-h-60 overflow-y-auto custom-scrollbar space-y-1" role="listbox" aria-multiselectable="true">
                    @foreach($allGenres as $g)
                      <label class="genre-item flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                        <input type="checkbox" name="genres[]" value="{{ $g }}" class="form-checkbox text-blue-600 h-5 w-5" {{ in_array($g, $selectedGenres) ? 'checked' : '' }}>
                        <span class="ml-3 text-base">{{ $g }}</span>
                      </label>
                    @endforeach
                  </div>
                </div>
              </div>

              {{-- ... Selected Tags --}}
              <div id="genre-tags" class="flex flex-wrap gap-2"></div>
            </div>
            {{-- ~~ END: Genre Multi-select ~~ --}}
          </div>
        </div>
      </div>
      {{-- ===== END: Category & Status ===== --}}

      {{-- ===== START: Contributors & Sources ===== --}}
      <div class="w-full">
        <div class="p-6 md:p-10 border-t">
          <h2 class="text-2xl font-bold text-gray-800 border-b pb-4 mb-8">Kontributor & Sumber</h2>

          {{-- ~~ START: Author/Artist ~~ --}}
          <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="text-base font-semibold text-gray-700">Nama Penulis Asli <span class="text-red-500">*</span></label>
                <input name="author_name" type="text" value="{{ old('author_name', $story->author_name) }}" class="mt-1 w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" required>
              </div>
              <div>
                <label class="text-base font-semibold text-gray-700">Artist</label>
                <input name="artist" type="text" value="{{ old('artist', $story->artist) }}" class="mt-1 w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
              </div>
            </div>
          {{-- ~~ END: Author/Artist ~~ --}}

            {{-- ~~ START: Origin/Translator ~~ --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              @php
                $langs = ['Jepang','Cina','Korea Selatan','Bahasa Lainnya'];
                $currentOrigin = old('origin', $story->origin);
                $originDisplay = in_array($currentOrigin, $langs, true) ? $currentOrigin : 'Pilih Bahasa Asli';
                $isPlaceholder = $originDisplay === 'Pilih Bahasa Asli';
              @endphp

              {{-- :: START: Language Dropdown :: --}}
              <div id="language-selection" class="relative dropdown">
                <label class="text-base font-semibold text-gray-700 mb-2 block">Asal Bahasa</label>
                <button type="button" class="dropdown-btn w-full flex items-center justify-between bg-white border border-gray-300 rounded-xl py-3 px-4 hover:border-blue-500" aria-haspopup="listbox" aria-expanded="false">
                  <span class="dropdown-label text-base {{ $isPlaceholder ? 'text-gray-500' : '' }}">{{ $originDisplay }}</span>
                  <i class="fa-solid fa-chevron-down text-gray-400 ml-4"></i>
                </button>
                <div class="dropdown-menu absolute mt-2 w-full bg-white rounded-xl shadow-xl z-10 hidden opacity-0 scale-95">
                  <a href="#" class="dropdown-item flex items-center px-4 py-3 text-base text-gray-700 hover:bg-gray-100 transition" data-value="Jepang" role="option">
                    <i class="fa-solid fa-fan text-red-500 w-5 mr-3"></i><span>Jepang</span>
                  </a>
                  <a href="#" class="dropdown-item flex items-center px-4 py-3 text-base text-gray-700 hover:bg-gray-100 transition" data-value="Cina" role="option">
                    <i class="fa-solid fa-scroll text-yellow-600 w-5 mr-3"></i><span>Cina</span>
                  </a>
                  <a href="#" class="dropdown-item flex items-center px-4 py-3 text-base text-gray-700 hover:bg-gray-100 transition" data-value="Korea Selatan" role="option">
                    <i class="fa-solid fa-yin-yang text-blue-500 w-5 mr-3"></i><span>Korea Selatan</span>
                  </a>
                  <a href="#" class="dropdown-item flex items-center px-4 py-3 text-base text-gray-700 hover:bg-gray-100 transition" data-value="Bahasa Lainnya" role="option">
                    <i class="fa-solid fa-globe text-gray-600 w-5 mr-3"></i><span>Bahasa Lainnya</span>
                  </a>
                </div>
                <input type="hidden" name="origin" id="language-input" value="{{ $currentOrigin }}">
              </div>
              {{-- :: END: Language Dropdown :: --}}

              <div>
                <label class="text-base font-semibold text-gray-700">Translator</label>
                <input name="translator" type="text" value="{{ old('translator', $story->translator) }}" class="mt-1 w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
              </div>
            </div>
            {{-- ~~ END: Origin/Translator ~~ --}}

            <div>
              <label class="text-base font-semibold text-gray-700">Proofreader</label>
              <input name="proofreader" type="text" value="{{ old('proofreader', $story->proofreader) }}" class="mt-1 w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>
          </div>
        </div>
      </div>
      {{-- ===== END: Contributors & Sources ===== --}}

      {{-- ===== START: Footer Actions ===== --}}
      <div class="px-6 md:px-10 pb-6 md:pb-10 flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t">
        <a href="{{ route('workdesk') }}" class="nav-link w-full sm:w-auto text-center bg-gray-200 text-gray-800 font-semibold px-8 py-3 rounded-full hover:bg-gray-300">Batal</a>
        <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white font-semibold px-8 py-3 rounded-full hover:bg-blue-700 shadow-lg shadow-blue-200/50">
          <i class="fa-solid fa-save mr-2"></i> Simpan Perubahan
        </button>
      </div>
      {{-- ===== END: Footer Actions ===== --}}
    </form>
    {{-- ===== END: Form ===== --}}
  </div>
</main>

{{-- ===== START: Cropper Modal ===== --}}
<div id="image-editor-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4 bg-black/50">
  <div class="bg-white rounded-xl shadow-xl w-full max-w-lg">
    <div class="p-4 flex justify-between items-center border-b">
      <h3 id="editor-title" class="text-lg font-bold text-gray-800">Sesuaikan Gambar</h3>
      <button id="close-editor-btn" class="text-2xl text-gray-500 hover:text-gray-800" aria-label="Tutup">&times;</button>
    </div>
    <div class="p-6">
      <div id="image-editor-viewport" class="relative w-full bg-gray-900 overflow-hidden" style="height: 400px;">
        <img id="editable-image" src="" class="block max-w-full" alt="Gambar untuk disesuaikan">
      </div>
    </div>
    <div class="p-4 flex justify-end gap-4 border-t bg-gray-50 rounded-b-xl">
      <button id="cancel-editor-btn" type="button" class="bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-full hover:bg-gray-300">Batal</button>
      <button id="save-editor-btn" type="button" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-full hover:bg-blue-700">Simpan</button>
    </div>
  </div>
</div>
{{-- ===== END: Cropper Modal ===== --}}
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<script>
  window.oldSynopsis = @json(old('synopsis', $story->synopsis ?? ''));
  window.existingCoverUrl = @json($story->cover_image_path ? Storage::url($story->cover_image_path) : null);
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  /* ===== START: Quill (Synopsis) ===== */
  const quill = new Quill('#synopsis-editor', {
    modules:{toolbar:[[{header:false}],['bold','italic','underline','strike'],[{list:'ordered'},{list:'bullet'}],['link'],['clean']]},
    theme:'snow',
    placeholder:'Tuliskan ringkasan menarik tentang ceritamu...'
  });
  const synopsisInput=document.getElementById('synopsis-input');
  quill.root.innerHTML = window.oldSynopsis ?? '';
  synopsisInput.value  = window.oldSynopsis ?? '';
  quill.on('text-change',()=>{ synopsisInput.value = quill.root.innerHTML; });
  /* ===== END: Quill (Synopsis) ===== */

  /* ===== START: Dropdowns (type/status/language) ===== */
  function closeAllDropdowns(exclude=null){
    document.querySelectorAll('.dropdown-menu').forEach(m=>{
      if(m!==exclude){m.classList.add('hidden','opacity-0','scale-95');}
    });
    document.querySelectorAll('.dropdown-btn[aria-expanded="true"]').forEach(b=>b.setAttribute('aria-expanded','false'));
  }
  window.addEventListener('click',()=>closeAllDropdowns());

  document.querySelectorAll('#type-selection,#status-selection,#language-selection').forEach(dd=>{
    const btn=dd.querySelector('.dropdown-btn');
    const menu=dd.querySelector('.dropdown-menu');
    const label=dd.querySelector('.dropdown-label');
    const input=dd.querySelector('input[type="hidden"]');

    if(label && input && input.value){ label.textContent=input.value; label.classList.remove('text-gray-500'); }

    btn.addEventListener('click',e=>{
      e.stopPropagation();
      const openNow = !menu.classList.contains('hidden');
      closeAllDropdowns(menu);
      if(!openNow){
        menu.classList.remove('hidden','opacity-0','scale-95');
        btn.setAttribute('aria-expanded','true');
      }else{
        menu.classList.add('hidden','opacity-0','scale-95');
        btn.setAttribute('aria-expanded','false');
      }
    });

    menu.addEventListener('click',e=>{
      const item=e.target.closest('.dropdown-item');
      if(!item) return;
      e.preventDefault();
      label.textContent=item.dataset.value;
      label.classList.remove('text-gray-500');
      input.value=item.dataset.value;
      closeAllDropdowns();
    });
  });
  /* ===== END: Dropdowns (type/status/language) ===== */

  /* ===== START: Genres (max 3) ===== */
  const genreRoot=document.getElementById('genre-selection');
  if(genreRoot){
    const items=genreRoot.querySelectorAll('.genre-item input[type="checkbox"]');
    const label=genreRoot.querySelector('.dropdown-label');
    const tags=document.getElementById('genre-tags');
    const menu=genreRoot.querySelector('.dropdown-menu');
    const btn=genreRoot.querySelector('.dropdown-btn');
    const search=document.getElementById('genre-search');

    function updateGenre(){
      const checked=[...items].filter(cb=>cb.checked);
      if(!checked.length){ label.textContent='Pilih genre...'; label.classList.add('text-gray-500'); }
      else { label.textContent=`${checked.length} genre dipilih`; label.classList.remove('text-gray-500'); }
      tags.innerHTML='';
      checked.forEach(cb=>{
        const tag=document.createElement('span');
        tag.className='bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-1 rounded-full flex items-center';
        tag.innerHTML=`<span>${cb.value}</span><button type="button" class="ml-2 text-blue-600 hover:text-blue-800" data-value="${cb.value}" aria-label="Hapus genre">&times;</button>`;
        tags.appendChild(tag);
      });
    }
    updateGenre();

    btn.addEventListener('click',e=>{
      e.stopPropagation();
      const openNow = !menu.classList.contains('hidden');
      closeAllDropdowns(menu);
      if(!openNow) menu.classList.remove('hidden','opacity-0','scale-95');
    });

    if(search){
      search.addEventListener('input',()=>{
        const f=(search.value||'').toLowerCase();
        genreRoot.querySelectorAll('.genre-item').forEach(item=>{
          const name=item.querySelector('span').textContent.toLowerCase();
          item.style.display = name.includes(f) ? 'flex':'none';
        });
      });
    }

    items.forEach(cb=>{
      cb.addEventListener('change',()=>{
        const checked=[...items].filter(x=>x.checked);
        if(checked.length>3){ cb.checked=false; alert('Maksimal 3 genre.'); return; }
        updateGenre();
      });
    });

    tags.addEventListener('click',e=>{
      const b=e.target.closest('button'); if(!b) return;
      const val=b.dataset.value;
      const box=[...items].find(c=>c.value===val);
      if(box) box.checked=false;
      updateGenre();
    });
  }
  /* ===== END: Genres (max 3) ===== */

  /* ===== START: Cropper (Cover) ===== */
  const imageEditorModal=document.getElementById('image-editor-modal');
  const editableImage=document.getElementById('editable-image');
  const coverDataInput=document.getElementById('cover_image_data');
  const previewCover=document.getElementById('cover-preview');
  const saveEditorBtn=document.getElementById('save-editor-btn');
  const closeEditorBtn=document.getElementById('close-editor-btn');
  const cancelEditorBtn=document.getElementById('cancel-editor-btn');
  let cropper=null;

  function openImageEditor(url){
    editableImage.src=url;
    imageEditorModal.classList.remove('hidden');
    imageEditorModal.classList.add('flex');
    setTimeout(()=>{
      if(cropper) cropper.destroy();
      cropper=new Cropper(editableImage,{aspectRatio:2/3,viewMode:1,dragMode:'move',background:false,autoCropArea:1});
    },200);
  }

  document.querySelectorAll('.image-upload-input').forEach(inp=>{
    inp.addEventListener('change',e=>{
      const file=e.target.files?.[0];
      if(file){
        const reader=new FileReader();
        reader.onload=ev=>openImageEditor(ev.target.result);
        reader.readAsDataURL(file);
      }
      e.target.value='';
    });
  });

  document.getElementById('adjust-cover-btn').addEventListener('click',()=>{
    if(coverDataInput.value) return openImageEditor(coverDataInput.value);
    if(window.existingCoverUrl) return openImageEditor(window.existingCoverUrl);
    alert('Silakan unggah gambar terlebih dahulu.');
  });

  saveEditorBtn.addEventListener('click',()=>{
    if(!cropper) return;
    const canvas=cropper.getCroppedCanvas({width:600,height:900});
    if(canvas){
      const data=canvas.toDataURL('image/jpeg',0.9);
      previewCover.style.backgroundImage=`url('${data}')`;
      previewCover.style.backgroundSize='cover';
      previewCover.style.backgroundPosition='center';
      document.getElementById('cover-placeholder')?.classList.add('hidden');
      previewCover.classList.remove('border-dashed','border-4');
      previewCover.style.border='0';
      coverDataInput.value=data;
    }
    cropper.destroy(); cropper=null;
    imageEditorModal.classList.add('hidden'); imageEditorModal.classList.remove('flex');
  });

  [closeEditorBtn,cancelEditorBtn].forEach(btn=>btn.addEventListener('click',()=>{
    imageEditorModal.classList.add('hidden'); imageEditorModal.classList.remove('flex');
    if(cropper){ cropper.destroy(); cropper=null; }
  }));
  /* ===== END: Cropper (Cover) ===== */
});
</script>
@endpush
