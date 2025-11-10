<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\Chapter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Validation\Rule;

class StoryController extends Controller
{
    // ===================== START: Show Story Detail =====================
    public function show($slug)
    {
        // --- START: Ambil story + tambah view ---
        $story = Story::where('slug', $slug)->firstOrFail();
        $story->increment('views');
        // --- END: Ambil story + tambah view ---

        // --- START: Hitung peringkat berdasar primary genre ---
        $rank = null;
        $primaryGenre = $story->genres[0] ?? null;
        if ($primaryGenre) {
            $rankedIds = Story::whereJsonContains('genres', $primaryGenre)
                ->orderBy('views', 'desc')
                ->pluck('id')
                ->toArray();

            $index = array_search($story->id, $rankedIds);
            if ($index !== false) $rank = $index + 1;
        }
        // --- END: Hitung peringkat berdasar primary genre ---

        // --- START: Param list & sorting ---
        $q    = request('q');
        $sort = in_array(request('sort'), ['terbaru', 'terlama']) ? request('sort') : 'terlama';
        // --- END: Param list & sorting ---

        // --- START: First chapter (untuk tombol “Mulai Membaca”) ---
        $firstChapter = $story->chapters()->orderBy('id', 'asc')->first();
        // --- END: First chapter ---

        // --- START: Query chapters (search + sort + paginate) ---
        $chapters = $story->chapters()
            ->when($q, fn($query) => $query->where('title', 'like', "%{$q}%"))
            ->orderBy('id', $sort === 'terbaru' ? 'desc' : 'asc')
            ->paginate(10)
            ->withQueryString();
        // --- END: Query chapters ---

        // --- START: Return view ---
        return view('detail', [
            'story'        => $story,
            'chapters'     => $chapters,
            'firstChapter' => $firstChapter,
            'rank'         => $rank,
            'primaryGenre' => $primaryGenre,
            'q'            => $q,
            'sort'         => $sort,
        ]);
        // --- END: Return view ---
    }
    // ===================== END: Show Story Detail =====================


    // ===================== START: Manage Chapters List =====================
    public function manage(Request $request, Story $story)
    {
        // --- START: Ambil input filter/paging/jump ---
        $q        = trim($request->input('q', ''));
        $jump     = $request->input('jump');
        $perPage  = (int) $request->input('per_page', 20);
        $perPage  = $perPage > 0 && $perPage <= 100 ? $perPage : 20;
        // --- END: Ambil input ---

        // --- START: Fitur “lompat ke bab nomor …” → hitung halaman ---
        if (filled($jump) && ctype_digit((string)$jump)) {
            $number   = (int) $jump;
            $position = $story->chapters()
                ->where('number', '<=', $number)
                ->orderBy('number')
                ->count();

            $page = (int) ceil(max(1, $position) / $perPage);

            return redirect()
                ->route('stories.manage', $story)
                ->withInput($request->except('page') + ['page' => $page]);
        }
        // --- END: Fitur lompat ke bab ---

        // --- START: Query listing bab (filter + sort + paginate) ---
        $query = $story->chapters()
            ->select('id','title','slug','status','word_count','updated_at','number')
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('title', 'like', "%{$q}%");
                    // Bisa tambahkan pencarian slug/nomor di sini bila perlu
                });
            })
            ->orderBy('number'); // Atau gunakan updated_at desc sesuai kebutuhan

        $chapters = $query->paginate($perPage)->appends($request->query());
        // --- END: Query listing bab ---

        // --- START: Return view ---
        return view('atur_cerita', compact('story', 'chapters'));
        // --- END: Return view ---
    }
    // ===================== END: Manage Chapters List =====================


    // ===================== START: Show Chapter Page (Reader) =====================
    public function showChapter($storySlug, $chapterSlug)
    {
        // (Saat ini hanya mengembalikan view dummy “bab”)
        return view('bab'); 
    }
    // ===================== END: Show Chapter Page =====================


    // ===================== START: Form Create Story =====================
    public function create()
    {
        return view('add_karya'); 
    }
    // ===================== END: Form Create Story =====================


    // ===================== START: Store New Story =====================
    public function store(Request $request)
    {
        // --- START: Validasi input ---
        $validated = $request->validate([
            'title'        => ['required','string','max:255'],
            'synopsis'     => ['required','string'],
            'author_name'  => ['required','string','max:255'],

            // cover required → ubah ke nullable jika ingin opsional
            'cover_image_data' => ['required','string'],

            // dropdown sesuai opsi form
            'type'   => ['required', Rule::in(['Novel Web','Cerpen','Light Novel','Ebook/Novel Fisik'])],
            'status' => ['required', Rule::in(['Berlanjut','Tamat','Hiatus','Drop'])],

            // genres: array of string (maks 3 akan dicek di bawah)
            'genres'   => ['nullable','array'],
            'genres.*' => ['string','max:100'],

            // field opsional
            'artist'            => ['nullable','string','max:255'],
            'translator'        => ['nullable','string','max:255'],
            'proofreader'       => ['nullable','string','max:255'],
            'original_language' => ['nullable','string','max:100'],
        ]);
        // --- END: Validasi input ---

        // --- START: Validasi batas maksimal 3 genre ---
        if (!empty($validated['genres']) && count($validated['genres']) > 3) {
            return back()
                ->withErrors(['genres' => 'Maksimal pilih 3 genre.'])
                ->withInput();
        }
        // --- END: Validasi maksimal 3 genre ---

        // --- START: Simpan cover dari data URL base64 ---
        $coverPath = null;
        if (!empty($validated['cover_image_data'])) {
            $dataUrl = $validated['cover_image_data'];

            if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $m)) {
                $ext    = strtolower($m[1]); // png|jpeg|jpg|webp|gif
                $base64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
            } else {
                // fallback ke jpg bila header tidak sesuai
                $ext    = 'jpg';
                $base64 = $dataUrl;
            }

            $imageData = base64_decode($base64, true);
            if ($imageData === false) {
                return back()
                    ->withErrors(['cover_image_data' => 'Format gambar tidak valid.'])
                    ->withInput();
            }

            if ($ext === 'jpeg') $ext = 'jpg';

            $filename = 'cover-'.Str::random(10).'-'.time().'.'.$ext;
            Storage::disk('public')->put('covers/'.$filename, $imageData);
            $coverPath = 'covers/'.$filename;
        }
        // --- END: Simpan cover dari data URL base64 ---

        // --- START: Create story ---
        $story = Story::create([
            'user_id'          => auth()->id(),
            'title'            => $validated['title'],
            'slug'             => Str::slug($validated['title']),
            'synopsis'         => $validated['synopsis'],
            'author_name'      => $validated['author_name'],
            'cover_image_path' => $coverPath,
            'genres'           => $validated['genres'] ?? [],
            'type'             => $validated['type'],
            'status'           => $validated['status'],

            'artist'           => $validated['artist'] ?? null,
            'translator'       => $validated['translator'] ?? null,
            'proofreader'      => $validated['proofreader'] ?? null,
            'origin'           => $validated['original_language'] ?? null,
        ]);
        // --- END: Create story ---

        // --- START: Redirect sukses ---
        return redirect()
            ->route('workdesk')
            ->with('success', 'Cerita baru berhasil diterbitkan!');
        // --- END: Redirect sukses ---
    }
    // ===================== END: Store New Story =====================


    // ===================== START: Edit Story Form =====================
    public function edit(Story $story)
    {
        if ($story->user_id !== auth()->id()) abort(403);
        return view('edit_karya', compact('story'));
    }
    // ===================== END: Edit Story Form =====================


    // ===================== START: Update Story =====================
    public function update(Request $request, Story $story)
    {
        // --- START: Otorisasi pemilik story ---
        if ($story->user_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan mengedit karya ini.');
        }
        // --- END: Otorisasi ---

        // --- START: Validasi input ---
        $validated = $request->validate([
            'title'           => ['required','string','max:255', Rule::unique('stories','title')->ignore($story->id)],
            'synopsis'        => 'required|string',
            'author_name'     => 'required|string|max:255',
            'cover_image_data'=> 'nullable|string',
            'genres'          => 'nullable|array',
            'genres.*'        => 'string|max:50',
            'type'            => 'required|string|in:Novel Web,Cerpen,Light Novel,Ebook/Novel Fisik',
            'status'          => 'required|string|in:Berlanjut,Tamat,Hiatus,Drop',
            'origin'          => 'required|string|in:Jepang,Cina,Korea Selatan,Bahasa Lainnya',
            'artist'          => 'nullable|string|max:255',
            'translator'      => 'nullable|string|max:255',
            'proofreader'     => 'nullable|string|max:255',
        ]);
        // --- END: Validasi input ---

        // --- START: Kelola cover (opsional, replace lama bila ada) ---
        $coverImagePath = $story->cover_image_path;
        if (!empty($validated['cover_image_data'])) {
            try {
                @list($type, $data) = explode(';', $validated['cover_image_data']);
                @list(, $data) = explode(',', $data);
                $imageData = base64_decode($data);

                $filename = 'cover-' . Str::random(10) . '-' . time() . '.jpg';
                $path = 'covers/' . $filename;
                Storage::disk('public')->put($path, $imageData);

                if ($story->cover_image_path) {
                    Storage::disk('public')->delete($story->cover_image_path);
                }
                $coverImagePath = $path;
            } catch (\Exception $e) {
                report($e);
            }
        }
        // --- END: Kelola cover ---

        // --- START: Update data story ---
        $story->update([
            'title'            => $validated['title'],
            'slug'             => Str::slug($validated['title']),
            'synopsis'         => $validated['synopsis'],
            'author_name'      => $validated['author_name'],
            'artist'           => $validated['artist'] ?? null,
            'translator'       => $validated['translator'] ?? null,
            'proofreader'      => $validated['proofreader'] ?? null,
            'origin'           => $validated['origin'], // wajib ada, tidak NULL
            'cover_image_path' => $coverImagePath,
            'genres'           => $validated['genres'] ?? [],
            'type'             => $validated['type'],
            'status'           => $validated['status'],
        ]);
        // --- END: Update data story ---

        // --- START: Redirect sukses ---
        return redirect()
            ->route('stories.manage', $story)
            ->with('success', 'Karya berhasil diperbarui!');
        // --- END: Redirect sukses ---
    }
    // ===================== END: Update Story =====================


    // ===================== START: Destroy Story =====================
    public function destroy(Story $story)
    {
        // --- START: Otorisasi ---
        if ($story->user_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan menghapus karya ini.');
        }
        // --- END: Otorisasi ---

        // --- START: Hapus cover (jika ada) & hapus story ---
        if ($story->cover_image_path) {
            Storage::disk('public')->delete($story->cover_image_path);
        }
        $story->delete();
        // --- END: Hapus cover & story ---

        // --- START: Redirect sukses ---
        return redirect()
            ->route('workdesk')
            ->with('success', 'Karya berhasil dihapus!');
        // --- END: Redirect sukses ---
    }
    // ===================== END: Destroy Story =====================
}
