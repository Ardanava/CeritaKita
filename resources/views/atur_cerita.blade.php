@extends('layouts.app')

@section('title', 'Atur Cerita - ' . $story->title)

@push('styles')
<style>
  /* ---------- util kecil ---------- */
  .custom-scrollbar::-webkit-scrollbar{width:8px}
  .custom-scrollbar::-webkit-scrollbar-track{background:#f1f5f9}
  .custom-scrollbar::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:10px}

  .quill-content-display{font-family:'Lora',serif}
  .quill-content-display .ql-align-center{text-align:center}
  .quill-content-display .ql-align-right{text-align:right}
  .quill-content-display .ql-align-justify{text-align:justify}
  .quill-content-display .ql-align-left{text-align:left}
  .quill-content-display blockquote{border-left:4px solid #e2e8f0;margin:.5rem 0;padding-left:16px;font-style:italic}

  /* ---------- pretty select (custom dropdown) ---------- */
  .selectmenu{position:relative;min-width:120px}
  .selectmenu-btn{
      display:inline-flex;align-items:center;gap:.5rem;
      background:#fff;border:1px solid #e5e7eb;border-radius:9999px;
      padding:.5rem .875rem;font-weight:600;color:#111827;
      transition:box-shadow .2s, border-color .2s;
  }
  .selectmenu-btn:focus{outline:0;box-shadow:0 0 0 3px rgba(59,130,246,.3)}
  .selectmenu .chev{font-size:.825rem;color:#6b7280}
  .selectmenu-menu{
      position:absolute;right:0;top:calc(100% + .4rem);
      background:#fff;border:1px solid #e5e7eb;border-radius:.75rem;
      box-shadow:0 10px 30px rgba(2,6,23,.1);min-width:160px;padding:.25rem;z-index:30
  }
  .selectmenu-item{
      padding:.5rem .75rem;border-radius:.5rem;cursor:pointer;
      font-weight:600;color:#111827;white-space:nowrap
  }
  .selectmenu-item[aria-selected="true"]{background:#e5edff;color:#1d4ed8}
  .selectmenu-item:hover{background:#f3f4f6}
  .selectmenu.hidden .selectmenu-menu{display:none}

  /* ---------- buttons ---------- */
  .primary-btn{
      background:#2563eb;color:#fff;padding:.55rem 1rem;border-radius:9999px;
      font-weight:700;box-shadow:0 6px 18px rgba(37,99,235,.18)
  }
  .primary-btn:hover{background:#1d4ed8}
  .ghost-btn{
      background:#eef2ff;color:#4338ca;padding:.55rem 1rem;border-radius:9999px;font-weight:700
  }
  .ghost-btn:hover{background:#e0e7ff}
</style>
@endpush

@section('content')
{{-- ==================== MAIN WRAPPER: START ==================== --}}
<main class="container mx-auto px-4 lg:px-8 py-12">
  <div class="max-w-6xl mx-auto">

    {{-- ===== Back Link ke Workdesk: START ===== --}}
    <a href="{{ route('workdesk') }}" class="text-sm font-semibold text-blue-600 hover:underline flex items-center gap-2 mb-4">
      <i class="fa-solid fa-arrow-left"></i> Kembali ke Workdesk
    </a>
    {{-- ===== Back Link ke Workdesk: END ===== --}}

    {{-- ===== Halaman Title: START ===== --}}
    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-8">Atur Cerita: {{ $story->title }}</h1>
    {{-- ===== Halaman Title: END ===== --}}

    {{-- ================= Story Info Card: START ================= --}}
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        {{-- --- Kolom Cover: START --- --}}
        <div class="md:col-span-1">
          <img
            src="{{ $story->cover_image_path ? Storage::url($story->cover_image_path) : 'https://placehold.co/600x900/3B82F6/FFFFFF?text=Cover' }}"
            alt="Cover {{ $story->title }}"
            class="w-full h-auto rounded-lg shadow-md aspect-[2/3] object-cover bg-gray-100">
        </div>
        {{-- --- Kolom Cover: END --- --}}

        {{-- --- Kolom Detail Cerita: START --- --}}
        <div class="md:col-span-2 flex flex-col">

          {{-- Header Judul + Tombol Edit: START --}}
          <div class="flex items-start justify-between gap-4">
            <h2 class="text-3xl font-bold">{{ $story->title }}</h2>
            <a href="{{ route('stories.edit', $story) }}"
               class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow-sm">
              <i class="fa-solid fa-pen"></i> Edit Karya
            </a>
          </div>
          {{-- Header Judul + Tombol Edit: END --}}

          {{-- Badge Info (type/status/origin/genres): START --}}
          <div class="mt-3 flex flex-wrap items-center gap-2">
            @if($story->type)
              <span class="px-2.5 py-1 rounded-full text-xs bg-gray-100 text-gray-800 flex items-center gap-1">
                <i class="fa-solid fa-book"></i>{{ $story->type }}
              </span>
            @endif
            @if($story->status)
              <span class="px-2.5 py-1 rounded-full text-xs bg-emerald-100 text-emerald-800 flex items-center gap-1">
                <i class="fa-solid fa-signal"></i>{{ $story->status }}
              </span>
            @endif
            @if($story->origin)
              <span class="px-2.5 py-1 rounded-full text-xs bg-indigo-100 text-indigo-800 flex items-center gap-1">
                <i class="fa-solid fa-globe"></i>{{ $story->origin }}
              </span>
            @endif
            @if(!empty($story->genres))
              @foreach(array_slice($story->genres,0,3) as $g)
                <span class="px-2.5 py-1 rounded-full text-xs bg-blue-100 text-blue-800">{{ $g }}</span>
              @endforeach
              @if(count($story->genres) > 3)
                <span class="px-2.5 py-1 rounded-full text-xs bg-blue-50 text-blue-700">+{{ count($story->genres) - 3 }}</span>
              @endif
            @endif
          </div>
          {{-- Badge Info: END --}}

          {{-- Sinopsis: START --}}
          <div class="mt-4 border-t pt-4">
            <h3 class="font-semibold text-gray-700 mb-2">Sinopsis</h3>
            <div class="text-gray-600 text-sm leading-relaxed max-h-40 overflow-y-auto custom-scrollbar pr-3 quill-content-display">
              {!! $story->synopsis !!}
            </div>
          </div>
          {{-- Sinopsis: END --}}

          {{-- Statistik & Info: START --}}
          <div class="mt-6 pt-6 border-t border-gray-100">
            <h3 class="font-semibold text-gray-700 mb-4">Statistik & Info</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
              <div class="flex items-start text-gray-600">
                <i class="fa-solid fa-signal w-5 text-center mr-2 {{ $story->status === 'Tamat' ? 'text-gray-500' : 'text-green-500' }}"></i>
                <div><p class="font-semibold">Status</p><p>{{ $story->status ?? '—' }}</p></div>
              </div>
              <div class="flex items-start text-gray-600">
                <i class="fa-solid fa-book w-5 text-center mr-2 text-gray-500"></i>
                <div><p class="font-semibold">Jenis</p><p>{{ $story->type ?? '—' }}</p></div>
              </div>
              <div class="flex items-start text-gray-600">
                <i class="fa-solid fa-globe w-5 text-center mr-2 text-blue-500"></i>
                <div><p class="font-semibold">Asal Bahasa</p><p>{{ $story->origin ?? '—' }}</p></div>
              </div>
              <div class="flex items-start text-gray-600">
                <i class="fa-solid fa-user-pen w-5 text-center mr-2 text-gray-500"></i>
                <div><p class="font-semibold">Penulis Asli</p><p>{{ $story->author_name ?? '—' }}</p></div>
              </div>
              <div class="flex items-start text-gray-600">
                <i class="fa-solid fa-paintbrush w-5 text-center mr-2 text-pink-500"></i>
                <div><p class="font-semibold">Artist</p><p>{{ $story->artist ?: '—' }}</p></div>
              </div>
              <div class="flex items-start text-gray-600">
                <i class="fa-solid fa-language w-5 text-center mr-2 text-purple-500"></i>
                <div><p class="font-semibold">Translator</p><p>{{ $story->translator ?: '—' }}</p></div>
              </div>
              <div class="flex items-start text-gray-600">
                <i class="fa-solid fa-spell-check w-5 text-center mr-2 text-emerald-500"></i>
                <div><p class="font-semibold">Proofreader</p><p>{{ $story->proofreader ?: '—' }}</p></div>
              </div>
              <div class="flex items-start text-gray-600">
                <i class="fa-solid fa-eye w-5 text-center mr-2 text-purple-500"></i>
                <div><p class="font-semibold">Dilihat</p><p>{{ number_format($story->views ?? 0, 0, ',', '.') }}</p></div>
              </div>
            </div>
          </div>
          {{-- Statistik & Info: END --}}

        </div>
        {{-- --- Kolom Detail Cerita: END --- --}}
      </div>
    </div>
    {{-- ================= Story Info Card: END =================== --}}

    {{-- ========================= Daftar Bab Card: START ========================= --}}
    <div class="bg-white rounded-xl shadow-lg">

      {{-- Header Daftar Bab + Tombol Tambah: START --}}
      <div class="p-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Bab</h2>
        <a href="{{ route('chapters.create', ['story' => $story]) }}"
           class="w-full sm:w-auto text-center bg-blue-600 text-white font-bold py-2 px-6 rounded-full hover:bg-blue-700 transition-transform hover:scale-105 shadow-md flex items-center justify-center gap-2">
          <i class="fa-solid fa-plus"></i> Tambah Bab Baru
        </a>
      </div>
      {{-- Header Daftar Bab + Tombol Tambah: END --}}

      {{-- Filter Bar (Search + Per Page): START --}}
      <form method="GET" class="p-6 flex flex-col lg:flex-row lg:items-center gap-4 border-b">

        {{-- Input Pencarian: START --}}
        <div class="relative flex-1">
          <input type="text" name="q" value="{{ request('q') }}" class="w-full py-2 pl-10 pr-4 border rounded-full focus:ring-2 focus:ring-blue-500" placeholder="Cari judul bab..." autocomplete="off">
          <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
        {{-- Input Pencarian: END --}}

        {{-- Per Page (Custom Select) + Apply / Reset: START --}}
        <div class="lg:ml-auto flex items-center gap-2">
          {{-- Custom Select Per Page: START --}}
          <div class="selectmenu hidden" id="perPageMenu" data-name="per_page" data-value="{{ (int)request('per_page', 20) }}">
            <button type="button" class="selectmenu-btn" aria-haspopup="listbox" aria-expanded="false">
              <span class="label">{{ (int)request('per_page', 20) }}/hal</span>
              <i class="fa-solid fa-chevron-down chev"></i>
            </button>

            <ul class="selectmenu-menu hidden" role="listbox">
              @foreach([10,20,30,50,100] as $n)
                <li class="selectmenu-item"
                    role="option"
                    data-value="{{ $n }}"
                    aria-selected="{{ (int)request('per_page', 20)===$n ? 'true' : 'false' }}">
                    {{ $n }}/hal
                </li>
              @endforeach
            </ul>

            <input type="hidden" name="per_page" value="{{ (int)request('per_page', 20) }}">
          </div>
          {{-- Custom Select Per Page: END --}}

          {{-- Tombol Terapkan + Reset: START --}}
          <button class="primary-btn">Terapkan</button>

          @if(request()->hasAny(['q','per_page','page']))
            <a href="{{ route('stories.manage', $story) }}" class="ghost-btn">Reset</a>
          @endif
          {{-- Tombol Terapkan + Reset: END --}}
        </div>
        {{-- Per Page + Actions: END --}}
      </form>
      {{-- Filter Bar: END --}}

      {{-- Tabel Daftar Bab: START --}}
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
            <tr>
              <th class="p-4 font-semibold">Judul Bab</th>
              <th class="p-4 font-semibold">Status</th>
              <th class="p-4 font-semibold hidden sm:table-cell">Jumlah Kata</th>
              <th class="p-4 font-semibold hidden md:table-cell">Terakhir Diubah</th>
              <th class="p-4 font-semibold text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            @forelse ($chapters as $chapter)
              <tr>
                <td class="p-4 font-semibold text-gray-800">
                  {{ $chapter->title }}
                </td>
                <td class="p-4">
                  <span class="text-xs font-bold {{ ($chapter->status ?? 'draft') === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }} px-2 py-1 rounded-full">
                    {{ Str::ucfirst($chapter->status ?? 'draft') }}
                  </span>
                </td>
                <td class="p-4 text-gray-600 hidden sm:table-cell">
                  {{ number_format($chapter->word_count ?? 0, 0, ',', '.') }}
                </td>
                <td class="p-4 text-gray-600 hidden md:table-cell">
                  {{ optional($chapter->updated_at)->format('d M Y') }}
                </td>
                <td class="p-4 text-right">
                  {{-- Aksi Desktop: START --}}
                  <div class="hidden sm:flex items-center justify-end space-x-1">
                    <a href="{{ route('stories.chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug]) }}" class="text-gray-400 hover:text-green-600 w-8 h-8 inline-flex items-center justify-center rounded-full hover:bg-green-50" title="Baca"><i class="fa-solid fa-eye"></i></a>
                    <a href="{{ route('chapters.edit', ['story' => $story, 'chapter' => $chapter]) }}" class="text-gray-400 hover:text-blue-600 w-8 h-8 inline-flex items-center justify-center rounded-full hover:bg-blue-50" title="Edit"><i class="fa-solid fa-pen"></i></a>
                    <button class="delete-btn text-gray-400 hover:text-red-600 w-8 h-8 inline-flex items-center justify-center rounded-full hover:bg-red-50" title="Hapus" data-target-form="delete-chapter-form-{{ $chapter->id }}">
                      <i class="fa-solid fa-trash-can"></i>
                    </button>
                  </div>
                  {{-- Aksi Desktop: END --}}

                  {{-- Aksi Mobile (Kebab): START --}}
                  <div class="relative sm:hidden">
                    <button class="kebab-btn text-gray-500 hover:text-blue-600 w-8 h-8 rounded-full hover:bg-gray-100">
                      <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <div class="kebab-menu absolute right-0 top-full mt-2 w-32 bg-white rounded-md shadow-lg z-10 hidden text-left">
                      <a href="{{ route('stories.chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug]) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fa-solid fa-eye w-4 text-center"></i> Baca</a>
                      <a href="{{ route('chapters.edit', ['story' => $story, 'chapter' => $chapter]) }}" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fa-solid fa-pen w-4 text-center"></i> Edit</a>
                      <button class="delete-btn w-full flex items-center gap-2 px-4 py-2 text-sm text-red-500 hover:bg-red-50" data-target-form="delete-chapter-form-{{ $chapter->id }}">
                        <i class="fa-solid fa-trash-can w-4 text-center"></i> Hapus
                      </button>
                    </div>
                  </div>
                  {{-- Aksi Mobile (Kebab): END --}}

                  {{-- Form Hapus (Hidden): START --}}
                  <form action="{{ route('chapters.destroy', ['story' => $story, 'chapter' => $chapter]) }}" method="POST" id="delete-chapter-form-{{ $chapter->id }}" class="hidden">
                    @csrf
                    @method('DELETE')
                  </form>
                  {{-- Form Hapus (Hidden): END --}}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="p-4 text-center text-gray-500">Belum ada bab yang ditulis untuk cerita ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{-- Tabel Daftar Bab: END --}}

      {{-- Pagination: START --}}
      @if ($chapters->lastPage() > 1)
        <div class="p-4 border-t flex justify-center">
          <nav class="flex items-center gap-2">
            @if ($chapters->onFirstPage())
              <span class="px-3 py-2 rounded-lg bg-gray-200 text-gray-500 font-semibold shadow-sm cursor-not-allowed">&laquo;</span>
            @else
              <a href="{{ $chapters->previousPageUrl() }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm">&laquo;</a>
            @endif

            @foreach ($chapters->getUrlRange(1, $chapters->lastPage()) as $page => $url)
              @if ($page == $chapters->currentPage())
                <span class="px-3 py-2 rounded-lg bg-blue-600 text-white font-semibold shadow-md">{{ $page }}</span>
              @else
                <a href="{{ $url }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm">{{ $page }}</a>
              @endif
            @endforeach

            @if ($chapters->hasMorePages())
              <a href="{{ $chapters->nextPageUrl() }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm">&raquo;</a>
            @else
              <span class="px-3 py-2 rounded-lg bg-gray-200 text-gray-500 font-semibold shadow-sm cursor-not-allowed">&raquo;</span>
            @endif
          </nav>
        </div>
      @endif
      {{-- Pagination: END --}}

    </div>
    {{-- ========================= Daftar Bab Card: END =========================== --}}

  </div>
</main>
{{-- ==================== MAIN WRAPPER: END ====================== --}}

{{-- ==================== Delete Modal: START ==================== --}}
<div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50" data-form-to-submit="">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-sm text-center p-6">
    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
      <i class="fa-solid fa-trash-can text-2xl text-red-600"></i>
    </div>
    <h3 class="text-lg font-bold text-gray-800 mt-4">Hapus Bab Ini?</h3>
    <p class="text-gray-600 mt-2 text-sm">Apakah Anda yakin ingin menghapus bab ini? Tindakan ini tidak dapat diurungkan.</p>
    <div class="mt-6 flex justify-center gap-4">
      <button id="cancel-delete-btn" class="bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-full hover:bg-gray-300">Batal</button>
      <button id="confirm-delete-btn" class="bg-red-600 text-white font-semibold px-6 py-2 rounded-full hover:bg-red-700">Hapus</button>
    </div>
  </div>
</div>
{{-- ==================== Delete Modal: END ====================== --}}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // ===== Custom Select PerPage: START =====
  const sel = document.getElementById('perPageMenu');
  if (sel) {
    sel.classList.remove('hidden'); // mencegah FOUC
    const btn = sel.querySelector('.selectmenu-btn');
    const menu = sel.querySelector('.selectmenu-menu');
    const label = sel.querySelector('.label');
    const input = sel.querySelector('input[type="hidden"]');
    const valueNow = sel.dataset.value || input.value;

    Array.from(menu.querySelectorAll('.selectmenu-item')).forEach(li => {
      if (String(li.dataset.value) === String(valueNow)) li.setAttribute('aria-selected','true');
    });

    const toggle = () => menu.classList.toggle('hidden');
    btn.addEventListener('click', (e)=>{ e.stopPropagation(); toggle(); });
    window.addEventListener('click', ()=> menu.classList.add('hidden'));

    menu.addEventListener('click', (e) => {
      const li = e.target.closest('.selectmenu-item');
      if (!li) return;
      menu.querySelectorAll('.selectmenu-item').forEach(i=>i.setAttribute('aria-selected','false'));
      li.setAttribute('aria-selected','true');
      input.value = li.dataset.value;
      label.textContent = `${li.dataset.value}/hal`;
      menu.classList.add('hidden');
    });
  }
  // ===== Custom Select PerPage: END =====

  // ===== Delete Modal Logic: START =====
  const deleteModal = document.getElementById('delete-modal');
  const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
  const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
  let rowToRemove = null;

  function openDelete(formId, row){
    deleteModal.dataset.formToSubmit = formId;
    rowToRemove = row;
    deleteModal.classList.remove('hidden'); deleteModal.classList.add('flex');
  }
  function closeDelete(){
    deleteModal.dataset.formToSubmit = '';
    rowToRemove = null;
    deleteModal.classList.add('hidden'); deleteModal.classList.remove('flex');
  }

  document.querySelector('tbody')?.addEventListener('click', (e)=>{
    const delBtn = e.target.closest('.delete-btn');
    if(!delBtn) return;
    e.preventDefault();
    const formId = delBtn.dataset.targetForm;
    const row = delBtn.closest('tr');
    openDelete(formId, row);
  });

  cancelDeleteBtn?.addEventListener('click', closeDelete);
  deleteModal?.addEventListener('click', (e)=>{ if(e.target===deleteModal) closeDelete(); });

  confirmDeleteBtn?.addEventListener('click', ()=>{
    const formId = deleteModal.dataset.formToSubmit;
    const form = document.getElementById(formId);
    if(form){ form.submit(); }
    if(rowToRemove){
      rowToRemove.style.opacity='0';
      rowToRemove.style.transition='opacity .25s ease';
      setTimeout(()=>rowToRemove.remove(), 250);
    }
    closeDelete();
  });
  // ===== Delete Modal Logic: END =====

  // ===== Mobile Kebab Menu: START =====
  document.querySelectorAll('.kebab-btn').forEach(btn=>{
    btn.addEventListener('click', (e)=>{
      e.stopPropagation();
      const menu = btn.nextElementSibling;
      document.querySelectorAll('.kebab-menu').forEach(m=>{ if(m!==menu) m.classList.add('hidden'); });
      menu.classList.toggle('hidden');
    });
  });
  window.addEventListener('click', ()=> document.querySelectorAll('.kebab-menu').forEach(m=>m.classList.add('hidden')));
  // ===== Mobile Kebab Menu: END =====
});
</script>
@endpush
