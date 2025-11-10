@extends('layouts.app')

@section('title', 'Beranda - CeritaKita')

@push('styles')
<style>
  /* utils */
  .line-clamp-2{overflow:hidden;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2}
  .line-clamp-3{overflow:hidden;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:3}
  .card-min-h{min-height:220px}

  /* dots */
  #pop-cards [data-dots]{display:flex;align-items:center;gap:.5rem;position:relative;z-index:1}
  #pop-cards .dots-wrap{display:inline-flex;align-items:center;gap:.5rem;padding:.35rem .55rem;border-radius:9999px;background:rgba(255,255,255,.92);border:1px solid rgba(17,24,39,.08);backdrop-filter:saturate(120%) blur(6px)}
  #pop-cards [data-dots] button{width:.6rem;height:.6rem;border-radius:9999px;background:#cbd5e1;border:2px solid #fff;outline:1px solid rgba(0,0,0,.08);box-shadow:none;transition:transform .18s,outline-color .18s,background-color .18s,opacity .18s;opacity:.9}
  #pop-cards [data-dots] button[aria-current="true"]{background:#111827;outline:1px solid rgba(0,0,0,.25);transform:scale(1.06);opacity:1}

  /* nav */
  #pop-cards .pop-prev,#pop-cards .pop-next{box-shadow:0 6px 12px rgba(0,0,0,.12);z-index:0}
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-8">
  <div class="lg:flex lg:gap-8">

    {{-- ===== START: MAIN ===== --}}
    <main class="lg:w-2/3">

      @if($popularStories->isNotEmpty())
      {{-- ===== START: POPULER MINGGU INI ===== --}}
      <section class="mb-16 relative">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-3xl font-bold">Populer Minggu Ini</h2>
        </div>

        <div id="pop-cards" class="relative">
          {{-- ---- START: Viewport ---- --}}
          <div class="overflow-hidden rounded-3xl shadow-2xl border border-gray-100 bg-white">
            {{-- ---- START: Track ---- --}}
            <div class="flex transition-transform duration-500 ease-out" data-track>
              @foreach ($popularStories as $s)
                @php
                  $primaryGenre = $s->genres[0] ?? 'Umum';
                  $statusBadge  = $s->status === 'Berlanjut'
                                  ? ['text'=>'Berlanjut','bg'=>'bg-green-100','fg'=>'text-green-700']
                                  : ['text'=>'Tamat','bg'=>'bg-gray-100','fg'=>'text-gray-700'];
                  $firstChapter  = $s->chapters()->orderBy('id','asc')->first();
                  $latestChapter = $s->chapters()->orderBy('id','desc')->first();
                  $chapCount     = $s->chapters()->count();
                  $cover = $s->cover_image_path ? Storage::url($s->cover_image_path)
                          : 'https://placehold.co/320x480/60A5FA/FFFFFF?text=' . urlencode(Str::limit($s->title, 12));
                  $rank = null;
                  if ($primaryGenre) {
                    $ids = \App\Models\Story::whereJsonContains('genres', $primaryGenre)->orderBy('views','desc')->pluck('id')->toArray();
                    $idx = array_search($s->id, $ids);
                    $rank = $idx !== false ? $idx+1 : null;
                  }
                  $synopsis = Str::limit(strip_tags($s->synopsis ?? ''), 190);
                @endphp

                {{-- ---- START: Slide ---- --}}
                <div class="min-w-full p-3">
                  <div class="rounded-2xl p-5 md:p-7 lg:p-8 card-min-h">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 items-start">

                      {{-- ~~ START: Cover ~~ --}}
                      <div class="md:col-span-1">
                        <div class="w-28 md:w-32 mx-auto md:mx-0">
                          <div class="aspect-[2/3] w-full overflow-hidden rounded-xl ring-1 ring-gray-100 shadow-md">
                            <img src="{{ $cover }}" alt="Sampul {{ $s->title }}" class="w-full h-full object-cover" loading="lazy">
                          </div>
                        </div>
                      </div>
                      {{-- ~~ END: Cover ~~ --}}

                      {{-- ~~ START: Info ~~ --}}
                      <div class="md:col-span-4 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                          <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-blue-100 text-blue-700">{{ $primaryGenre }}</span>
                          <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full {{ $statusBadge['bg'] }} {{ $statusBadge['fg'] }}">{{ $statusBadge['text'] }}</span>
                          @if($rank)
                            <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-bold rounded-full bg-amber-100 text-amber-700">
                              <i class="fa-solid fa-fire-flame-curved mr-1"></i>#{{ $rank }} <span class="font-normal ml-1">dalam {{ $primaryGenre }}</span>
                            </span>
                          @endif
                        </div>

                        <h3 class="text-[22px] md:text-3xl font-extrabold text-gray-900 mb-1 truncate">{{ $s->title }}</h3>

                        <p class="text-sm text-gray-700">
                          @if($latestChapter)
                            <span class="font-semibold">Bab Terbaru:</span> {{ $latestChapter->title }}
                          @endif
                        </p>
                        <p class="text-sm text-gray-500 mb-3">oleh {{ $s->author_name }}</p>

                        @if($synopsis)
                          <p class="text-gray-700 text-sm md:text-base line-clamp-3 mb-4">{{ $synopsis }}</p>
                        @endif

                        {{-- :: START: Metrics :: --}}
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-600 mb-5">
                          <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-eye"></i>{{ number_format($s->views, 0, ',', '.') }} kali dibaca</span>
                          <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-layer-group"></i>{{ $chapCount }} bab</span>
                          @if($latestChapter)
                            <span class="inline-flex items-center gap-1.5"><i class="fa-regular fa-clock"></i>{{ ($latestChapter->updated_at ?? $latestChapter->created_at)->diffForHumans() }}</span>
                          @endif
                        </div>
                        {{-- :: END: Metrics :: --}}

                        {{-- :: START: Actions :: --}}
                        <div class="flex flex-wrap items-center gap-3">
                          @if($firstChapter)
                            <a href="{{ route('stories.chapter', ['storySlug' => $s->slug, 'chapterSlug' => $firstChapter->slug]) }}" class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-5 py-2.5 rounded-full hover:bg-blue-700 transition"><i class="fa-solid fa-book-open"></i> Mulai</a>
                          @endif
                          <a href="{{ route('stories.show', $s->slug) }}" class="inline-flex items-center gap-2 bg-gray-100 text-gray-800 font-semibold px-5 py-2.5 rounded-full hover:bg-gray-200 transition"><i class="fa-solid fa-circle-info"></i> Detail</a>
                        </div>
                        {{-- :: END: Actions :: --}}
                      </div>
                      {{-- ~~ END: Info ~~ --}}

                    </div>
                  </div>
                </div>
                {{-- ---- END: Slide ---- --}}
              @endforeach
            </div>
            {{-- ---- END: Track ---- --}}
          </div>
          {{-- ---- END: Viewport ---- --}}

          @if($popularStories->count() > 1)
            {{-- ---- START: Controls & Dots ---- --}}
            <button type="button" class="pop-prev absolute -left-4 md:-left-5 top-1/2 -translate-y-1/2 w-10 h-10 md:w-11 md:h-11 rounded-full bg-white grid place-items-center hover:bg-gray-50"><i class="fa-solid fa-chevron-left"></i></button>
            <button type="button" class="pop-next absolute -right-4 md:-right-5 top-1/2 -translate-y-1/2 w-10 h-10 md:w-11 md:h-11 rounded-full bg-white grid place-items-center hover:bg-gray-50"><i class="fa-solid fa-chevron-right"></i></button>

            <div class="flex items-center justify-center mt-4">
              <div class="dots-wrap" data-dots></div>
            </div>
            {{-- ---- END: Controls & Dots ---- --}}
          @endif
        </div>
      </section>
      {{-- ===== END: POPULER MINGGU INI ===== --}}
      @endif

      {{-- ===== START: BARU DIUNGGAH ===== --}}
      <section class="mt-16">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
          <h2 class="text-3xl font-bold">Baru Diunggah</h2>

          {{-- ---- START: Search ---- --}}
          <form method="GET" action="{{ route('home') }}" class="relative w-full sm:w-auto" id="home-search-form">
            <input type="text" id="home-search-input" name="search" autocomplete="off" value="{{ request('search') }}" placeholder="Cari judul cerita..." class="w-full sm:w-64 bg-white border border-gray-300 rounded-full py-2 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <i class="fa-solid fa-search text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
            <button type="submit" class="hidden"></button>
          </form>
          {{-- ---- END: Search ---- --}}
        </div>

        {{-- ---- START: Story List ---- --}}
        <div id="story-list" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          @forelse ($newStories as $story)
            @php
              $cover = $story->cover_image_path ? Storage::url($story->cover_image_path)
                      : 'https://placehold.co/96x128/60A5FA/FFFFFF?text=' . urlencode(Str::limit($story->title, 8));
              $primaryGenre = $story->genres[0] ?? 'Umum';
              $latest       = $story->chapters()->orderBy('id','desc')->first();
              $latestTitle  = $latest?->title ?? 'Belum ada bab';
              $latestWhen   = ($latest?->updated_at ?? $latest?->created_at ?? $story->created_at)->diffForHumans();
              $groupLabel   = $story->artist ?: 'No Group';
              $chapCount    = $story->chapters()->count();
            @endphp

            {{-- :: START: Story Card :: --}}
            <a href="{{ route('stories.show', $story->slug) }}" class="group bg-white rounded-xl border border-gray-200 hover:border-gray-300 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">
              <div class="flex items-start p-4 gap-4">
                {{-- ~~ START: Thumb ~~ --}}
                <div class="relative flex-shrink-0">
                  <img src="{{ $cover }}" alt="Sampul {{ $story->title }}" class="w-16 h-20 md:w-20 md:h-24 object-cover rounded-lg ring-1 ring-gray-200 group-hover:ring-gray-300">
                </div>
                {{-- ~~ END: Thumb ~~ --}}

                {{-- ~~ START: Meta ~~ --}}
                <div class="min-w-0 flex-1">
                  <h3 class="text-lg md:text-xl font-extrabold text-gray-900 leading-snug truncate">{{ $story->title }}</h3>

                  <div class="mt-1 flex items-center gap-2 text-gray-700">
                    <span class="inline-flex items-center text-xs font-semibold bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">{{ $primaryGenre }}</span>
                    @if($latest)
                      <span class="text-sm truncate"><span class="font-semibold">Ch. {{ $latest->id }}</span><span class="text-gray-500">— {{ Str::limit(strip_tags($latestTitle), 40) }}</span></span>
                    @endif
                  </div>

                  <div class="mt-1 text-sm text-gray-500 truncate">{{ $groupLabel }}</div>

                  <div class="mt-2 flex items-center justify-between text-sm text-gray-500">
                    <div class="flex items-center gap-3">
                      <span class="inline-flex items-center"><i class="fa-solid fa-eye mr-1.5 text-gray-400"></i>{{ number_format($story->views, 0, ',', '.') }}</span>
                      <span class="hidden sm:inline-flex items-center"><i class="fa-solid fa-layer-group mr-1.5 text-gray-400"></i>{{ $chapCount }} bab</span>
                    </div>
                    <div class="flex items-center gap-2">
                      <span class="whitespace-nowrap">{{ $latestWhen }}</span>
                      <i class="fa-regular fa-comment text-gray-400"></i>
                    </div>
                  </div>
                </div>
                {{-- ~~ END: Meta ~~ --}}
              </div>
            </a>
            {{-- :: END: Story Card :: --}}
          @empty
            {{-- :: START: Empty State :: --}}
            <div class="lg:col-span-2 text-center text-gray-500 py-10">
              <i class="fa-solid fa-ghost text-4xl mb-4"></i>
              <p class="font-semibold">Waduh, kosong!</p>
              @if(request('search'))
                <p>Tidak ada cerita baru yang cocok dengan pencarian Anda.</p>
              @else
                <p>Belum ada cerita yang diunggah.</p>
              @endif
            </div>
            {{-- :: END: Empty State :: --}}
          @endforelse
        </div>
        {{-- ---- END: Story List ---- --}}

        {{-- ---- START: Pagination ---- --}}
        <div class="p-4 mt-8 border-t flex justify-center">
          @if ($newStories instanceof \Illuminate\Pagination\LengthAwarePaginator && $newStories->hasPages())
            <nav class="flex items-center gap-2" aria-label="Paginasi">
              @if ($newStories->onFirstPage())
                <span class="px-3 py-2 rounded-lg bg-gray-200 text-gray-500 font-semibold shadow-sm cursor-not-allowed" aria-disabled="true">&laquo;</span>
              @else
                <a href="{{ $newStories->previousPageUrl() }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm" rel="prev">&laquo;</a>
              @endif

              @foreach ($newStories->links()->elements as $element)
                @if (is_string($element))
                  <span class="px-3 py-2 rounded-lg bg-gray-200 text-gray-500 font-semibold shadow-sm cursor-not-allowed">{{ $element }}</span>
                @endif
                @if (is_array($element))
                  @foreach ($element as $page => $url)
                    @if ($page == $newStories->currentPage())
                      <span class="px-3 py-2 rounded-lg bg-blue-600 text-white font-semibold shadow-md" aria-current="page">{{ $page }}</span>
                    @else
                      <a href="{{ $url }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm">{{ $page }}</a>
                    @endif
                  @endforeach
                @endif
              @endforeach

              @if ($newStories->hasMorePages())
                <a href="{{ $newStories->nextPageUrl() }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 shadow-sm" rel="next">&raquo;</a>
              @else
                <span class="px-3 py-2 rounded-lg bg-gray-200 text-gray-500 font-semibold shadow-sm cursor-not-allowed" aria-disabled="true">&raquo;</span>
              @endif
            </nav>
          @endif
        </div>
        {{-- ---- END: Pagination ---- --}}
      </section>
      {{-- ===== END: BARU DIUNGGAH ===== --}}
    </main>
    {{-- ===== END: MAIN ===== --}}

    {{-- ===== START: SIDEBAR ===== --}}
    <aside class="lg:w-1/3 mt-16 lg:mt-0">
      <div class="sticky top-24 space-y-8">

        {{-- ===== START: Papan Info ===== --}}
        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="font-bold text-xl text-blue-600 flex items-center gap-2"><i class="fa-solid fa-bullhorn"></i> Papan Info</h3>
            @if (Route::has('posts.index'))
              <a href="{{ route('posts.index') }}" class="text-blue-600 text-sm font-semibold hover:underline inline-flex items-center gap-1">Lihat semua <i class="fa-solid fa-arrow-right-long"></i></a>
            @endif
          </div>

          {{-- ---- START: Post List ---- --}}
          <ul class="divide-y divide-gray-200">
            @forelse ($developerPosts as $post)
              @php
                $type = strtolower($post->type ?? 'info');
                switch ($type) {
                  case 'announcement': case 'pengumuman': $accent='bg-indigo-500'; $chip='bg-indigo-100 text-indigo-700'; $icon='fa-bullhorn'; $label='Pengumuman'; break;
                  case 'update': case 'pembaruan': $accent='bg-green-500'; $chip='bg-green-100 text-green-700'; $icon='fa-arrows-rotate'; $label='Pembaruan'; break;
                  case 'maintenance': case 'perawatan': $accent='bg-amber-500'; $chip='bg-amber-100 text-amber-700'; $icon='fa-screwdriver-wrench'; $label='Perawatan'; break;
                  default: $accent='bg-blue-500'; $chip='bg-gray-100 text-gray-700'; $icon='fa-circle-info'; $label='Info'; break;
                }
                $isPinned = (bool) ($post->is_pinned ?? $post->pinned_at);
              @endphp

              {{-- :: START: Post Item :: --}}
              <li class="group hover:bg-gray-50/60 transition-colors">
                <a href="{{ route('posts.show', $post) }}" class="flex items-center">
                  <div class="w-1 self-stretch {{ $accent }}"></div>
                  <div class="flex-1 p-4">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $chip }}"><i class="fa-solid {{ $icon }}"></i> {{ $label }}</span>
                      @if($isPinned)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold rounded-full bg-amber-100 text-amber-700"><i class="fa-solid fa-thumbtack"></i> Disematkan</span>
                      @endif
                      @if(!empty($post->version))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold rounded-full bg-blue-100 text-blue-700"><i class="fa-solid fa-tag"></i> v{{ $post->version }}</span>
                      @endif
                    </div>
                    <h4 class="font-semibold text-gray-900 text-base leading-snug truncate">{{ $post->title }}</h4>
                    <div class="mt-1 flex items-center gap-3 text-xs text-gray-500">
                      <span class="inline-flex items-center gap-1"><i class="fa-regular fa-clock"></i> {{ $post->created_at->diffForHumans() }}</span>
                    </div>
                  </div>
                  <div class="pr-4 text-gray-400 group-hover:text-gray-500"><i class="fa-solid fa-chevron-right"></i></div>
                </a>
              </li>
              {{-- :: END: Post Item :: --}}
            @empty
              {{-- :: START: Empty Post :: --}}
              <li class="px-6 py-10 text-center text-gray-500">
                <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 mb-2"><i class="fa-regular fa-bell"></i></div>
                <p class="font-medium">Tidak ada info terbaru.</p>
                <p class="text-sm">Pantau kembali nanti ya.</p>
              </li>
              {{-- :: END: Empty Post :: --}}
            @endforelse
          </ul>
          {{-- ---- END: Post List ---- --}}
        </div>
        {{-- ===== END: Papan Info ===== --}}

        {{-- ===== START: Dukung Kami ===== --}}
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
          <h3 class="font-bold text-xl mb-4 text-green-600 flex items-center"><i class="fas fa-hand-holding-heart mr-3"></i>Dukung Kami</h3>
          <p class="text-gray-600 mb-4 text-sm">Dukungan Anda membantu kami untuk terus berkembang!</p>
          <div class="flex flex-col space-y-2">
            <a href="#" class="bg-red-500 text-white font-bold py-2 px-3 text-sm rounded-lg hover:bg-red-600 transition-colors flex items-center justify-center gap-2"><i class="fa-solid fa-mug-hot"></i> Trakteer</a>
            <a href="#" class="bg-blue-500 text-white font-bold py-2 px-3 text-sm rounded-lg hover:bg-blue-600 transition-colors flex items-center justify-center gap-2"><i class="fa-solid fa-coffee"></i> Ko-fi</a>
          </div>
        </div>
        {{-- ===== END: Dukung Kami ===== --}}

      </div>
    </aside>
    {{-- ===== END: SIDEBAR ===== --}}

  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const root = document.getElementById('pop-cards');
  if(!root) return;

  const track  = root.querySelector('[data-track]');
  const dotsEl = root.querySelector('[data-dots]');
  const prev   = root.querySelector('.pop-prev');
  const next   = root.querySelector('.pop-next');

  const realSlides = Array.from(track.children);
  if (realSlides.length <= 1){
    if (prev) prev.style.display='none';
    if (next) next.style.display='none';
    if (dotsEl) dotsEl.style.display='none';
    return;
  }

  // clones (infinite)
  const firstClone = realSlides[0].cloneNode(true);
  const lastClone  = realSlides[realSlides.length-1].cloneNode(true);
  track.insertBefore(lastClone, realSlides[0]);
  track.appendChild(firstClone);

  let index = 1;          // 0..N+1 (with clones)
  let timer = null;
  const N = realSlides.length;

  const slideWidth = () => {
    const s = track.children[0];
    const r = s.getBoundingClientRect();
    const cs = getComputedStyle(s);
    return r.width + parseFloat(cs.marginLeft||0) + parseFloat(cs.marginRight||0);
  };

  const sizeSlides = () => Array.from(track.children).forEach(s => s.style.minWidth='100%');

  const translate = (i, withTransition=true) => {
    const w = slideWidth();
    if (!w) { requestAnimationFrame(()=>translate(i, withTransition)); return; } // layout not ready
    track.style.transition = withTransition ? 'transform 500ms ease' : 'none';
    track.style.transform  = `translateX(${-w*i}px)`;
  };

  const wrapToRealRange = (i) => {
    // keep in [1..N] even after long tab-hidden jumps
    const k = ((i-1) % N + N) % N; // 0..N-1
    return k + 1;
  };

  const realIndex = () => {
    if (index === 0) return N-1;
    if (index === N+1) return 0;
    return index-1;
  };

  const buildDots = () => {
    if(!dotsEl) return;
    dotsEl.setAttribute('role','tablist');
    dotsEl.setAttribute('aria-label','Navigasi slide populer');
    dotsEl.innerHTML = '';
    realSlides.forEach((_,i)=>{
      const b = document.createElement('button');
      b.type='button';
      b.setAttribute('role','tab');
      b.setAttribute('aria-label',`Ke slide ${i+1}`);
      b.title=`Slide ${i+1}`;
      b.addEventListener('click',()=>go(i+1));
      dotsEl.appendChild(b);
    });
    updateDots();
  };

  const updateDots = () => {
    if(!dotsEl) return;
    Array.from(dotsEl.children).forEach((d,i)=> d.setAttribute('aria-current', i===realIndex() ? 'true':'false'));
  };

  const go = (i, user=true) => {
    // normalize immediately to avoid runaway index when tab was hidden
    index = i;
    // if jump beyond clones, wrap to real range and snap without transition
    if (index < 0 || index > N+1) {
      index = wrapToRealRange(index);
      translate(index, false);
    } else {
      translate(index, true);
    }
    updateDots();
    if(user) resetAuto();
  };

  track.addEventListener('transitionend', ()=>{
    if (index === 0){ index = N; translate(index, false); }
    else if (index === N+1){ index = 1; translate(index, false); }
  });

  const start = () => { if(!timer) timer = setInterval(()=>go(index+1,false), 6000); };
  const stop  = () => { if(timer){ clearInterval(timer); timer=null; } };
  const resetAuto = () => { stop(); start(); };

  if (prev) prev.addEventListener('click', ()=>go(index-1));
  if (next) next.addEventListener('click', ()=>go(index+1));

  // swipe
  let x0=null;
  track.addEventListener('touchstart',e=>{x0=e.touches[0].clientX},{passive:true});
  track.addEventListener('touchend',e=>{
    if(x0==null) return;
    const dx=e.changedTouches[0].clientX-x0;
    if(Math.abs(dx)>40) dx<0 ? go(index+1) : go(index-1);
    x0=null;
  },{passive:true});

  // pause/resume
  root.addEventListener('mouseenter', stop);
  root.addEventListener('mouseleave', start);

  // visibility: stop timers and hard-normalize index on return
  const onVisibleResume = () => {
    index = wrapToRealRange(index);
    translate(index, false); // snap to valid slide
    updateDots();
    resetAuto();
  };
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) stop();
    else onVisibleResume();
  });
  window.addEventListener('focus', onVisibleResume);
  window.addEventListener('blur', stop);

  // init
  sizeSlides();
  translate(index, false);
  buildDots();
  start();

  // responsive snap
  let rid;
  window.addEventListener('resize', ()=>{
    clearTimeout(rid);
    rid = setTimeout(()=>translate(index,false), 120);
  });
});
</script>
@endpush
