<?php

namespace App\Http\Controllers;

use App\Models\Post;  
use Illuminate\Http\Request;
use Illuminate\Support\Str;  
use Illuminate\Support\Facades\Auth;  

class PostController extends Controller
{ 
    // ===================== START: Form Create Post =====================
    public function create()
    { 
        return view('posts.create');
    }
    // ===================== END: Form Create Post =====================


    // ===================== START: Store Post =====================
    public function store(Request $request)
    {
        // --- START: Validasi ---
        $validated = $request->validate([
            'title'      => 'required|string|max:255|unique:posts,title',
            'content'    => 'required|string',
            'type'       => 'required|in:announcement,update,maintenance,info',
            'visibility' => 'required|in:public,private',
            'is_pinned'  => 'required|in:0,1',          
            'priority'   => 'nullable|integer|min:0|max:10',
            'summary'    => 'nullable|string|max:500',
            'tags'       => 'nullable|string|max:300',  
        ]);
        // --- END: Validasi ---

        // --- START: Generate summary, excerpt, slug, tags ---
        $slug     = Str::slug($validated['title']);
        $summary  = $validated['summary'] ?: $this->autoSummary($validated['content'], 220);
        $excerpt  = Str::limit($summary, 160, '…');
        $tags     = $this->makeTags(
            $validated['title'],
            $validated['content'],
            $request->input('tags')
        );
        // --- END: Generate summary, excerpt, slug, tags ---

        // --- START: Simpan ke DB ---
        Post::create([
            'user_id'    => auth()->id(),
            'title'      => $validated['title'],
            'slug'       => $slug,
            'content'    => $validated['content'],
            'summary'    => $summary,
            'excerpt'    => $excerpt,
            'tags'       => $tags,
            'type'       => $validated['type'],
            'visibility' => $validated['visibility'],
            'is_pinned'  => (bool) ((int) $validated['is_pinned']),
            'priority'   => $validated['priority'] ?? 0,
        ]);
        // --- END: Simpan ke DB ---

        // --- START: Redirect ---
        return redirect()
            ->route('workdesk')
            ->with('success', 'Postingan baru berhasil dibuat!');
        // --- END: Redirect ---
    }
    // ===================== END: Store Post =====================


    // ===================== START: Util - Auto Summary =====================
    private function autoSummary(string $html, int $limit = 220): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
        return Str::limit($text, $limit, '…');
    }
    // ===================== END: Util - Auto Summary =====================


    // ===================== START: Util - Make Tags =====================
    /**
     * Normalisasi daftar tag.
     */
    private function makeTags(string $title, string $html, ?string $rawCsv): array
    {
        // 1) input manual CSV
        if ($rawCsv && trim($rawCsv) !== '') {
            return $this->normalizeTagCsv($rawCsv);
        }

        // 2) hashtag di konten
        $hash = $this->extractHashtags($html);
        if (!empty($hash)) return $hash;

        // 3) fallback → dari judul
        return $this->guessTagsFromTitle($title);
    }
    // ===================== END: Util - Make Tags =====================


    // ===================== START: Util - CSV Tags =====================
    private function normalizeTagCsv(string $csv): array
    {
        $parts = array_map('trim', explode(',', $csv));
        $tags  = [];

        foreach ($parts as $p) {
            if ($p === '') continue;
            $clean = trim(preg_replace('/[^\pL\pN\s\-]+/u', '', $p));
            if ($clean !== '') $tags[] = mb_strtolower($clean);
        }

        return array_slice(array_values(array_unique($tags)), 0, 10);
    }
    // ===================== END: Util - CSV Tags =====================


    // ===================== START: Util - Extract hashtags =====================
    private function extractHashtags(string $html): array
    {
        $text = strip_tags($html);
        preg_match_all('/#([\pL\pN\-]{2,})/u', $text, $m);

        if (empty($m[1])) return [];

        $tags = array_map(fn($t) => mb_strtolower($t), $m[1]);
        return array_slice(array_values(array_unique($tags)), 0, 10);
    }
    // ===================== END: Util - Extract hashtags =====================


    // ===================== START: Util - Guess Tags From Title =====================
    private function guessTagsFromTitle(string $title): array
    {
        $stop = [
            'dan','atau','yang','untuk','dengan','dari','pada','ini','itu','akan','kami','kita','di','ke','ya',
            'v','versi','update','pembaruan','rilis','release','patch','fix','bug','info','pengumuman'
        ];

        $words = preg_split('/\s+/u', mb_strtolower($title));
        $cand  = [];

        foreach ($words as $w) {
            $w = preg_replace('/[^\pL\pN\-]+/u', '', $w);
            if (mb_strlen($w) >= 4 && !in_array($w, $stop, true)) {
                $cand[] = $w;
            }
        }

        if (empty($cand)) return [];
        return array_slice(array_values(array_unique($cand)), 0, 5);
    }
    // ===================== END: Util - Guess Tags From Title =====================



    // ===================== START: Index Post List =====================
    public function index(Request $request)
    {
        $type   = $request->type;
        $search = $request->q;

        $posts = Post::query()
            ->when($type, fn($q) => $type !== 'all'
                ? $q->where('type', $type)
                : $q
            )
            ->when($search, fn($q) =>
                $q->where(function($sub) use ($search) {
                    $sub->where('title', 'like', "%$search%")
                        ->orWhere('content', 'like', "%$search%");
                })
            )
            ->orderByDesc('is_pinned')
            ->orderByDesc('priority')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('posts.index', compact('posts', 'type', 'search'));
    }
    // ===================== END: Index Post List =====================



    // ===================== START: Show Post Detail =====================
    public function show(Post $post)
    {
        // --- START: Restrict visibility ---
        if (($post->visibility ?? 'public') === 'private' && !auth()->check()) {
            abort(404);
        }
        // --- END: Restrict visibility ---

        // Eager load penulis
        $post->load('user');

        // Optional: increment views
        try { $post->increment('views'); } catch (\Throwable $e) {}

        // --- START: prev/next (only public) ---
        $prev = Post::where('id', '!=', $post->id)
            ->where('visibility', '!=', 'private')
            ->where('created_at', '<', $post->created_at)
            ->orderBy('created_at', 'desc')
            ->first();

        $next = Post::where('id', '!=', $post->id)
            ->where('visibility', '!=', 'private')
            ->where('created_at', '>', $post->created_at)
            ->orderBy('created_at', 'asc')
            ->first();
        // --- END: prev/next ---

        return view('posts.show', compact('post', 'prev', 'next'));
    }
    // ===================== END: Show Post Detail =====================



    // ===================== START: Edit Post =====================
    public function edit(Post $post)
    { 
        if ($post->user_id !== auth()->id()) abort(403);

        return view('posts.edit', compact('post'));
    }
    // ===================== END: Edit Post =====================



    // ===================== START: Update Post =====================
    public function update(Request $request, Post $post)
    {
        // --- START: Otorisasi ---
        abort_if($post->user_id !== auth()->id(), 403);
        // --- END: Otorisasi ---

        // --- START: Validasi ---
        $validated = $request->validate([
            'title'      => 'required|string|max:255|unique:posts,title,' . $post->id,
            'content'    => 'required|string',

            'type'       => 'required|string|in:announcement,update,maintenance,info',
            'visibility' => 'required|string|in:public,private',
            'is_pinned'  => 'nullable|boolean',
            'priority'   => 'nullable|integer|min:0|max:10',
            'summary'    => 'nullable|string|max:500',
            'tags'       => 'nullable|string',
        ]);
        // --- END: Validasi ---

        // --- START: Parsing pinned & tags ---
        $isPinned = (bool) ($validated['is_pinned'] ?? false);

        $tags = null;
        if (!empty($validated['tags'])) {
            $tags = collect(explode(',', $validated['tags']))
                ->map(fn ($t) => trim($t))
                ->filter()
                ->values()
                ->all();
            if (empty($tags)) $tags = null;
        }
        // --- END: Parsing pinned & tags ---

        // --- START: summary/excerpt ---
        $summary = $validated['summary'] ?? null;
        $excerpt = $summary;
        if (!$summary) {
            $plain   = trim(preg_replace('/\s+/', ' ', strip_tags($validated['content'])));
            $excerpt = Str::limit($plain, 220);
        }
        // --- END: summary/excerpt ---

        // --- START: slug baru (judul berubah = slug berubah) ---
        $slug = Str::slug($validated['title']);
        // --- END: slug ---

        // --- START: Update post DB ---
        $post->update([
            'title'      => $validated['title'],
            'slug'       => $slug,
            'content'    => $validated['content'],
            'type'       => $validated['type'],
            'visibility' => $validated['visibility'],
            'is_pinned'  => $isPinned,
            'priority'   => $validated['priority'] ?? 0,
            'summary'    => $summary,
            'excerpt'    => $excerpt,
            'tags'       => $tags,
        ]);
        // --- END: Update post DB ---

        return redirect()
            ->route('workdesk')
            ->with('success', 'Postingan berhasil diperbarui!');
    }
    // ===================== END: Update Post =====================



    // ===================== START: Destroy Post =====================
    public function destroy(Post $post)
    { 
        if ($post->user_id !== auth()->id()) abort(403);

        $post->delete(); 

        return redirect()
            ->route('workdesk')
            ->with('success', 'Postingan berhasil dihapus!');
    }
    // ===================== END: Destroy Post =====================



    // ===================== START: metaForType helper =====================
    private function metaForType(string $type): array
    {
        switch ($type) {
            case 'announcement':
                return [
                    'label' => 'Pengumuman',
                    'icon'  => 'fa-bullhorn',
                    'chip'  => 'bg-indigo-100 text-indigo-700',
                    'bar'   => 'bg-indigo-500',
                ];
            case 'update':
                return [
                    'label' => 'Pembaruan',
                    'icon'  => 'fa-arrows-rotate',
                    'chip'  => 'bg-green-100 text-green-700',
                    'bar'   => 'bg-green-500',
                ];
            case 'maintenance':
                return [
                    'label' => 'Perawatan',
                    'icon'  => 'fa-screwdriver-wrench',
                    'chip'  => 'bg-amber-100 text-amber-700',
                    'bar'   => 'bg-amber-500',
                ];
            default:
                return [
                    'label' => 'Info',
                    'icon'  => 'fa-circle-info',
                    'chip'  => 'bg-gray-100 text-gray-700',
                    'bar'   => 'bg-blue-500',
                ];
        }
    }
    // ===================== END: metaForType =====================

}
