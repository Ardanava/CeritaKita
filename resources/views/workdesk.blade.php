@extends('layouts.app')

@section('title', 'Workdesk - Karyaku')

@section('content')
<main class="container mx-auto px-4 lg:px-8 py-12">
  <div class="max-w-6xl mx-auto">

    {{-- ===== START: Header ===== --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
      <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">Workdesk Karyaku</h1>

      {{-- ~~ START: Search ~~ --}}
      <form method="GET" action="{{ route('workdesk') }}" class="relative w-full sm:w-auto order-3 sm:order-2">
        <input type="text" autocomplete="off" name="search" value="{{ request('search') }}" placeholder="Cari karyaku..." class="w-full sm:w-64 bg-white border border-gray-300 rounded-full py-2 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <i class="fa-solid fa-search text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
        <button type="submit" class="hidden"></button>
      </form>
      {{-- ~~ END: Search ~~ --}}

      {{-- ~~ START: Quick Actions ~~ --}}
      <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto order-2 sm:order-3">
        <a href="{{ route('stories.create') }}" class="w-full sm:w-auto text-center bg-blue-600 text-white font-bold py-2 px-6 rounded-full hover:bg-blue-700 transition-transform hover:scale-105 shadow-md flex items-center justify-center gap-2">
          <i class="fa-solid fa-plus"></i> Tulis Karya Baru
        </a>
        <a href="{{ route('posts.create') }}" class="w-full sm:w-auto text-center bg-gray-700 text-white font-bold py-2 px-6 rounded-full hover:bg-gray-800 transition-transform hover:scale-105 shadow-md flex items-center justify-center gap-2" title="Tambah Postingan (Papan Info)">
          <i class="fa-solid fa-thumbtack"></i>
        </a>
      </div>
      {{-- ~~ END: Quick Actions ~~ --}}
    </div>
    {{-- ===== END: Header ===== --}}

    {{-- ===== START: Story Grid ===== --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
      @forelse ($stories as $story)
        @php
          $cover = $story->cover_image_path
            ? Storage::url($story->cover_image_path)
            : 'https://placehold.co/96x128/60A5FA/FFFFFF?text=' . urlencode(Str::limit($story->title, 8));

          $genre       = $story->genres[0] ?? 'Umum';
          $latest      = $story->chapters()->orderBy('id','desc')->first();
          $latestTitle = $latest?->title ?? 'Belum ada bab';
          $latestWhen  = ($latest?->updated_at ?? $latest?->created_at ?? $story->updated_at)->diffForHumans();
          $chapCount   = $story->chapters()->count();
          $views       = number_format($story->views, 0, ',', '.');

          $statusChip  = $story->status === 'Tamat'
                        ? 'bg-gray-200 text-gray-700'
                        : 'bg-green-100 text-green-700';
        @endphp

        {{-- ~~ START: Story Card ~~ --}}
        <div class="my-story-card group bg-white rounded-xl border border-gray-200 hover:border-gray-300 shadow-sm hover:shadow-md transition-all duration-200">
          <div class="p-4 flex items-start gap-4">

            {{-- :: START: Thumb :: --}}
            <a href="{{ route('stories.manage', $story) }}" class="relative flex-shrink-0">
              <img src="{{ $cover }}"
                   alt="Sampul {{ $story->title }}"
                   class="w-16 h-20 md:w-20 md:h-24 object-cover rounded-lg ring-1 ring-gray-200 group-hover:ring-gray-300" />
            </a>
            {{-- :: END: Thumb :: --}}

            {{-- :: START: Meta :: --}}
            <div class="min-w-0 flex-1">
              <div class="flex items-start gap-2">
                <h3 class="text-base md:text-lg font-extrabold text-gray-900 leading-snug truncate">
                  <a href="{{ route('stories.manage', $story) }}" class="hover:text-blue-600">
                    {{ $story->title }}
                  </a>
                </h3>
                <span class="ml-auto hidden sm:inline-flex items-center text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $statusChip }}">
                  {{ $story->status }}
                </span>
              </div>

              {{-- :: START: Genre + Latest Chapter :: --}}
              <div class="mt-1 flex flex-wrap items-center gap-2 text-gray-700">
                <span class="inline-flex items-center text-xs font-semibold bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">
                  {{ $genre }}
                </span>

                @php
                  // gunakan eager load jika tersedia; fallback ke query
                  $latest = $story->chapters->first() ?? $story->chapters()->latest('id')->first();
                @endphp
                <div>
                  <span>Ch. {{ $story->chapters_count }}</span>
                  @if($latest)
                    <span>— {{ Str::limit($latest->title, 40) }}</span>
                  @endif
                </div>
              </div>
              {{-- :: END: Genre + Latest Chapter :: --}}

              {{-- :: START: Stats :: --}}
              <div class="mt-2 flex items-center gap-3 text-sm text-gray-500">
                <span class="inline-flex items-center"><i class="fa-solid fa-eye mr-1.5 text-gray-400"></i>{{ $views }}</span>
                <span class="inline-flex items-center"><i class="fa-solid fa-layer-group mr-1.5 text-gray-400"></i>{{ $chapCount }} bab</span>
                <span class="inline-flex items-center"><i class="fa-regular fa-clock mr-1.5 text-gray-400"></i>{{ $latestWhen }}</span>
                <span class="sm:hidden inline-flex items-center text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $statusChip }} ml-auto">
                  {{ $story->status }}
                </span>
              </div>
              {{-- :: END: Stats :: --}}

              {{-- :: START: Actions :: --}}
              <div class="mt-3 flex items-center gap-2">
                <a href="{{ route('stories.manage', $story) }}"
                   class="inline-flex items-center gap-2 bg-gray-100 text-gray-800 font-semibold px-3 py-1.5 rounded-full hover:bg-gray-200">
                  <i class="fa-solid fa-gear"></i> Kelola
                </a>

                <a href="{{ route('stories.show', $story->slug) }}"
                   class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-3 py-1.5 rounded-full hover:bg-blue-700">
                  <i class="fa-solid fa-circle-info"></i> Lihat
                </a>

                <button class="delete-btn ml-auto text-gray-400 hover:text-red-600 w-9 h-9 inline-flex items-center justify-center rounded-lg hover:bg-red-50"
                        title="Hapus"
                        data-target-form="delete-story-form-{{ $story->id }}"
                        data-item-container=".my-story-card">
                  <i class="fa-solid fa-trash-can"></i>
                </button>

                <form action="{{ route('stories.destroy', $story) }}" method="POST" id="delete-story-form-{{ $story->id }}" class="hidden">
                  @csrf @method('DELETE')
                </form>
              </div>
              {{-- :: END: Actions :: --}}
            </div>
            {{-- :: END: Meta :: --}}

          </div>
        </div>
        {{-- ~~ END: Story Card ~~ --}}
      @empty
        {{-- ~~ START: Empty Grid ~~ --}}
        <div class="xl:col-span-2 text-center text-gray-500 py-16">
          <i class="fa-solid fa-ghost text-5xl mb-4 text-gray-300"></i>
          @if(request('search'))
            <h3 class="text-xl font-semibold">Karya tidak ditemukan</h3>
            <p class="mt-2">Tidak ada karyamu yang cocok dengan pencarian "{{ request('search') }}".</p>
          @else
            <h3 class="text-xl font-semibold">Anda belum menulis karya.</h3>
            <p class="mt-2">Ayo mulai tulis karya pertamamu!</p>
          @endif
        </div>
        {{-- ~~ END: Empty Grid ~~ --}}
      @endforelse
    </div>
    {{-- ===== END: Story Grid ===== --}}

    {{-- ===== START: Story Pagination ===== --}}
    @if ($stories->hasPages() && $stories->lastPage() > 1)
      <div class="p-4 flex justify-center">
        <nav class="flex items-center gap-2" aria-label="Paginasi karya">
          {{-- Prev --}}
          @if ($stories->onFirstPage())
            <span class="px-3 py-2 rounded-lg bg-gray-200 text-gray-500 font-semibold shadow-sm cursor-not-allowed">&laquo;</span>
          @else
            <a href="{{ $stories->previousPageUrl() }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm" rel="prev">&laquo;</a>
          @endif

          {{-- Numbers --}}
          @foreach ($stories->getUrlRange(1, $stories->lastPage()) as $page => $url)
            @if ($page == $stories->currentPage())
              <span class="px-3 py-2 rounded-lg bg-blue-600 text-white font-semibold shadow-md" aria-current="page">{{ $page }}</span>
            @else
              <a href="{{ $url }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm">{{ $page }}</a>
            @endif
          @endforeach

          {{-- Next --}}
          @if ($stories->hasMorePages())
            <a href="{{ $stories->nextPageUrl() }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm" rel="next">&raquo;</a>
          @else
            <span class="px-3 py-2 rounded-lg bg-gray-200 text-gray-500 font-semibold shadow-sm cursor-not-allowed">&raquo;</span>
          @endif
        </nav>
      </div>
    @endif
    {{-- ===== END: Story Pagination ===== --}}

    {{-- ===== START: Posts & Announcements ===== --}}
    <section class="mt-12">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Postingan & Pengumuman</h2>

        {{-- (opsional) tombol buat post --}}
        {{--
        <a href="{{ route('posts.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-4 py-2 rounded-full hover:bg-blue-700">
           <i class="fa-solid fa-bullhorn"></i> Tulis Postingan
        </a>
        --}}
      </div>

      {{-- ~~ START: Post List ~~ --}}
      <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <ul class="divide-y divide-gray-200">
          @forelse ($posts as $post)
            @php
              $type = strtolower($post->type ?? 'info');
              switch ($type) {
                case 'announcement': case 'pengumuman':
                  $accent='bg-indigo-500'; $chip='bg-indigo-100 text-indigo-700'; $icon='fa-bullhorn';         $label='Pengumuman'; break;
                case 'update': case 'pembaruan':
                  $accent='bg-green-500';  $chip='bg-green-100 text-green-700';  $icon='fa-arrows-rotate';   $label='Pembaruan';  break;
                case 'maintenance': case 'perawatan':
                  $accent='bg-amber-500';  $chip='bg-amber-100 text-amber-700';  $icon='fa-screwdriver-wrench'; $label='Perawatan'; break;
                default:
                  $accent='bg-blue-500';   $chip='bg-gray-100 text-gray-700';   $icon='fa-circle-info';     $label='Info';       break;
              }
              $isPinned   = (bool)($post->is_pinned ?? false);
              $visibility = $post->visibility === 'private' ? 'private' : 'public';
              $visIcon    = $visibility === 'private' ? 'fa-lock' : 'fa-earth-americas';
              $visText    = $visibility === 'private' ? 'Privat' : 'Publik';
            @endphp

            {{-- :: START: Post Item :: --}}
            <li class="post-item-container group hover:bg-gray-50/60 transition-colors">
              <div class="flex items-stretch">
                <div class="w-1 {{ $accent }}"></div>

                <div class="flex-1 p-4">
                  {{-- chips --}}
                  <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $chip }}">
                      <i class="fa-solid {{ $icon }}"></i> {{ $label }}
                    </span>
                    @if($isPinned)
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold rounded-full bg-amber-100 text-amber-700">
                        <i class="fa-solid fa-thumbtack"></i> Disematkan
                      </span>
                    @endif
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold rounded-full bg-gray-100 text-gray-700">
                      <i class="fa-solid {{ $visIcon }}"></i> {{ $visText }}
                    </span>
                  </div>

                  {{-- title + desktop actions --}}
                  <div class="flex items-start justify-between gap-3">
                    <a href="{{ route('posts.show', $post) }}"
                       class="font-semibold text-gray-900 leading-snug hover:text-blue-600 truncate">
                      {{ $post->title }}
                    </a>
                    <div class="hidden sm:flex items-center gap-2">
                      <a href="{{ route('posts.edit', $post) }}"
                         class="inline-flex items-center gap-1.5 text-sm bg-gray-100 text-gray-800 px-3 py-1.5 rounded-full hover:bg-gray-200">
                        <i class="fa-solid fa-pen"></i> Edit
                      </a>
                      <button type="button"
                              class="delete-post-btn inline-flex items-center gap-1.5 text-sm bg-red-100 text-red-700 px-3 py-1.5 rounded-full hover:bg-red-200"
                              data-target-form="delete-post-form-{{ $post->id }}"
                              data-item-container=".post-item-container">
                        <i class="fa-solid fa-trash-can"></i> Hapus
                      </button>
                    </div>
                  </div>

                  {{-- meta --}}
                  <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-gray-500">
                    <span class="inline-flex items-center gap-1">
                      <i class="fa-regular fa-clock"></i> {{ $post->created_at->diffForHumans() }}
                    </span>
                    @if(!empty($post->author))
                      <span class="inline-flex items-center gap-1">
                        <i class="fa-regular fa-user"></i> {{ $post->author }}
                      </span>
                    @endif
                    @if(!empty($post->version))
                      <span class="inline-flex items-center gap-1">
                        <i class="fa-solid fa-tag"></i> v{{ $post->version }}
                      </span>
                    @endif
                  </div>

                  {{-- mobile actions --}}
                  <div class="mt-3 flex sm:hidden items-center gap-2">
                    <a href="{{ route('posts.edit', $post) }}"
                       class="inline-flex items-center gap-1.5 text-sm bg-gray-100 text-gray-800 px-3 py-1.5 rounded-full hover:bg-gray-200">
                      <i class="fa-solid fa-pen"></i> Edit
                    </a>
                    <button type="button"
                            class="delete-post-btn inline-flex items-center gap-1.5 text-sm bg-red-100 text-red-700 px-3 py-1.5 rounded-full hover:bg-red-200"
                            data-target-form="delete-post-form-{{ $post->id }}"
                            data-item-container=".post-item-container">
                      <i class="fa-solid fa-trash-can"></i> Hapus
                    </button>
                  </div>

                  {{-- hidden delete form --}}
                  <form action="{{ route('posts.destroy', $post) }}" method="POST" id="delete-post-form-{{ $post->id }}" class="hidden">
                    @csrf @method('DELETE')
                  </form>
                </div>

                {{-- chevron --}}
                <a href="{{ route('posts.show', $post) }}"
                   class="pr-4 text-gray-400 group-hover:text-gray-500 hidden md:flex items-center" aria-label="Buka detail">
                  <i class="fa-solid fa-chevron-right"></i>
                </a>
              </div>
            </li>
            {{-- :: END: Post Item :: --}}
          @empty
            {{-- :: START: Empty Posts :: --}}
            <li class="px-6 py-10 text-center text-gray-500">
              <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 mb-2">
                <i class="fa-regular fa-bell"></i>
              </div>
              <p class="font-medium">Belum ada postingan.</p>
              <p class="text-sm">Buat satu untuk mulai mengisi Papan Info.</p>
            </li>
            {{-- :: END: Empty Posts :: --}}
          @endforelse
        </ul>

        {{-- ~~ START: Post Pagination ~~ --}}
        @if ($posts->hasPages())
          <div class="p-4 border-t flex justify-center">
            <nav class="flex items-center gap-2" aria-label="Paginasi postingan">
              @if ($posts->onFirstPage())
                <span class="px-3 py-2 rounded-lg bg-gray-200 text-gray-500 font-semibold shadow-sm cursor-not-allowed">&laquo;</span>
              @else
                <a href="{{ $posts->previousPageUrl() }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm" rel="prev">&laquo;</a>
              @endif

              @foreach ($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
                @if ($page == $posts->currentPage())
                  <span class="px-3 py-2 rounded-lg bg-blue-600 text-white font-semibold shadow-md" aria-current="page">{{ $page }}</span>
                @else
                  <a href="{{ $url }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm">{{ $page }}</a>
                @endif
              @endforeach

              @if ($posts->hasMorePages())
                <a href="{{ $posts->nextPageUrl() }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm" rel="next">&raquo;</a>
              @else
                <span class="px-3 py-2 rounded-lg bg-gray-200 text-gray-500 font-semibold shadow-sm cursor-not-allowed">&raquo;</span>
              @endif
            </nav>
          </div>
        @endif
        {{-- ~~ END: Post Pagination ~~ --}}
      </div>
      {{-- ~~ END: Post List ~~ --}}
    </section>
    {{-- ===== END: Posts & Announcements ===== --}}

  </div>
</main>

{{-- ===== START: Delete Modal ===== --}}
<div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-sm text-center p-6">
    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
      <i class="fa-solid fa-trash-can text-2xl text-red-600"></i>
    </div>
    <h3 class="text-lg font-bold text-gray-800 mt-4">Anda Yakin?</h3>
    <p class="text-gray-600 mt-2 text-sm">Apakah Anda yakin ingin menghapus ini? Tindakan ini tidak dapat diurungkan.</p>
    <div class="mt-6 flex justify-center gap-4">
      <button id="cancel-delete-btn" class="bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-full hover:bg-gray-300">Batal</button>
      <button id="confirm-delete-btn" class="bg-red-600 text-white font-semibold px-6 py-2 rounded-full hover:bg-red-700">Hapus</button>
    </div>
  </div>
</div>
{{-- ===== END: Delete Modal ===== --}}
@endsection

@push('scripts')
{{-- ===== START: Delete Modal Script ===== --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  const deleteModal = document.getElementById('delete-modal');
  const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
  const confirmDeleteBtn = document.getElementById('confirm-delete-btn');

  let formToSubmit = null;
  let itemElementToRemove = null;

  function openDeleteModal(formId, itemContainerSelector, buttonElement) {
    formToSubmit = document.getElementById(formId);
    itemElementToRemove = (buttonElement && itemContainerSelector)
      ? buttonElement.closest(itemContainerSelector)
      : null;

    if (formToSubmit && itemElementToRemove) {
      deleteModal.classList.remove('hidden');
      deleteModal.classList.add('flex');
      deleteModal.setAttribute('aria-hidden', 'false');
    } else {
      console.error('Form hapus atau container item tidak ditemukan:', formId, itemContainerSelector);
    }
  }

  function closeDeleteModal() {
    deleteModal.classList.add('hidden');
    deleteModal.classList.remove('flex');
    deleteModal.setAttribute('aria-hidden', 'true');
    formToSubmit = null;
    itemElementToRemove = null;
    if (confirmDeleteBtn) confirmDeleteBtn.disabled = false;
  }

  // post delete buttons
  document.querySelectorAll('.delete-post-btn').forEach(button => {
    button.addEventListener('click', (e) => {
      const formId = e.currentTarget.dataset.targetForm;
      const containerSelector = e.currentTarget.dataset.itemContainer || '.post-item-container';
      openDeleteModal(formId, containerSelector, e.currentTarget);
    });
  });

  // story delete buttons
  document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', (e) => {
      const formId = e.currentTarget.dataset.targetForm;
      const containerSelector = e.currentTarget.dataset.itemContainer || '.my-story-card'; // fix fallback
      openDeleteModal(formId, containerSelector, e.currentTarget);
    });
  });

  // cancel
  cancelDeleteBtn?.addEventListener('click', (e) => {
    e.preventDefault();
    closeDeleteModal();
  });

  // confirm
  confirmDeleteBtn?.addEventListener('click', (e) => {
    e.preventDefault();
    if (!formToSubmit) return;
    confirmDeleteBtn.disabled = true;

    if (typeof formToSubmit.requestSubmit === 'function') formToSubmit.requestSubmit();
    else formToSubmit.submit();

    if (itemElementToRemove) setTimeout(() => itemElementToRemove.remove(), 50);
    closeDeleteModal();
  });

  // click outside
  deleteModal.addEventListener('click', (e) => {
    if (e.target === deleteModal) closeDeleteModal();
  });

  // ESC
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !deleteModal.classList.contains('hidden')) closeDeleteModal();
  });
});
</script>
{{-- ===== END: Delete Modal Script ===== --}}
@endpush
