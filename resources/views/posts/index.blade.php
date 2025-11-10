@extends('layouts.app')

@section('title', 'Papan Info - Semua Postingan')

@push('styles')
<style>
  /* utils */
  .line-clamp-2{overflow:hidden;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2}
  .line-clamp-3{overflow:hidden;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:3}
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-10">
  <div class="max-w-5xl mx-auto">

    {{-- ===== START: Header ===== --}}
    <div class="mb-8">
      <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight text-gray-900">Papan Info</h1>
      <p class="text-gray-600 mt-1">Semua pengumuman & pembaruan dari admin.</p>
    </div>
    {{-- ===== END: Header ===== --}}

    {{-- ===== START: Toolbar (Tabs + Search) ===== --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-5 mb-6">
      <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

        {{-- ---- START: Tabs tipe ---- --}}
        <div class="flex flex-wrap gap-2">
          @php
            $tab = $type ?: 'all';
            $tabs = [
              'all' => ['label'=>'Semua', 'icon'=>'fa-list-ul'],
              'announcement' => ['label'=>'Pengumuman', 'icon'=>'fa-bullhorn', 'class'=>'text-indigo-700 bg-indigo-50 ring-indigo-200'],
              'update'       => ['label'=>'Pembaruan', 'icon'=>'fa-arrows-rotate', 'class'=>'text-green-700 bg-green-50 ring-green-200'],
              'maintenance'  => ['label'=>'Perawatan', 'icon'=>'fa-screwdriver-wrench', 'class'=>'text-amber-700 bg-amber-50 ring-amber-200'],
              'info'         => ['label'=>'Info', 'icon'=>'fa-circle-info', 'class'=>'text-gray-700 bg-gray-50 ring-gray-200'],
            ];
          @endphp

          @foreach($tabs as $key => $meta)
            @php
              $active = $tab === $key;
              $base   = 'inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-semibold ring-1';
              $style  = $active
                        ? ($meta['class'] ?? 'text-blue-700 bg-blue-50 ring-blue-200')
                        : 'text-gray-600 bg-white hover:bg-gray-50 ring-gray-200';
              $urlParams = ['q' => $search ?: null, 'type' => $key === 'all' ? null : $key];
            @endphp
            <a href="{{ route('posts.index', $urlParams) }}"
               class="{{ $base }} {{ $style }}"
               aria-current="{{ $active ? 'page' : 'false' }}">
               <i class="fa-solid {{ $meta['icon'] }}"></i>{{ $meta['label'] }}
            </a>
          @endforeach
        </div>
        {{-- ---- END: Tabs tipe ---- --}}

        {{-- ---- START: Pencarian ---- --}}
        <form id="post-search-form" method="GET" action="{{ route('posts.index') }}" class="relative w-full md:w-80">
          <input type="hidden" name="type" value="{{ $type }}">
          <input type="text" name="q" value="{{ $search }}" autocomplete="off" placeholder="Cari judul atau isi…" class="w-full bg-white border border-gray-300 rounded-full py-2 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <i class="fa-solid fa-search text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
          <button type="submit" class="hidden"></button>
        </form>
        {{-- ---- END: Pencarian ---- --}}

      </div>
    </div>
    {{-- ===== END: Toolbar (Tabs + Search) ===== --}}

    {{-- ===== START: List Post ===== --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      @forelse ($posts as $post)
        @php
          $t = strtolower($post->type ?? 'info');
          switch ($t) {
            case 'announcement': $accent='bg-indigo-500'; $chip='bg-indigo-100 text-indigo-700'; $icon='fa-bullhorn';         $label='Pengumuman'; break;
            case 'update':       $accent='bg-green-500';  $chip='bg-green-100 text-green-700';  $icon='fa-arrows-rotate';    $label='Pembaruan';  break;
            case 'maintenance':  $accent='bg-amber-500';  $chip='bg-amber-100 text-amber-700';  $icon='fa-screwdriver-wrench';$label='Perawatan';  break;
            default:             $accent='bg-blue-500';   $chip='bg-gray-100 text-gray-700';    $icon='fa-circle-info';      $label='Info';       break;
          }
          $isPinned   = (bool)($post->is_pinned ?? false);
          $visibility = $post->visibility === 'private' ? 'private' : 'public';
          if ($visibility === 'private') { $accent = 'bg-gray-400'; }
          $excerpt = $post->summary ?: Str::limit(strip_tags($post->content ?? ''), 180);
        @endphp

        {{-- ---- START: Post Item ---- --}}
        <article class="group hover:bg-gray-50/60 transition-colors">
          <a href="{{ route('posts.show', $post) }}" class="flex">
            {{-- ~~ START: Accent bar ~~ --}}
            <div class="w-1 self-stretch {{ $accent }}"></div>
            {{-- ~~ END: Accent bar ~~ --}}

            {{-- ~~ START: Body ~~ --}}
            <div class="flex-1 p-5">
              {{-- :: START: Chips :: --}}
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
                  <i class="fa-solid {{ $visibility==='private' ? 'fa-lock' : 'fa-earth-americas' }}"></i>
                  {{ $visibility==='private' ? 'Privat' : 'Publik' }}
                </span>
                @if(!empty($post->version))
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold rounded-full bg-blue-100 text-blue-700">
                    <i class="fa-solid fa-tag"></i> v{{ $post->version }}
                  </span>
                @endif
              </div>
              {{-- :: END: Chips :: --}}

              {{-- :: START: Title & Excerpt :: --}}
              <h2 class="text-lg md:text-xl font-bold text-gray-900 leading-snug mb-1">{{ $post->title }}</h2>
              @if($excerpt)
                <p class="text-sm text-gray-600 line-clamp-2">{{ $excerpt }}</p>
              @endif
              {{-- :: END: Title & Excerpt :: --}}

              {{-- :: START: Meta :: --}}
              <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
                <span class="inline-flex items-center gap-1"><i class="fa-regular fa-clock"></i> {{ $post->created_at->diffForHumans() }}</span>
                @if(!empty($post->author))
                  <span class="inline-flex items-center gap-1"><i class="fa-regular fa-user"></i> {{ $post->author }}</span>
                @endif
              </div>
              {{-- :: END: Meta :: --}}
            </div>
            {{-- ~~ END: Body ~~ --}}

            {{-- ~~ START: Chevron ~~ --}}
            <div class="pr-4 py-5 text-gray-400 group-hover:text-gray-500" aria-label="Buka detail">
              <i class="fa-solid fa-chevron-right"></i>
            </div>
            {{-- ~~ END: Chevron ~~ --}}
          </a>
        </article>
        {{-- ---- END: Post Item ---- --}}

        @if(! $loop->last)
          <div class="border-t border-gray-200"></div>
        @endif
      @empty
        {{-- ---- START: Empty State ---- --}}
        <div class="px-8 py-14 text-center text-gray-500">
          <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 mb-3">
            <i class="fa-regular fa-bell"></i>
          </div>
          <p class="font-semibold">Belum ada postingan.</p>
          <p class="text-sm">Pengumuman akan tampil di sini.</p>
        </div>
        {{-- ---- END: Empty State ---- --}}
      @endforelse
    </div>
    {{-- ===== END: List Post ===== --}}

    {{-- ===== START: Paginasi ===== --}}
    @if ($posts->hasPages())
      <div class="flex justify-center mt-6">
        <nav class="flex items-center gap-2" aria-label="Paginasi">
          {{-- ~~ START: Prev ~~ --}}
          @if ($posts->onFirstPage())
            <span class="px-3 py-2 rounded-lg bg-gray-200 text-gray-500 font-semibold shadow-sm cursor-not-allowed">&laquo;</span>
          @else
            <a href="{{ $posts->previousPageUrl() }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm" rel="prev">&laquo;</a>
          @endif
          {{-- ~~ END: Prev ~~ --}}

          {{-- ~~ START: Numbers ~~ --}}
          @foreach ($posts->links()->elements as $element)
            @if (is_string($element))
              <span class="px-3 py-2 rounded-lg bg-gray-200 text-gray-500 font-semibold shadow-sm cursor-not-allowed">{{ $element }}</span>
            @endif
            @if (is_array($element))
              @foreach ($element as $page => $url)
                @if ($page == $posts->currentPage())
                  <span class="px-3 py-2 rounded-lg bg-blue-600 text-white font-semibold shadow-md" aria-current="page">{{ $page }}</span>
                @else
                  <a href="{{ $url }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm">{{ $page }}</a>
                @endif
              @endforeach
            @endif
          @endforeach
          {{-- ~~ END: Numbers ~~ --}}

          {{-- ~~ START: Next ~~ --}}
          @if ($posts->hasMorePages())
            <a href="{{ $posts->nextPageUrl() }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm" rel="next">&raquo;</a>
          @else
            <span class="px-3 py-2 rounded-lg bg-gray-200 text-gray-500 font-semibold shadow-sm cursor-not-allowed">&raquo;</span>
          @endif
          {{-- ~~ END: Next ~~ --}}
        </nav>
      </div>
    @endif
    {{-- ===== END: Paginasi ===== --}}

  </div>
</div>
@endsection

@push('scripts')
<script>
  // ===== START: Debounce pencarian =====
  document.addEventListener('DOMContentLoaded', () => {
    const form  = document.getElementById('post-search-form');
    const input = form?.querySelector('input[name="q"]');
    if (!form || !input) return;

    let t;
    input.addEventListener('input', () => {
      clearTimeout(t);
      t = setTimeout(() => form.requestSubmit(), 400);
    });
  });
  // ===== END: Debounce pencarian =====
</script>
@endpush
