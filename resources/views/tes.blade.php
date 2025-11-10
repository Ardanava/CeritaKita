@extends('layouts.app')

@section('title', 'Edit Karya - CeritaKita')

@push('styles')
    <!-- Muat CSS Quill.js -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <!-- Muat CSS Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        #image-editor-viewport { height: 400px; }
        .image-preview { background-size: cover; background-position: center; }
        .dropdown-menu { transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out; }
        
        /* Style untuk Editor Sinopsis Quill */
        #synopsis-editor {
            font-family: 'Lora', serif;
            font-size: 1.125rem; 
            height: 250px;
        }
        #synopsis-editor .ql-toolbar { border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; }
        #synopsis-editor .ql-container.ql-snow { border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; }
    </style>
@endpush

@section('content')
<main class="container mx-auto px-4 lg:px-8 py-12">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">Edit Karya</h1>

        <!-- Form action dan method -->
        <form id="new-story-form" action="{{ route('stories.update', $story) }}" method="POST" class="bg-white rounded-2xl shadow-lg">
            @csrf
            @method('PUT') 

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-6 md:p-10">
                <!-- Kolom Kiri: Cover -->
                <div class="lg:col-span-1">
                    <label class="text-base font-semibold text-gray-700 mb-2 block">Sampul Cerita</label>
                    
                    <!-- Tampilkan cover yang ada -->
                    <div id="cover-preview" class="w-full aspect-[2/3] bg-gray-100 border-gray-300 rounded-lg flex flex-col items-center justify-center text-gray-500 relative image-preview" style="background-image: url('{{ $story->cover_image_path ? Storage::url($story->cover_image_path) : 'none' }}'); {{ $story->cover_image_path ? 'border:0;' : 'border-2 border-dashed;' }}">
                        
                        <div id="cover-placeholder" class="{{ $story->cover_image_path ? 'hidden' : 'flex flex-col items-center justify-center' }}">
                            <i class="fa-solid fa-image text-5xl"></i>
                            <p class="mt-3 text-base font-semibold">Klik untuk mengunggah</p>
                            <p class="text-sm">Rekomendasi: 600x800px</p>
                        </div>
                    </div>
                    <input type="hidden" name="cover_image_data" id="cover_image_data">
                    
                    <div class="mt-4 flex gap-3">
                        <label for="cover-upload" class="flex-grow text-center cursor-pointer bg-white border border-gray-300 text-gray-700 px-4 py-2 text-base font-semibold rounded-full hover:bg-gray-50 transition">
                            Ganti
                            <input id="cover-upload" data-target="cover" type="file" class="hidden image-upload-input" accept="image/*">
                        </label>
                        <button id="adjust-cover-btn" data-target="cover" type="button" class="adjust-image-btn flex-grow text-center bg-white border border-gray-300 text-gray-700 px-4 py-2 text-base font-semibold rounded-full hover:bg-gray-50 transition">
                            Sesuaikan
                        </button>
                    </div>
                </div>

                <!-- Kolom Kanan: Detail -->
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <label for="title" class="text-base font-semibold text-gray-700">Judul Karya</label>
                        <input id="title" name="title" type="text" placeholder="Contoh: Pewaris Naga Terakhir" class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-base" required value="{{ old('title', $story->title) }}">
                    </div>
                    
                    <div>
                        <label class="text-base font-semibold text-gray-700">Sinopsis</label>
                        <div id="synopsis-editor" class="mt-1">
                        </div>
                        <input type="hidden" name="synopsis" id="synopsis-input">
                    </div>

                    <div>
                        <div id="original-author-container" class="mt-3">
                            <label for="original-author" class="text-base font-semibold text-gray-700">Nama Penulis</label>
                            <input id="original-author" name="author_name" type="text" placeholder="Masukkan nama penulis asli" class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-base" required value="{{ old('author_name', $story->author_name) }}">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section: Detail Cerita (Genre, etc) -->
            <div class="p-6 md:p-10 border-t">
                <h2 class="text-2xl font-bold text-gray-800 border-b pb-4 mb-8">Detail Cerita</h2>
                <div class="space-y-8">
                    <!-- Genre -->
                    <div id="genre-selection" class="space-y-4">
                        <label class="text-base font-semibold text-gray-700 mb-2 block">Genre (Pilih maks. 3)</label>
                        <div class="relative dropdown">
                            <button type="button" class="dropdown-btn w-full flex items-center justify-between text-left bg-white border border-gray-300 rounded-lg py-3 px-4">
                                <span class="dropdown-label text-gray-500 text-base">Pilih genre...</span>
                                <i class="fa-solid fa-chevron-down text-gray-400 text-xs ml-4"></i>
                            </button>
                            <div class="dropdown-menu absolute mt-2 w-full bg-white rounded-lg shadow-xl z-10 hidden opacity-0 transform scale-95 p-2">
                                <input type="text" id="genre-search" placeholder="Cari genre..." class="w-full border-b px-2 py-1 mb-2 focus:outline-none text-base">
                                <div class="max-h-40 overflow-y-auto space-y-1 custom-scrollbar">
                                    @foreach(['Fantasi', 'Romansa', 'Misteri', 'Horor', 'Sci-Fi', 'Aksi'] as $genre)
                                    <label class="genre-item flex items-center p-2 rounded-lg hover:bg-gray-50">
                                        <input type="checkbox" name="genres[]" value="{{ $genre }}" 
                                               class="form-checkbox text-blue-600 h-5 w-5"
                                               {{ in_array($genre, old('genres', $story->genres ?? [])) ? 'checked' : '' }}>
                                        <span class="ml-3 text-base">{{ $genre }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div id="genre-tags" class="flex flex-wrap gap-2"></div>
                    </div>
                    
                    <!-- Jenis & Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="text-base font-semibold text-gray-700 mb-2 block">Jenis Cerita</label>
                            <div id="type-selection" class="relative dropdown">
                                <button type="button" class="dropdown-btn w-full flex items-center justify-between text-left bg-white border border-gray-300 rounded-lg py-3 px-4">
                                    <span class="dropdown-label text-base">{{ old('type', $story->type) }}</span>
                                    <i class="fa-solid fa-chevron-down text-gray-400 text-xs ml-4"></i>
                                </button>
                                <div class="dropdown-menu absolute mt-2 w-full bg-white rounded-lg shadow-xl z-10 hidden opacity-0 transform scale-95">
                                    <a href="#" class="dropdown-item block px-4 py-2 text-base text-gray-700 hover:bg-gray-100" data-value="Novel Web">Novel Web</a>
                                    <a href="#" class="dropdown-item block px-4 py-2 text-base text-gray-700 hover:bg-gray-100" data-value="Cerpen">Cerpen</a>
                                </div>
                                <input type="hidden" name="type" id="type-input" value="{{ old('type', $story->type) }}">
                            </div>
                        </div>
                        <div>
                            <label class="text-base font-semibold text-gray-700 mb-2 block">Status Cerita</label>
                            <div id="status-selection" class="relative dropdown">
                                <button type="button" class="dropdown-btn w-full flex items-center justify-between text-left bg-white border border-gray-300 rounded-lg py-3 px-4">
                                    <span class="dropdown-label text-base">{{ old('status', $story->status) }}</span>
                                    <i class="fa-solid fa-chevron-down text-gray-400 text-xs ml-4"></i>
                                </button>
                                <div class="dropdown-menu absolute mt-2 w-full bg-white rounded-lg shadow-xl z-10 hidden opacity-0 transform scale-95">
                                    <a href="#" class="dropdown-item block px-4 py-2 text-base text-gray-700 hover:bg-gray-100" data-value="Berlanjut">Berlanjut</a>
                                    <a href="#" class="dropdown-item block px-4 py-2 text-base text-gray-700 hover:bg-gray-100" data-value="Tamat">Tamat</a>
                                </div>
                                <input type="hidden" name="status" id="status-input" value="{{ old('status', $story->status) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tombol Aksi -->
            <div class="px-6 md:px-8 pb-6 md:pb-8 flex flex-col sm:flex-row justify-end gap-4 pt-4 border-t">
                <a href="{{ route('workdesk') }}" class="nav-link w-full sm:w-auto text-center bg-gray-200 text-gray-800 font-semibold px-8 py-3 rounded-full hover:bg-gray-300 transition-colors">Batal</a>
                <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white font-semibold px-8 py-3 rounded-full hover:bg-blue-700 transition-colors">Simpan Perubahan</button>
            </div>

        </form>
    </div>
</main>

<!-- Modal Cropper -->
<div id="image-editor-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg">
        <div class="p-4 flex justify-between items-center border-b">
            <h3 id="editor-title" class="text-lg font-bold text-gray-800">Sesuaikan Gambar</h3>
            <button id="close-editor-btn" class="text-2xl text-gray-500 hover:text-gray-800">&times;</button>
        </div>
        <div class="p-6"><div id="image-editor-viewport" class="relative w-full bg-gray-900 overflow-hidden"><img id="editable-image" src="" class="block max-w-full"></div></div>
        <div class="p-4 flex justify-end gap-4 border-t bg-gray-50 rounded-b-lg">
            <button id="cancel-editor-btn" type="button" class="bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-full hover:bg-gray-300">Batal</button>
            <button id="save-editor-btn" type="button" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-full hover:bg-blue-700">Simpan</button>
        </div>
    </div>
</div>

<!-- Modal Discard Changes -->
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
@endsection

@push('scripts')
<!-- Muat JS Quill.js -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<!-- Muat JS Cropper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<script>
    window.oldSynopsis = @json(old('synopsis', $story->synopsis));
    window.existingCoverUrl = @json($story->cover_image_path ? Storage::url($story->cover_image_path) : null);
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- Logika Modal Umum ---
    const allModals = document.querySelectorAll('#image-editor-modal, #discard-changes-modal');
    const closeModalTriggers = document.querySelectorAll('#close-editor-btn, #cancel-editor-btn, #keep-editing-btn');
    
    function openModal(modal) { if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); } }
    function closeAllModals() { allModals.forEach(modal => { modal.classList.add('hidden'); modal.classList.remove('flex'); }); }

    closeModalTriggers.forEach(btn => btn.addEventListener('click', closeAllModals));
    allModals.forEach(modal => modal.addEventListener('click', (e) => { if (e.target === modal) closeAllModals(); }));
    
    // --- Logika Discard Changes ---
    const form = document.getElementById('new-story-form');
    const discardChangesModal = document.getElementById('discard-changes-modal');
    const discardConfirmBtn = document.getElementById('discard-confirm-btn');
    let formChanged = false; 
    let navigateUrl = '#';

    if(form) {
        form.addEventListener('input', () => { formChanged = true; });
        form.addEventListener('submit', () => { formChanged = false; });
    }
    
    function showDiscardConfirmation(url) {
        if (formChanged) {
            discardConfirmBtn.href = url;
            openModal(discardChangesModal);
        } else {
            window.location.href = url;
        }
    }
    
    const cancelLink = document.querySelector('a[href="{{ route('workdesk') }}"]');
    if (cancelLink) {
         cancelLink.addEventListener('click', function(e) {
             e.preventDefault();
             showDiscardConfirmation(this.href);
         });
    }
    document.querySelectorAll('header a:not(.no-warn)').forEach(link => {
        if (link.getAttribute('href') && link.getAttribute('href') !== '#' && !link.closest('#discard-confirm-btn')) {
            link.addEventListener('click', function (e) {
                if(e.currentTarget.href !== window.location.href) {
                    e.preventDefault();
                    showDiscardConfirmation(this.href);
                }
            });
        }
    });


    var toolbarOptions = [
        [{ 'header': [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['link'],
        ['clean']
    ];
    var quill = new Quill('#synopsis-editor', {
        modules: { toolbar: toolbarOptions },
        theme: 'snow',
        placeholder: 'Tuliskan ringkasan menarik tentang ceritamu...'
    });

    var synopsisInput = document.getElementById('synopsis-input');

    if (window.oldSynopsis) {
        quill.root.innerHTML = window.oldSynopsis;
        synopsisInput.value = window.oldSynopsis; 
    }

    quill.on('text-change', function() {
        synopsisInput.value = quill.root.innerHTML;
        formChanged = true;
    });

    form.addEventListener('submit', function(e) {
        var content = quill.root.innerHTML;
        if (quill.getLength() <= 1 || content === '<p><br></p>') {
            synopsisInput.value = '';
        } else {
            synopsisInput.value = content;
        }
    });

    const imageEditorModal = document.getElementById('image-editor-modal');
    const editorTitle = document.getElementById('editor-title');
    const viewport = document.getElementById('image-editor-viewport');
    const editableImage = document.getElementById('editable-image');
    const saveEditorBtn = document.getElementById('save-editor-btn');
    const coverDataInput = document.getElementById('cover_image_data');

    let cropper = null;
    let editorState = { target: null, previewElement: null };

    function openImageEditor(target, imageUrl) {
        editorState.target = target;
        editorState.previewElement = document.getElementById(`${target}-preview`);
        let aspectRatio = 2 / 3;
        editorTitle.textContent = "Sesuaikan Gambar";
        editableImage.src = imageUrl;
        openModal(imageEditorModal);
        
        if (cropper) cropper.destroy();
        cropper = new Cropper(editableImage, {
            aspectRatio: aspectRatio, viewMode: 1, dragMode: 'move', background: false, autoCropArea: 1,
        });
    }
    
    function applyChanges() {
        if (!cropper) return;
        const canvas = cropper.getCroppedCanvas({ width: 600, height: 900 });
        if(canvas){
            const dataUrl = canvas.toDataURL('image/jpeg', 0.9); 
            editorState.previewElement.style.backgroundImage = `url('${dataUrl}')`;
            editorState.previewElement.classList.remove('border-dashed', 'border-2');
            const placeholder = editorState.previewElement.querySelector('#cover-placeholder');
            if (placeholder) placeholder.classList.add('hidden'); 
            
            if (editorState.target === 'cover') {
                coverDataInput.value = dataUrl;
            }
            
            formChanged = true;
        }
        closeAllModals();
    }

    document.querySelectorAll('.image-upload-input').forEach(input => {
        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => openImageEditor(e.target.dataset.target, event.target.result);
                reader.readAsDataURL(file);
            }
            e.target.value = ''; 
        });
    });
    
    document.querySelectorAll('.adjust-image-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const target = e.currentTarget.dataset.target;
            
            const currentDataUrl = coverDataInput.value;
            const existingUrl = window.existingCoverUrl;
            
            if (currentDataUrl) {
                openImageEditor(target, currentDataUrl);
            } else if (existingUrl) {
                openImageEditor(target, existingUrl);
            } else {
                alert("Silakan unggah gambar terlebih dahulu.");
            }
        });
    });

    if(saveEditorBtn) saveEditorBtn.addEventListener('click', applyChanges);

    const genreDropdown = document.getElementById('genre-selection');
    if (genreDropdown) {
        const genreDropdownBtn = genreDropdown.querySelector('.dropdown-btn');
        const genreDropdownMenu = genreDropdown.querySelector('.dropdown-menu');
        const genreSearch = genreDropdown.querySelector('#genre-search');
        const genreItems = genreDropdown.querySelectorAll('.genre-item');
        const genreTagsContainer = document.getElementById('genre-tags');
        const genreLabel = genreDropdown.querySelector('.dropdown-label');
        
        function updateGenreTags(selected) {
            genreTagsContainer.innerHTML = '';
            if (selected.length === 0) {
                genreLabel.textContent = 'Pilih genre...';
                genreLabel.classList.add('text-gray-500');
            } else {
                genreLabel.textContent = `${selected.length} genre dipilih`;
                genreLabel.classList.remove('text-gray-500');
            }
            
            selected.forEach(cb => {
                const tag = document.createElement('span');
                tag.className = 'bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-1 rounded-full flex items-center';
                tag.innerHTML = `<span>${cb.value}</span><button type="button" class="ml-2 text-blue-600 hover:text-blue-800" data-value="${cb.value}">&times;</button>`;
                genreTagsContainer.appendChild(tag);
            });
        }
        
        const initialSelectedGenres = Array.from(genreItems).map(i => i.querySelector('input[type="checkbox"]')).filter(cb => cb.checked);
        updateGenreTags(initialSelectedGenres);

        genreDropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            closeAllDropdowns(genreDropdownMenu);
            genreDropdownMenu.classList.toggle('hidden');
            setTimeout(() => genreDropdownMenu.classList.toggle('opacity-0'), 10);
            setTimeout(() => genreDropdownMenu.classList.toggle('scale-95'), 10);
        });

        genreSearch.addEventListener('input', () => {
            const filter = genreSearch.value.toLowerCase();
            genreItems.forEach(item => {
                const label = item.querySelector('span').textContent.toLowerCase();
                item.style.display = label.includes(filter) ? 'flex' : 'none';
            });
        });

        genreItems.forEach(item => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            checkbox.addEventListener('change', () => {
                const selectedGenres = Array.from(genreItems).map(i => i.querySelector('input[type="checkbox"]')).filter(cb => cb.checked);
                if (selectedGenres.length > 3) {
                    checkbox.checked = false;
                    alert('Anda hanya dapat memilih maksimal 3 genre.');
                    return;
                }
                updateGenreTags(selectedGenres);
                formChanged = true;
            });
        });
        
        genreTagsContainer.addEventListener('click', (e) => {
            if(e.target.tagName === 'BUTTON') {
                const value = e.target.dataset.value;
                const checkbox = genreDropdown.querySelector(`input[value="${value}"]`);
                if(checkbox) checkbox.checked = false;
                updateGenreTags(Array.from(genreItems).map(i => i.querySelector('input[type="checkbox"]')).filter(cb => cb.checked));
                formChanged = true;
            }
        });
    }
    
    document.querySelectorAll('#type-selection, #status-selection').forEach(dropdown => {
        const button = dropdown.querySelector('.dropdown-btn');
        const menu = dropdown.querySelector('.dropdown-menu');
        const label = dropdown.querySelector('.dropdown-label');
        const input = dropdown.querySelector('input[type="hidden"]');

        button.addEventListener('click', (e) => {
            e.stopPropagation();
            closeAllDropdowns(menu);
            menu.classList.toggle('hidden');
            setTimeout(() => menu.classList.toggle('opacity-0'), 10);
            setTimeout(() => menu.classList.toggle('scale-95'), 10);
        });

        menu.addEventListener('click', (e) => {
            e.preventDefault();
            if(e.target.classList.contains('dropdown-item')) {
                const value = e.target.dataset.value;
                label.textContent = value;
                input.value = value;
                menu.classList.add('hidden', 'opacity-0', 'scale-95');
                formChanged = true;
            }
        });
    });
        
    function closeAllDropdowns(excludeMenu = null) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu !== excludeMenu) {
                menu.classList.add('hidden', 'opacity-0', 'scale-95');
            }
        });
    }

    window.addEventListener('click', (e) => {
        closeAllDropdowns();
    });
    
});
</script>
@endpush