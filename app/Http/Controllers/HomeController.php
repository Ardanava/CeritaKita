<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Models\Story;
use App\Models\Post;

class HomeController extends Controller
{ 
    // ===================== START: Home (Landing) =====================
    public function index(Request $request)
    { 
        // --- START: Ambil parameter pencarian (untuk UI: search bar di beranda) ---
        $searchTerm = $request->input('search'); 
        // --- END: Ambil parameter pencarian ---

        // --- START: Section UI: POPULAR STORIES (kartu “Paling Populer”) ---
        // catatan: $popularStories awal di-set 3 lalu dioverride menjadi 9 (disatukan saja)
        $popularStories = Story::with('chapters')
            ->orderByDesc('views')
            ->take(9)
            ->get();
        // --- END: Section UI: POPULAR STORIES ---

        // --- START: Section UI: NEW STORIES (grid “Cerita Terbaru”, support pencarian + pagination) ---
        $newStoriesQuery = Story::with('chapters')
            ->when($searchTerm, function ($query, $term) { 
                return $query->where('title', 'like', '%' . $term . '%');
            })
            ->latest();  

        $newStories = $newStoriesQuery
            ->paginate(9)
            ->withQueryString();
        // --- END: Section UI: NEW STORIES ---

        // --- START: Section UI: DEVELOPER POSTS (feed pengumuman/pembaruan) ---
        $developerPosts = Post::query()
            // jika guest → tampilkan yang public saja
            ->when(!auth()->check(), fn($q) => $q->where('visibility', 'public'))
            ->orderByDesc('is_pinned')  // pin di atas
            ->orderByDesc('priority')   // urutkan prioritas
            ->latest()                  // lalu terbaru
            ->limit(5)
            ->get();
        // --- END: Section UI: DEVELOPER POSTS ---

        // --- START: Render view beranda ---
        return view('home', [
            'popularStories' => $popularStories,
            'newStories'     => $newStories,
            'developerPosts' => $developerPosts,
        ]);
        // --- END: Render view beranda ---
    } 
    // ===================== END: Home (Landing) =====================


    // ===================== START: Proyek Kami (Listing + Filter) =====================
    public function proyekKami(Request $request)
    {
        // --- START: Ambil parameter filter/sort dari UI (top filter bar) ---
        $q            = trim($request->query('q', ''));
        $status       = $request->query('status', 'all');        // all|ongoing|completed|hiatus|dropped
        $genre        = $request->query('genre', 'all');         // all|<nama-genre>
        $minChapters  = (int) $request->query('min_chapters', 0);
        $maxChapters  = (int) $request->query('max_chapters', 999);
        $sort         = $request->query('sort', 'latest');       // latest|chapters|title_az
        // --- END: Ambil parameter filter/sort ---

        // --- START: Query dasar + eager load (untuk kartu proyek) ---
        $query = Story::query()
            ->with(['user'])
            ->withCount('chapters');
        // --- END: Query dasar ---

        // --- START: Filter: Pencarian judul/penulis (UI: input search) ---
        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                   ->orWhere('author_name', 'like', "%{$q}%")
                   ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$q}%"));
            });
        }
        // --- END: Filter: Pencarian ---

        // --- START: Filter: Status (UI: dropdown status) ---
        if ($status !== 'all') {
            // mapping agar fleksibel dengan data yang tersimpan
            $map = [
                'ongoing'   => ['ongoing', 'berlanjut', 'Berlanjut'],
                'completed' => ['completed', 'tamat', 'Tamat'],
                'hiatus'    => ['hiatus', 'Hiatus'],
                'dropped'   => ['dropped', 'drop', 'Drop'],
            ];
            $values = $map[$status] ?? [$status];
            $query->whereIn('status', $values);
        }
        // --- END: Filter: Status ---

        // --- START: Filter: Genre (UI: dropdown genre) ---
        if ($genre !== 'all') {
            // Kolom 'genres' bertipe JSON array → gunakan whereJsonContains
            $query->whereJsonContains('genres', $genre);
            // NOTE: Jika genres disimpan sebagai CSV string, ganti dengan:
            // $query->where('genres', 'like', "%{$genre}%");
        }
        // --- END: Filter: Genre ---

        // --- START: Filter: Rentang jumlah bab (UI: range min/max chapters) ---
        $query->havingBetween('chapters_count', [$minChapters, $maxChapters]);
        // --- END: Filter: Rentang jumlah bab ---

        // --- START: Sorting (UI: dropdown urutkan) ---
        switch ($sort) {
            case 'chapters':
                $query->orderByDesc('chapters_count')->orderByDesc('updated_at');
                break;
            case 'title_az':
                $query->orderBy('title');
                break;
            default: // latest
                $query->orderByDesc('updated_at');
        }
        // --- END: Sorting ---

        // --- START: Pagination hasil (UI: pagination bawah) ---
        $stories = $query->paginate(12)->appends($request->query());
        // --- END: Pagination ---

        // --- START: Sumber daftar genre untuk dropdown (UI: filter genre) ---
        // Ambil distinct genre dari DB (baik JSON array maupun CSV)
        $allGenres = Story::query()
            ->select('genres')
            ->whereNotNull('genres')
            ->get()
            ->flatMap(function ($s) {
                $g = $s->genres;
                if (is_string($g)) { // jika CSV
                    return collect(array_map('trim', explode(',', $g)));
                }
                return collect($g ?? []);
            })
            ->unique()
            ->sort()
            ->values()
            ->toArray();
        // --- END: Sumber daftar genre ---

        // --- START: Render view proyek-kami ---
        return view('projects', [
            'stories'    => $stories,
            'allGenres'  => $allGenres,
        ]);
        // --- END: Render view proyek-kami ---
    }
    // ===================== END: Proyek Kami (Listing + Filter) =====================
}
