<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChapterController extends Controller
{
    // ===================== START: Form Tambah Bab =====================
    public function create(Story $story)
    {
        // --- START: Otorisasi pemilik cerita ---
        if ($story->user_id !== auth()->id()) {
            abort(403);
        }
        // --- END: Otorisasi pemilik cerita ---

        return view('add_bab', compact('story'));
    }
    // ===================== END: Form Tambah Bab =====================


    // ===================== START: Simpan Bab Baru =====================
    public function store(Request $request, string $story_slug)
    {
        // --- START: Ambil story berdasarkan slug ---
        $story = Story::where('slug', $story_slug)->firstOrFail();
        // --- END: Ambil story berdasarkan slug ---

        // --- START: Otorisasi pemilik cerita ---
        if ($story->user_id !== auth()->id()) {
            abort(403);
        }
        // --- END: Otorisasi pemilik cerita ---

        // --- START: Validasi input (judul unik per story) ---
        $validated = $request->validate([
            'chapter_title'   => [
                'required','string','max:255',
                Rule::unique('chapters', 'title')->where(fn($q) => $q->where('story_id', $story->id)),
            ],
            'chapter_content' => 'required|string',   // HTML dari Quill
            'author_note'     => 'nullable|string',
            'status'          => 'required|in:draft,published',
        ]);
        // --- END: Validasi input ---

        // --- START: Normalisasi status ke format DB ---
        $status = $validated['status'] === 'published' ? 'Published' : 'Draft';
        // --- END: Normalisasi status ---

        // --- START: Buat slug unik per-story untuk bab baru ---
        $baseSlug = Str::slug($validated['chapter_title']);
        $slug     = $baseSlug;

        $existsCount = Chapter::where('story_id', $story->id)
            ->where(function ($q) use ($baseSlug, $slug) {
                $q->where('slug', $slug)
                  ->orWhere('slug', 'like', $baseSlug.'-%');
            })
            ->count();

        if ($existsCount > 0) {
            $slug = $baseSlug.'-'.($existsCount + 1);
        }
        // --- END: Slug unik per-story ---

        // --- START: Hitung statistik konten (word count) ---
        $wordCount = str_word_count(trim(strip_tags($validated['chapter_content'])));
        // --- END: Hitung statistik konten ---

        // --- START: Tentukan nomor urut bab berikutnya ---
        $nextNumber = (int) (Chapter::where('story_id', $story->id)->max('number') ?? 0) + 1;
        // --- END: Nomor urut bab ---

        // --- START: Simpan dalam transaksi ---
        DB::transaction(function () use ($story, $validated, $slug, $status, $wordCount, $nextNumber) {
            $chapter = new Chapter([
                'title'       => $validated['chapter_title'],
                'slug'        => $slug,
                'content'     => $validated['chapter_content'],      // simpan HTML utuh
                'author_note' => $validated['author_note'] ?? null,
                'status'      => $status,                             // 'Published' atau 'Draft'
                'word_count'  => $wordCount,
                'number'      => $nextNumber,                         // urutan bab
            ]);

            $story->chapters()->save($chapter);

            // Sentuh story agar waktu updated_at naik
            $story->touch();
        });
        // --- END: Simpan transaksi ---

        // --- START: Redirect dengan pesan sukses sesuai status ---
        return redirect()
            ->route('stories.manage', $story->slug)
            ->with('success', $status === 'Published'
                ? 'Bab baru berhasil diterbitkan!'
                : 'Draft bab berhasil disimpan.');
        // --- END: Redirect ---
    }
    // ===================== END: Simpan Bab Baru =====================



    // ===================== START: Form Edit Bab =====================
    public function edit(Story $story, Chapter $chapter)
    {
        // --- START: Pastikan bab milik story yang sama ---
        if ($chapter->story_id !== $story->id) {
            abort(404);
        }
        // --- END: Validasi kepemilikan story|chapter ---

        // --- START: Otorisasi pemilik cerita ---
        if ($story->user_id !== auth()->id()) {
            abort(403);
        }
        // --- END: Otorisasi ---

        return view('edit_bab', compact('story', 'chapter'));
    }
    // ===================== END: Form Edit Bab =====================


    // ===================== START: Update Bab =====================
    public function update(Request $request, Story $story, Chapter $chapter)
    {
        // --- START: Relasi & otorisasi ---
        if ($chapter->story_id !== $story->id) {
            abort(404);
        }
        if ($story->user_id !== auth()->id()) {
            abort(403);
        }
        // --- END: Relasi & otorisasi ---

        // --- START: Validasi input ---
        $validated = $request->validate([
            'title'       => [
                'required', 'string', 'max:255',
                Rule::unique('chapters', 'title')
                    ->where(fn($q) => $q->where('story_id', $story->id))
                    ->ignore($chapter->id),
            ],
            'content'     => ['required','string'],       // HTML dari Quill
            'author_note' => ['nullable','string'],       // ubah ke nullable agar fleksibel
            'status'      => ['required','in:draft,published'],
        ]);
        // --- END: Validasi input ---

        // --- START: Normalisasi status ke format DB ---
        $status = $validated['status'] === 'published' ? 'Published' : 'Draft';
        // --- END: Normalisasi status ---

        // --- START: Perbarui slug unik per-story bila judul berubah ---
        $newTitle = $validated['title'];
        $baseSlug = Str::slug($newTitle);
        $slug     = $baseSlug;

        if ($newTitle !== $chapter->title) {
            $exists = Chapter::where('story_id', $story->id)
                ->where(function ($q) use ($baseSlug, $slug) {
                    $q->where('slug', $slug)
                      ->orWhere('slug', 'like', $baseSlug.'-%');
                })
                ->where('id', '!=', $chapter->id)
                ->count();

            if ($exists > 0) {
                $slug = $baseSlug.'-'.($exists + 1);
            }
        } else {
            // judul sama → pakai slug lama
            $slug = $chapter->slug;
        }
        // --- END: Slug unik per-story ---

        // --- START: Hitung statistik (kata, waktu baca, excerpt) ---
        $plainText   = trim(strip_tags($validated['content']));
        $wordCount   = str_word_count($plainText);
        $readMinutes = max(1, (int) ceil($wordCount / 200));
        $excerpt     = Str::limit($plainText, 180);
        // --- END: Statistik konten ---

        // --- START: Simpan perubahan dalam transaksi ---
        DB::transaction(function () use ($chapter, $story, $validated, $status, $slug, $wordCount, $readMinutes, $excerpt) {
            // published_at logic: set saat dipublish pertama kali, reset jika kembali draft
            $publishedAt = $chapter->published_at;
            if ($status === 'Published' && is_null($publishedAt)) {
                $publishedAt = Carbon::now();
            }
            if ($status === 'Draft') {
                $publishedAt = null; // kebijakan: draft tak punya published_at
            }

            $chapter->update([
                'title'        => $validated['title'],
                'slug'         => $slug,
                'content'      => $validated['content'],
                'author_note'  => $validated['author_note'] ?? null,
                'status'       => $status,
                'word_count'   => $wordCount,
                // kolom opsional, hanya set jika kolom ada pada tabel
                'reading_time' => property_exists($chapter, 'reading_time') ? $readMinutes : $chapter->reading_time ?? null,
                'excerpt'      => property_exists($chapter, 'excerpt') ? $excerpt : $chapter->excerpt ?? null,
                'published_at' => $publishedAt,
            ]);

            // Sentuh story agar urutan "terbaru" ikut naik
            $story->touch();
        });
        // --- END: Simpan transaksi ---

        // --- START: Redirect dengan pesan ---
        return redirect()
            ->route('stories.manage', $story->slug)
            ->with('success', $status === 'Published'
                ? 'Bab berhasil diperbarui & diterbitkan.'
                : 'Draft bab berhasil diperbarui.');
        // --- END: Redirect ---
    }
    // ===================== END: Update Bab =====================


    // ===================== START: Hapus Bab =====================
    public function destroy(Story $story, Chapter $chapter)
    {
        // --- START: Otorisasi pemilik & validasi relasi ---
        $this->authorizeOwner($story);
        if ($chapter->story_id !== $story->id) {
            abort(404);
        }
        // --- END: Otorisasi & validasi ---

        // --- START: Hapus dan redirect ---
        $chapter->delete();
        return redirect()->route('stories.manage', $story)->with('success', 'Bab "' . $chapter->title . '" berhasil dihapus.');
        // --- END: Hapus & redirect ---
    }
    // ===================== END: Hapus Bab =====================


    // ===================== START: Tampilkan Bab (Reader) =====================
    public function showChapter($storySlug, $chapterSlug)
    {
        // --- START: Ambil story & bab berdasarkan slug ---
        $story = Story::where('slug', $storySlug)->firstOrFail();
        $chapter = Chapter::where('story_id', $story->id)
            ->where('slug', $chapterSlug)
            ->firstOrFail();
        // --- END: Ambil story & bab ---

        // --- START: Cari prev/next chapter (by id) ---
        $chapter->previous = Chapter::where('story_id', $story->id)
            ->where('id', '<', $chapter->id)
            ->orderBy('id', 'desc')
            ->first();

        $chapter->next = Chapter::where('story_id', $story->id)
            ->where('id', '>', $chapter->id)
            ->orderBy('id', 'asc')
            ->first();
        // --- END: Prev/Next ---

        // --- START: Render view reader ---
        return view('bab', [
            'story'   => $story,
            'chapter' => $chapter
        ]);
        // --- END: Render view ---
    }
    // ===================== END: Tampilkan Bab (Reader) =====================
}
