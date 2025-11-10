@extends('layouts.app')

@section('title', $post->title)

@push('styles')
<style>
  /* === START: Quill typography === */
  .post-content{font-family:'Lora',serif;font-size:1.05rem;line-height:1.85}
  .post-content .ql-align-center{text-align:center}
  .post-content .ql-align-right{text-align:right}
  .post-content .ql-align-justify{text-align:justify}
  .post-content .ql-align-left{text-align:left}
  .post-content .ql-align-center img{display:block;margin:0 auto}
  .post-content .ql-align-right img{display:block;margin-left:auto}
  .post-content .ql-align-left img{display:block;margin-right:auto}
  .post-content blockquote{border-left:4px solid #e5e7eb;margin:12px 0;padding-left:14px;font-style:italic;color:#374151}
  /* === END: Quill typography === */

  /* === START: Chips === */
  .chip{display:inline-flex;align-items:center;gap:.5rem;padding:.25rem .65rem;border:1px solid;border-radius:9999px;font-weight:700;font-size:.78rem}
  .chip-ann{background:#eef2ff;color:#3730a3;border-color:#6366f1}
  .chip-upd{background:#ecfdf5;color:#065f46;border-color:#10b981}
  .chip-mnt{background:#fffbeb;color:#92400e;border-color:#f59e0b}
  .chip-inf{background:#eff6ff;color:#1e40af;border-color:#3b82f6}
  /* === END: Chips === */

  .meta small{color:#6b7280}

  /* === START: Tags === */
  .tag{display:inline-flex;align-items:center;gap:.4rem;padding:.25rem .6rem;border:1px solid #e5e7eb;border-radius:.75rem;background:#f9fafb;font-weight:600;font-size:.8rem;color:#374151}
  .tag i{opacity:.7}
  /* === END: Tags === */

  /* === START: Prev/Next === */
  .post-nav a{display:flex;align-items:center;gap:.75rem;padding:12px 14px;border:1px solid #e5e7eb;border-radius:.9rem;background:#fff;transition:box-shadow .15s,border-color .15s}
  .post-nav a:hover{border-color:#cbd5e1;box-shadow:0 8px 22px rgba(0,0,0,.06)}
  /* === END: Prev/Next === */
</style>
@endpush

@section('content')
@php
  // map meta
  $type = strtolower($post->type ?? 'info');
  switch ($type) {
    case 'announcement':  $chip='chip-ann'; $icon='fa-bullhorn';          $label='Pengumuman'; break;
    case 'update':        $chip='chip-upd'; $icon='fa-arrows-rotate';     $label='Pembaruan';  break;
    case 'maintenance':   $chip='chip-mnt'; $icon='fa-screwdriver-wrench';$label='Perawatan';  break;
    default:              $chip='chip-inf'; $icon='fa-circle-info';       $label='Info';
  }
  // read time
  $plain = trim(preg_replace('/\s+/', ' ', strip_tags($post->content ?? '')));
  $words = str_word_count($plain);
  $readMinutes = max(1, (int)ceil($words / 200));
  // tags
  $tags = [];
  if (!empty($post->tags)) $tags = is_array($post->tags) ? $post->tags : array_filter(array_map('trim', explode(',', $post->tags)));
  // backlink
  $backUrl = Route::has('posts.index') ? route('posts.index') : route('home');
@endphp

{{-- ===== START: Main ===== --}}
<main class="container mx-auto px-4 lg:px-8 py-10">
  <article class="max-w-3xl mx-auto">

    {{-- ---- START: Back link ---- --}}
    <div class="mb-5">
      <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:underline">
        <i class="fa-solid fa-arrow-left-long"></i> Kembali ke Papan Info
      </a>
    </div>
    {{-- ---- END: Back link ---- --}}

    {{-- ---- START: Header ---- --}}
    <header class="mb-6">
      <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="chip {{ $chip }}">
          <i class="fa-solid {{ $icon }}"></i> <span>{{ $label }}</span>
        </div>

        {{-- ~~ START: Right status ~~ --}}
        <div class="flex items-center gap-2">
          @if($post->is_pinned)
            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-300">
              <i class="fa-solid fa-thumbtack"></i> Disematkan
            </span>
          @endif
          <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 border border-gray-300">
            <i class="fa-solid {{ ($post->visibility ?? 'public') === 'private' ? 'fa-lock' : 'fa-earth-americas' }}"></i>
            {{ ($post->visibility ?? 'public') === 'private' ? 'Privat' : 'Publik' }}
          </span>
          @if(!empty($post->version))
            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 border border-blue-300">
              <i class="fa-solid fa-tag"></i> v{{ $post->version }}
            </span>
          @endif
        </div>
        {{-- ~~ END: Right status ~~ --}}
      </div>

      <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-3">{{ $post->title }}</h1>

      {{-- ~~ START: Meta ~~ --}}
      <div class="meta mt-2 flex flex-wrap items-center gap-3 text-sm">
        <small class="inline-flex items-center gap-1"><i class="fa-regular fa-user"></i>{{ $post->author ?? optional($post->user)->name ?? 'Admin' }}</small>
        <span class="text-gray-300">•</span>
        <small class="inline-flex items-center gap-1"><i class="fa-regular fa-clock"></i> {{ $post->created_at->diffForHumans() }}</small>
        <span class="text-gray-300">•</span>
        <small class="inline-flex items-center gap-1"><i class="fa-solid fa-book-open-reader"></i> {{ $readMinutes }} min read</small>
        @if(!empty($post->priority))
          <span class="text-gray-300">•</span>
          <small class="inline-flex items-center gap-1"><i class="fa-solid fa-arrow-up-wide-short"></i> Prioritas {{ $post->priority }}</small>
        @endif
      </div>
      {{-- ~~ END: Meta ~~ --}}

      {{-- ~~ START: Summary ~~ --}}
      @if(!empty($post->summary))
        <p class="mt-4 text-gray-700 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">{{ $post->summary }}</p>
      @endif
      {{-- ~~ END: Summary ~~ --}}
    </header>
    {{-- ---- END: Header ---- --}}

    {{-- ---- START: Content ---- --}}
    <div class="post-content prose prose-blue max-w-none bg-white p-6 md:p-8 rounded-xl border border-gray-200 shadow-sm">
      {!! $post->content !!}
    </div>
    {{-- ---- END: Content ---- --}}

    {{-- ---- START: Tags + Share ---- --}}
    <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
      {{-- ~~ START: Tags ~~ --}}
      @if(!empty($tags))
        <div class="flex items-center gap-2 flex-wrap">
          @foreach($tags as $t)
            <span class="tag"><i class="fa-solid fa-hashtag"></i>{{ $t }}</span>
          @endforeach
        </div>
      @endif
      {{-- ~~ END: Tags ~~ --}}

      {{-- ~~ START: Share ~~ --}}
      <div class="flex items-center gap-2">
        <span class="text-sm text-gray-500 mr-2">Bagikan:</span>
        @php $share = urlencode(request()->fullUrl()); $title = urlencode($post->title); @endphp
        <a target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url={{ $share }}&text={{ $title }}" class="tag"><i class="fa-brands fa-x-twitter"></i> X</a>
        <a target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u={{ $share }}" class="tag"><i class="fa-brands fa-facebook-f"></i> Facebook</a>
        <a target="_blank" rel="noopener" href="https://t.me/share/url?url={{ $share }}&text={{ $title }}" class="tag"><i class="fa-brands fa-telegram"></i> Telegram</a>
      </div>
      {{-- ~~ END: Share ~~ --}}
    </div>
    {{-- ---- END: Tags + Share ---- --}}

    {{-- ---- START: Prev/Next ---- --}}
    @if(isset($prev) || isset($next))
      <div class="post-nav grid grid-cols-1 md:grid-cols-2 gap-3 mt-8">
        <div>
          @if(!empty($prev))
            <a href="{{ route('posts.show', $prev) }}"><i class="fa-solid fa-arrow-left-long text-gray-500"></i><span class="truncate">{{ $prev->title }}</span></a>
          @endif
        </div>
        <div class="md:text-right">
          @if(!empty($next))
            <a href="{{ route('posts.show', $next) }}" class="justify-end"><span class="truncate">{{ $next->title }}</span><i class="fa-solid fa-arrow-right-long text-gray-500"></i></a>
          @endif
        </div>
      </div>
    @endif
    {{-- ---- END: Prev/Next ---- --}}

  </article>
</main>
{{-- ===== END: Main ===== --}}

{{-- ===== START: Donasi ===== --}}
<section class="container mx-auto px-4 lg:px-8 py-8">
  <div class="max-w-3xl mx-auto text-center bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
    <h3 class="text-xl font-bold mb-2">Dukung Kami</h3>
    <p class="text-gray-600 mb-4">Suka dengan tulisan ini? Pertimbangkan untuk berdonasi ❤️</p>
    <div class="flex justify-center items-center gap-3 flex-wrap">
      <a href="#" class="bg-red-500 text-white font-bold py-2 px-4 rounded-full hover:bg-red-600 transition-colors flex items-center gap-2"><i class="fa-solid fa-mug-hot"></i> Trakteer</a>
      <a href="#" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-full hover:bg-blue-600 transition-colors flex items-center gap-2"><i class="fa-solid fa-coffee"></i> Ko-fi</a>
    </div>
  </div>
</section>
{{-- ===== END: Donasi ===== --}}

{{-- ===== START: Komentar ===== --}}
<section id="comment-section" class="container mx-auto px-4 lg:px-8 py-10 border-t">
  <div class="max-w-3xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Komentar</h2>
    <div id="disqus_thread" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
      <p class="text-center text-gray-500">
        <i class="fa-solid fa-comments text-3xl mb-3 block"></i>
        Area ini akan dimuat oleh <strong>Disqus</strong> saat aplikasi di-deploy.
      </p>
    </div>
  </div>
</section>
{{-- ===== END: Komentar ===== --}}
@endsection
