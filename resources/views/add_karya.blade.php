@extends('layouts.app')

@section('title', 'Cerita Kami - Tambah Cerita' )

@push('styles')
<!-- =============== GLOBAL ASSETS (Tailwind, FA, Quill, Cropper): START =============== -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<!-- =============== GLOBAL ASSETS: END =============== -->

<style>
    /* ======= FIX: Editor tidak bisa diklik karena ketutup overlay ======= */
    /* Sembunyikan opsi picker Quill saat tidak expanded agar tidak intercept klik */
    .ql-toolbar .ql-picker:not(.ql-expanded) .ql-picker-options { display: none !important; }
    .ql-toolbar .ql-picker.ql-expanded .ql-picker-options { display: block; z-index: 50; }
    /* Dropdown custom saat hidden/opacity/scale: hentikan penangkapan klik */
    .dropdown-menu.hidden,
    .dropdown-menu.opacity-0,
    .dropdown-menu.scale-95 { pointer-events: none; }
    /* Pastikan editor berada di atas elemen lain yang mungkin absolute */
    #synopsis-editor { position: relative; z-index: 10; }

    /* Styling untuk Quill.js */
    #synopsis-editor { min-height: 150px; max-height: 300px; overflow-y: auto; background-color: white; font-size: 1rem; }
    #synopsis-editor .ql-editor { min-height: 180px; }
    .ql-toolbar { border-top-left-radius: 0 !important; border-top-right-radius: 0 !important; background-color: white !important; border: 1px solid #d1d5db; border-bottom: none; }
    .ql-container { border-bottom-left-radius: 0 !important; border-bottom-right-radius: 0 !important; border: 1px solid #d1d5db !important; border-top: none !important; }

    /* Scrollbar dropdown genre */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #d1d5db; border-radius: 3px; }
    .dropdown-menu { transition: all 0.2s ease-in-out; transform-origin: top; }
</style>
@endpush

@section('content')
{{-- =========================== FORM WRAPPER: START =========================== --}}
<form id="new-story-form" action="{{ route('stories.store') }}" method="POST" class="bg-white rounded-2xl shadow-xl max-w-6xl mx-auto my-8">
    @csrf

    {{-- ==================== BAGIAN 1: COVER & INFORMASI INTI: START ==================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-6 md:p-10">
        {{-- --- KOLOM KIRI (Sampul): START --- --}}
        <div class="lg:col-span-1 space-y-4">
            <h2 class="text-xl font-bold text-gray-800 border-b pb-3">Sampul Cerita</h2>

            {{-- Dropzone/Preview Sampul: START --}}
            <div id="cover-preview" class="w-full aspect-[2/3] bg-blue-50 border-4 border-dashed border-blue-300 rounded-xl flex flex-col items-center justify-center text-gray-400 relative image-preview hover:border-blue-500 transition cursor-pointer group" style="background-size: cover; background-position: center; background-repeat: no-repeat;">
                <span class="absolute top-3 right-3 text-xs font-medium bg-white px-2 py-1 rounded-full text-blue-600 border border-blue-300 shadow-sm">Rasio 2:3</span>
                <div id="cover-placeholder" class="text-center transition-all group-hover:scale-[1.02] p-6">
                    <i class="fa-solid fa-cloud-arrow-up text-7xl text-blue-400 opacity-80 group-hover:text-blue-600 transition"></i>
                    <p class="mt-4 text-lg font-bold text-gray-800">Unggah Sampul Anda</p>
                    <p class="text-sm text-gray-500">Rekomendasi: 600x800px</p>
                </div>
            </div>
            {{-- Dropzone/Preview Sampul: END --}}

            <input type="hidden" name="cover_image_data" id="cover_image_data">

            {{-- Tombol Upload + Sesuaikan: START --}}
            <div class="flex gap-3">
                <label for="cover-upload" class="flex-grow text-center cursor-pointer bg-blue-50 border border-blue-600 text-blue-600 px-4 py-2 text-base font-semibold rounded-full hover:bg-blue-100 transition">
                    <i class="fa-solid fa-arrow-up-from-bracket mr-2"></i> Ganti Sampul
                    <input id="cover-upload" data-target="cover" type="file" class="hidden image-upload-input" accept="image/*">
                </label>
                <button id="adjust-cover-btn" data-target="cover" type="button" class="adjust-image-btn w-1/3 text-center bg-white border border-gray-300 text-gray-700 px-4 py-2 text-base font-semibold rounded-full hover:bg-gray-50 transition">
                    <i class="fa-solid fa-crop-simple"></i>
                </button>
            </div>
            {{-- Tombol Upload + Sesuaikan: END --}}
        </div>
        {{-- --- KOLOM KIRI (Sampul): END --- --}}

        {{-- --- KOLOM KANAN (Judul & Sinopsis): START --- --}}
        <div class="lg:col-span-2 space-y-8">
            <h2 class="text-xl font-bold text-gray-800 border-b pb-3">Informasi Inti</h2>

            {{-- Field Judul: START --}}
            <div>
                <label for="title" class="text-base font-semibold text-gray-700">Judul Karya <span class="text-red-500">*</span></label>
                <input id="title" name="title" type="text" placeholder="Contoh: Pewaris Naga Terakhir" class="mt-1 w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 text-base" required>
            </div>
            {{-- Field Judul: END --}}

            {{-- Field Sinopsis (Quill): START --}}
            <div>
                <label class="text-base font-semibold text-gray-700">Sinopsis</label>
                <div id="synopsis-editor" class="mt-1"></div>
                <input type="hidden" name="synopsis" id="synopsis-input">
            </div>
            {{-- Field Sinopsis: END --}}
        </div>
        {{-- --- KOLOM KANAN (Judul & Sinopsis): END --- --}}
    </div>
    {{-- ==================== BAGIAN 1: END ==================== --}}

    {{-- ==================== BAGIAN 2: KATEGORI & STATUS: START ==================== --}}
    <div class="w-full">
        <div class="p-6 md:p-10 border-t border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800 border-b pb-4 mb-8">Kategori & Status Publikasi</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                {{-- Select Jenis Cerita: START --}}
                <div>
                    <label class="text-base font-semibold text-gray-700 mb-2 block">Jenis Cerita</label>
                    <div id="type-selection" class="relative dropdown">
                        <button type="button" class="dropdown-btn w-full flex items-center justify-between text-left bg-white border border-gray-300 rounded-xl py-3 px-4 hover:border-blue-500 transition">
                            <span class="dropdown-label text-base">Novel Web</span>
                            <i class="fa-solid fa-chevron-down text-gray-400 text-xs ml-4"></i>
                        </button>
                        <div class="dropdown-menu absolute mt-2 w-full bg-white rounded-xl shadow-xl z-10 hidden opacity-0 transform scale-95">
                            <a href="#" class="dropdown-item block px-4 py-3 text-base text-gray-700 hover:bg-gray-100 transition" data-value="Novel Web">Novel Web</a>
                            <a href="#" class="dropdown-item block px-4 py-3 text-base text-gray-700 hover:bg-gray-100 transition" data-value="Cerpen">Cerpen</a>
                            <a href="#" class="dropdown-item block px-4 py-3 text-base text-gray-700 hover:bg-gray-100 transition" data-value="Light Novel">Light Novel</a>
                            <a href="#" class="dropdown-item block px-4 py-3 text-base text-gray-700 hover:bg-gray-100 transition" data-value="Ebook/Novel Fisik">Ebook/Novel Fisik</a>
                        </div>
                        <input type="hidden" name="type" id="type-input" value="Novel Web">
                    </div>
                </div>
                {{-- Select Jenis Cerita: END --}}

                {{-- Select Status Cerita: START --}}
                <div>
                    <label class="text-base font-semibold text-gray-700 mb-2 block">Status Cerita</label>
                    <div id="status-selection" class="relative dropdown">
                        <button type="button" class="dropdown-btn w-full flex items-center justify-between text-left bg-white border border-gray-300 rounded-xl py-3 px-4 hover:border-blue-500 transition">
                            <span class="dropdown-label text-base">Berlanjut</span>
                            <i class="fa-solid fa-chevron-down text-gray-400 text-xs ml-4"></i>
                        </button>
                        <div class="dropdown-menu absolute mt-2 w-full bg-white rounded-xl shadow-xl z-10 hidden opacity-0 transform scale-95">
                            <a href="#" class="dropdown-item block px-4 py-3 text-base text-gray-700 hover:bg-gray-100 transition" data-value="Berlanjut">Berlanjut</a>
                            <a href="#" class="dropdown-item block px-4 py-3 text-base text-gray-700 hover:bg-gray-100 transition" data-value="Tamat">Tamat</a>
                            <a href="#" class="dropdown-item block px-4 py-3 text-base text-gray-700 hover:bg-gray-100 transition" data-value="Hiatus">Hiatus</a>
                            <a href="#" class="dropdown-item block px-4 py-3 text-base text-gray-700 hover:bg-gray-100 transition" data-value="Drop">Drop</a>
                        </div>
                        <input type="hidden" name="status" id="status-input" value="Berlanjut">
                    </div>
                </div>
                {{-- Select Status Cerita: END --}}

                {{-- Select Genre Multi (max 3): START --}}
                <div id="genre-selection" class="space-y-2">
                    <label class="text-base font-semibold text-gray-700 mb-2 block">Genre (Pilih maks. 3)</label>
                    <div class="relative dropdown">
                        <button type="button" class="dropdown-btn w-full flex items-center justify-between text-left bg-white border border-gray-300 rounded-xl py-3 px-4 hover:border-blue-500 transition">
                            <span class="dropdown-label text-gray-500 text-base">Pilih genre...</span>
                            <i class="fa-solid fa-chevron-down text-gray-400 text-xs ml-4"></i>
                        </button>
                        <div class="dropdown-menu absolute mt-2 w-full bg-white rounded-xl shadow-xl z-10 hidden opacity-0 transform scale-95 p-3">
                            <input type="text" id="genre-search" placeholder="Cari genre..." class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-3 focus:outline-none focus:ring-1 focus:ring-blue-500 text-base">
                            <div class="max-h-60 overflow-y-auto space-y-1 custom-scrollbar">
                                <!-- Daftar genre: START -->
                                <label class="genre-item flex items-center p-2 rounded-lg hover:bg-gray-50 transition cursor-pointer"><input type="checkbox" name="genres[]" value="Fantasi" class="form-checkbox text-blue-600 h-5 w-5 rounded"><span class="ml-3 text-base">Fantasi</span></label>
                                <label class="genre-item flex items-center p-2 rounded-lg hover:bg-gray-50 transition cursor-pointer"><input type="checkbox" name="genres[]" value="Romansa" class="form-checkbox text-blue-600 h-5 w-5 rounded"><span class="ml-3 text-base">Romansa</span></label>
                                <label class="genre-item flex items-center p-2 rounded-lg hover:bg-gray-50 transition cursor-pointer"><input type="checkbox" name="genres[]" value="Misteri" class="form-checkbox text-blue-600 h-5 w-5 rounded"><span class="ml-3 text-base">Misteri</span></label>
                                <label class="genre-item flex items-center p-2 rounded-lg hover:bg-gray-50 transition cursor-pointer"><input type="checkbox" name="genres[]" value="Horor" class="form-checkbox text-blue-600 h-5 w-5 rounded"><span class="ml-3 text-base">Horor</span></label>
                                <label class="genre-item flex items-center p-2 rounded-lg hover:bg-gray-50 transition cursor-pointer"><input type="checkbox" name="genres[]" value="Sci-Fi" class="form-checkbox text-blue-600 h-5 w-5 rounded"><span class="ml-3 text-base">Sci-Fi</span></label>
                                <label class="genre-item flex items-center p-2 rounded-lg hover:bg-gray-50 transition cursor-pointer"><input type="checkbox" name="genres[]" value="Aksi" class="form-checkbox text-blue-600 h-5 w-5 rounded"><span class="ml-3 text-base">Aksi</span></label>
                                <label class="genre-item flex items-center p-2 rounded-lg hover:bg-gray-50 transition cursor-pointer"><input type="checkbox" name="genres[]" value="Drama" class="form-checkbox text-blue-600 h-5 w-5 rounded"><span class="ml-3 text-base">Drama</span></label>
                                <label class="genre-item flex items-center p-2 rounded-lg hover:bg-gray-50 transition cursor-pointer"><input type="checkbox" name="genres[]" value="Komedi" class="form-checkbox text-blue-600 h-5 w-5 rounded"><span class="ml-3 text-base">Komedi</span></label>
                                <label class="genre-item flex items-center p-2 rounded-lg hover:bg-gray-50 transition cursor-pointer"><input type="checkbox" name="genres[]" value="Thriller" class="form-checkbox text-blue-600 h-5 w-5 rounded"><span class="ml-3 text-base">Thriller</span></label>
                                <label class="genre-item flex items-center p-2 rounded-lg hover:bg-gray-50 transition cursor-pointer"><input type="checkbox" name="genres[]" value="Historical" class="form-checkbox text-blue-600 h-5 w-5 rounded"><span class="ml-3 text-base">Historical</span></label>
                                <label class="genre-item flex items-center p-2 rounded-lg hover:bg-gray-50 transition cursor-pointer"><input type="checkbox" name="genres[]" value="Slice of Life" class="form-checkbox text-blue-600 h-5 w-5 rounded"><span class="ml-3 text-base">Slice of Life</span></label>
                                <label class="genre-item flex items-center p-2 rounded-lg hover:bg-gray-50 transition cursor-pointer"><input type="checkbox" name="genres[]" value="Supernatural" class="form-checkbox text-blue-600 h-5 w-5 rounded"><span class="ml-3 text-base">Supernatural</span></label>
                                <label class="genre-item flex items-center p-2 rounded-lg hover:bg-gray-50 transition cursor-pointer"><input type="checkbox" name="genres[]" value="Game" class="form-checkbox text-blue-600 h-5 w-5 rounded"><span class="ml-3 text-base">Game</span></label>
                                <label class="genre-item flex items-center p-2 rounded-lg hover:bg-gray-50 transition cursor-pointer"><input type="checkbox" name="genres[]" value="Wuxia/Xianxia" class="form-checkbox text-blue-600 h-5 w-5 rounded"><span class="ml-3 text-base">Wuxia/Xianxia</span></label>
                                <!-- Daftar genre: END -->
                            </div>
                        </div>
                    </div>
                    <div id="genre-tags" class="flex flex-wrap gap-2"></div>
                </div>
                {{-- Select Genre Multi: END --}}

            </div>
        </div>
    </div>
    {{-- ==================== BAGIAN 2: END ==================== --}}

    {{-- ==================== BAGIAN 3: KONTRIBUTOR & SUMBER: START ==================== --}}
    <div class="w-full">
        <div class="p-6 md:p-10 border-t border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800 border-b pb-4 mb-8">Kontributor & Sumber</h2>

            <div class="space-y-6">
                {{-- Row 1: Penulis & Artist: START --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="original-author" class="text-base font-semibold text-gray-700">Nama Penulis Asli <span class="text-red-500">*</span></label>
                        <input id="original-author" name="author_name" type="text" placeholder="Nama penulis/author" class="mt-1 w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 text-base" required autocomplete="off">
                    </div>
                    <div>
                        <label for="artist" class="text-base font-semibold text-gray-700">Artist</label>
                        <input id="artist" name="artist" type="text" placeholder="Nama artist sampul/ilustrasi (Opsional)" class="mt-1 w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 text-base" autocomplete="off">
                    </div>
                </div>
                {{-- Row 1: END --}}

                {{-- Row 2: Asal Bahasa & Translator: START --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-base font-semibold text-gray-700 mb-2 block">Asal Bahasa</label>
                        <div id="language-selection" class="relative dropdown">
                            <button type="button" class="dropdown-btn w-full flex items-center justify-between text-left bg-white border border-gray-300 rounded-xl py-3 px-4 hover:border-blue-500 transition">
                                <span class="dropdown-label text-base text-gray-500">Pilih Bahasa Asli</span>
                                <i class="fa-solid fa-chevron-down text-gray-400 text-xs ml-4"></i>
                            </button>
                            <div class="dropdown-menu absolute mt-2 w-full bg-white rounded-xl shadow-xl z-10 hidden opacity-0 transform scale-95">
                                <a href="#" class="dropdown-item flex items-center px-4 py-3 text-base text-gray-700 hover:bg-gray-100 transition" data-value="Jepang"><span>Jepang</span></a>
                                <a href="#" class="dropdown-item flex items-center px-4 py-3 text-base text-gray-700 hover:bg-gray-100 transition" data-value="Cina"><span>Cina</span></a>
                                <a href="#" class="dropdown-item flex items-center px-4 py-3 text-base text-gray-700 hover:bg-gray-100 transition" data-value="Korea Selatan"><span>Korea Selatan</span></a>
                                <a href="#" class="dropdown-item flex items-center px-4 py-3 text-base text-gray-700 hover:bg-gray-100 transition" data-value="Bahasa Lainnya"><span>Bahasa Lainnya</span></a>
                            </div>
                            <input type="hidden" name="original_language" id="language-input">
                        </div>
                    </div>
                    <div>
                        <label for="translator" class="text-base font-semibold text-gray-700">Translator</label>
                        <input id="translator" name="translator" type="text" placeholder="Nama penerjemah (Opsional)" class="mt-1 w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 text-base" autocomplete="off">
                    </div>
                </div>
                {{-- Row 2: END --}}

                {{-- Proofreader Full Width: START --}}
                <div>
                    <label for="proofreader" class="text-base font-semibold text-gray-700">Proofreader</label>
                    <input id="proofreader" name="proofreader" type="text" placeholder="Nama proofreader (Opsional)" class="mt-1 w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 text-base" autocomplete="off">
                </div>
                {{-- Proofreader: END --}}
            </div>
        </div>
    </div>
    {{-- ==================== BAGIAN 3: END ==================== --}}

    {{-- ==================== BAGIAN 4: FOOTER AKSI FORM: START ==================== --}}
    <div class="px-6 md:px-10 pb-6 md:pb-10 flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
        <a href="{{ route('workdesk') }}" class="nav-link w-full sm:w-auto text-center bg-gray-200 text-gray-800 font-semibold px-8 py-3 rounded-full hover:bg-gray-300 transition-colors">Batal</a>
        <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white font-semibold px-8 py-3 rounded-full hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200/50">
            <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Terbitkan Sekarang
        </button>
    </div>
    {{-- ==================== BAGIAN 4: END ==================== --}}

</form>
{{-- =========================== FORM WRAPPER: END =========================== --}}

{{-- =========================== MODAL: IMAGE EDITOR: START =========================== --}}
<div id="image-editor-modal" class="hidden fixed inset-0 z-50 bg-gray-900 bg-opacity-75 items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-5 border-b">
            <h3 id="editor-title" class="text-xl font-bold text-gray-800">Sesuaikan Gambar</h3>
            <button id="close-editor-btn" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fa-solid fa-xmark text-2xl"></i>
            </button>
        </div>
        <div class="p-5 flex-grow overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Area Cropper: START --}}
                <div class="md:col-span-1">
                    <div class="max-h-96 overflow-hidden bg-gray-100 rounded-lg">
                        <img id="editable-image" src="" alt="Image to crop" class="max-w-full block">
                    </div>
                </div>
                {{-- Area Cropper: END --}}

                {{-- Preview Crop: START --}}
                <div class="md:col-span-1 flex flex-col items-center justify-center">
                    <h4 class="text-lg font-semibold mb-3">Preview Sampul (2:3)</h4>
                    <div id="image-editor-viewport" class="w-48 aspect-[2/3] border border-gray-300 shadow-md overflow-hidden bg-gray-200" style="background-image: none; background-size: cover; background-position: center;"></div>
                </div>
                {{-- Preview Crop: END --}}
            </div>
        </div>
        <div class="p-5 border-t flex justify-end gap-3">
            <button id="cancel-editor-btn" type="button" class="bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-full hover:bg-gray-300 transition">Batal</button>
            <button id="save-editor-btn" type="button" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-full hover:bg-blue-700 transition">Simpan & Terapkan</button>
        </div>
    </div>
</div>
{{-- =========================== MODAL: IMAGE EDITOR: END =========================== --}}

{{-- =========================== MODAL: DISCARD CHANGES: START =========================== --}}
<div id="discard-changes-modal" class="hidden fixed inset-0 z-50 bg-gray-900 bg-opacity-75 items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm">
        <div class="p-6 text-center">
            <i class="fa-solid fa-triangle-exclamation text-yellow-500 text-5xl mb-4"></i>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Buang Perubahan?</h3>
            <p class="text-gray-600 mb-6">Semua perubahan yang belum disimpan akan hilang jika Anda meninggalkan halaman ini.</p>
        </div>
        <div class="p-4 border-t flex justify-center gap-3">
            <button id="keep-editing-btn" type="button" class="bg-gray-200 text-gray-800 font-semibold px-5 py-2 rounded-full hover:bg-gray-300 transition">Lanjutkan Edit</button>
            <a id="discard-confirm-btn" href="#" class="bg-red-600 text-white font-semibold px-5 py-2 rounded-full hover:bg-red-700 transition">Buang & Keluar</a>
        </div>
    </div>
</div>
{{-- =========================== MODAL: DISCARD CHANGES: END =========================== --}}
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============================= 1) GENERAL MODAL LOGIC: START =============================
        const allModals = document.querySelectorAll('#image-editor-modal, #discard-changes-modal');
        const closeModalTriggers = document.querySelectorAll('#close-editor-btn, #cancel-editor-btn, #keep-editing-btn');
        function openModal(modal){ if(modal){ modal.classList.remove('hidden'); modal.classList.add('flex'); document.body.classList.add('overflow-hidden'); } }
        function closeAllModals(){ allModals.forEach(m=>{ m.classList.add('hidden'); m.classList.remove('flex'); }); document.body.classList.remove('overflow-hidden'); }
        closeModalTriggers.forEach(btn => btn.addEventListener('click', closeAllModals));
        allModals.forEach(m => m.addEventListener('click', (e)=>{ if(e.target===m) closeAllModals(); }));
        // ============================= 1) GENERAL MODAL LOGIC: END ===============================

        // ============================= 2) DISCARD CHANGES LOGIC: START ===========================
        const form = document.getElementById('new-story-form');
        const discardChangesModal = document.getElementById('discard-changes-modal');
        const discardConfirmBtn = document.getElementById('discard-confirm-btn');
        let formChanged = false;
        if(form){
            form.querySelectorAll('input, select, textarea').forEach(el=>{ if(el.type!=='hidden' && el.id!=='cover_image_data'){ el.addEventListener('input', ()=>{ formChanged = true; }); } });
            form.addEventListener('submit', ()=>{ formChanged = false; });
        }
        function showDiscardConfirmation(url){ if(formChanged){ discardConfirmBtn.href = url; openModal(discardChangesModal); } else { window.location.href = url; } }
        const cancelLink = document.querySelector('a[href="{{ route('workdesk') }}"]');
        if(cancelLink){ cancelLink.addEventListener('click', function(e){ e.preventDefault(); showDiscardConfirmation(this.href); }); }
        document.querySelectorAll('header .nav-link').forEach(link=>{ link.addEventListener('click', function(e){ e.preventDefault(); showDiscardConfirmation(this.href); }); });
        // ============================= 2) DISCARD CHANGES LOGIC: END =============================

        // ============================= 3) QUILL (SINOPSIS): START ================================
        var toolbarOptions = [[{ 'header': [false] }], ['bold','italic','underline','strike'], [{ 'list':'ordered'},{ 'list':'bullet'}], ['link'], ['clean']];
        var quill = new Quill('#synopsis-editor', { modules:{ toolbar: toolbarOptions }, theme:'snow', placeholder:'Tuliskan ringkasan menarik tentang ceritamu...' });
        var synopsisInput = document.getElementById('synopsis-input');
        quill.on('text-change', function(){ synopsisInput.value = quill.root.innerHTML; formChanged = true; });
        form.addEventListener('submit', function(){ var content = quill.getText().trim(); synopsisInput.value = (content.length===0 || quill.root.innerHTML==='\u003cp\u003e\u003cbr\u003e\u003c/p\u003e') ? '' : quill.root.innerHTML; });
        // ============================= 3) QUILL: END =============================================

        // ============================= 4) IMAGE EDITOR (CROPPER): START ==========================
        const imageEditorModal = document.getElementById('image-editor-modal');
        const editorTitle = document.getElementById('editor-title');
        const viewport = document.getElementById('image-editor-viewport');
        const editableImage = document.getElementById('editable-image');
        const saveEditorBtn = document.getElementById('save-editor-btn');
        const coverDataInput = document.getElementById('cover_image_data');
        let cropper = null; let editorState = { target:null, previewElement:null };
        function openImageEditor(target, imageUrl){ editorState.target = target; editorState.previewElement = document.getElementById(`${target}-preview`); let aspectRatio = 2/3; viewport.classList.toggle('rounded-full', target==='avatar'); editorTitle.textContent='Sesuaikan Gambar'; editableImage.src=imageUrl; openModal(imageEditorModal); setTimeout(()=>{ if(cropper) cropper.destroy(); cropper = new Cropper(editableImage,{ aspectRatio, viewMode:1, dragMode:'move', background:false, autoCropArea:1, preview: viewport }); },300); }
        function applyChanges(){ if(!cropper) return; const canvas = cropper.getCroppedCanvas({ width:600, height:900 }); if(canvas){ const dataUrl = canvas.toDataURL('image/jpeg',0.9); editorState.previewElement.style.backgroundImage = `url('${dataUrl}')`; editorState.previewElement.style.backgroundSize='cover'; editorState.previewElement.style.backgroundPosition='center'; editorState.previewElement.classList.remove('border-dashed','border-4','bg-blue-50'); const placeholder = editorState.previewElement.querySelector('#cover-placeholder'); if(placeholder) placeholder.style.display='none'; if(editorState.target==='cover'){ coverDataInput.value = dataUrl; } formChanged = true; } if(cropper) cropper.destroy(); closeAllModals(); }
        document.querySelectorAll('.image-upload-input').forEach(input=>{ input.addEventListener('change',(e)=>{ const file = e.target.files[0]; if(file){ const reader = new FileReader(); const target = e.target.dataset.target; reader.onload = (ev)=> openImageEditor(target, ev.target.result); reader.readAsDataURL(file); } e.target.value=''; }); });
        document.querySelectorAll('.adjust-image-btn').forEach(btn=>{ btn.addEventListener('click',(e)=>{ const target = e.currentTarget.dataset.target; if(coverDataInput.value){ openImageEditor(target, coverDataInput.value); return; } const previewEl = document.getElementById(`${target}-preview`); const m = window.getComputedStyle(previewEl).backgroundImage.match(/url\("?(.*?)"?\)/); if(m && m[1] && !m[1].includes('placehold.co')){ openImageEditor(target, m[1]); } else { alert('Silakan unggah gambar terlebih dahulu.'); } }); });
        if(saveEditorBtn) saveEditorBtn.addEventListener('click', applyChanges);
        // ============================= 4) IMAGE EDITOR: END ======================================

        // ============================= 5) DROPDOWN GENRE (MULTI): START ==========================
        const genreDropdown = document.getElementById('genre-selection');
        if(genreDropdown){
            const genreDropdownBtn = genreDropdown.querySelector('.dropdown-btn');
            const genreDropdownMenu = genreDropdown.querySelector('.dropdown-menu');
            const genreSearch = genreDropdown.querySelector('#genre-search');
            const genreItems = genreDropdown.querySelectorAll('.genre-item');
            const genreTagsContainer = document.getElementById('genre-tags');
            genreDropdownBtn.addEventListener('click',(e)=>{ e.stopPropagation(); closeAllDropdowns(genreDropdownMenu); const isHidden = genreDropdownMenu.classList.contains('hidden'); genreDropdownMenu.classList.toggle('hidden', !isHidden); setTimeout(()=>{ genreDropdownMenu.classList.toggle('opacity-0', !isHidden); genreDropdownMenu.classList.toggle('scale-95', !isHidden); },10); });
            genreSearch.addEventListener('input',()=>{ const f = genreSearch.value.toLowerCase(); genreItems.forEach(item=>{ const label = item.querySelector('span').textContent.toLowerCase(); item.style.display = label.includes(f) ? 'flex' : 'none'; }); });
            genreItems.forEach(item=>{ const cb = item.querySelector('input[type="checkbox"]'); cb.addEventListener('change',()=>{ const selected = Array.from(genreItems).map(i=> i.querySelector('input[type="checkbox"]').checked ? i.querySelector('input[type="checkbox"]').value : null).filter(Boolean); if(selected.length>3){ cb.checked=false; alert('Anda hanya dapat memilih maksimal 3 genre.'); return; } updateGenreTags(); formChanged = true; }); });
            function updateGenreTags(){ genreTagsContainer.innerHTML=''; const selected = Array.from(genreItems).map(i=> i.querySelector('input[type="checkbox"]').checked ? i.querySelector('input[type="checkbox"]').value : null).filter(Boolean); const dropdownLabel = genreDropdown.querySelector('.dropdown-label'); if(selected.length===0){ dropdownLabel.textContent='Pilih genre...'; dropdownLabel.classList.add('text-gray-500'); } else { dropdownLabel.textContent = selected.join(', '); dropdownLabel.classList.remove('text-gray-500'); } selected.forEach(val=>{ const tag = document.createElement('span'); tag.className='bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-1 rounded-full flex items-center'; tag.innerHTML = `<span>${val}</span><button type="button" class="ml-2 -mr-1 text-blue-600 hover:text-blue-800 focus:outline-none" data-value="${val}"><i class="fa-solid fa-xmark text-xs"></i></button>`; genreTagsContainer.appendChild(tag); }); }
            genreTagsContainer.addEventListener('click',(e)=>{ const btn = e.target.closest('button'); if(btn){ const value = btn.dataset.value; const cb = genreDropdown.querySelector(`input[value="${value}"]`); if(cb) cb.checked=false; const evt = new Event('change'); if(cb) cb.dispatchEvent(evt); updateGenreTags(); formChanged = true; } });
        }
        // ============================= 5) DROPDOWN GENRE: END ====================================

        // ============================= 6) DROPDOWN SEDERHANA (Jenis/Status/Bahasa): START =========
        document.querySelectorAll('#type-selection, #status-selection, #language-selection').forEach(dropdown=>{
            const button = dropdown.querySelector('.dropdown-btn');
            const menu = dropdown.querySelector('.dropdown-menu');
            const label = dropdown.querySelector('.dropdown-label');
            const input = dropdown.querySelector('input[type="hidden"]');
            button.addEventListener('click',(e)=>{ e.stopPropagation(); closeAllDropdowns(menu); const isHidden = menu.classList.contains('hidden'); menu.classList.toggle('hidden', !isHidden); setTimeout(()=>{ menu.classList.toggle('opacity-0', !isHidden); menu.classList.toggle('scale-95', !isHidden); if(isHidden){ button.classList.add('border-blue-500','ring-2','ring-blue-500'); } else { button.classList.remove('border-blue-500','ring-2','ring-blue-500'); } },10); });
            menu.addEventListener('click',(e)=>{ const item = e.target.closest('.dropdown-item'); if(!item) return; e.preventDefault(); const value = item.dataset.value; const itemContent = item.innerHTML; label.innerHTML = itemContent; label.classList.remove('text-gray-500'); input.value = value; menu.classList.add('hidden','opacity-0','scale-95'); button.classList.remove('border-blue-500','ring-2','ring-blue-500'); formChanged = true; });
        });
        function closeAllDropdowns(excludeMenu=null){ document.querySelectorAll('.dropdown-menu').forEach(menu=>{ if(menu!==excludeMenu){ menu.classList.add('hidden','opacity-0','scale-95'); const button = menu.closest('.dropdown').querySelector('.dropdown-btn'); if(button){ button.classList.remove('border-blue-500','ring-2','ring-blue-500'); } } }); }
        window.addEventListener('click', ()=>{ closeAllDropdowns(); });
        // ============================= 6) DROPDOWN SEDERHANA: END ================================
    });
</script>
@endpush
