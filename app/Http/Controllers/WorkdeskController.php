<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Story; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkdeskController extends Controller
{
    // ===================== START: Index (Workdesk) =====================
    public function index(Request $request)
    {
        // ---------- START: Ambil konteks user & query ----------
        $userId     = Auth::id();
        $searchTerm = trim((string) $request->query('search', ''));
        // ---------- END: Ambil konteks user & query ----------


        // ---------- START: Query Posts milik user (pagination postsPage) ----------
        $posts = Post::where('user_id', $userId)
            ->latest() // ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'postsPage')
            ->withQueryString();
        // ---------- END: Query Posts milik user ----------


        // ---------- START: Query Stories milik user (pagination storiesPage) ----------
        $stories = Story::where('user_id', $userId)
            ->withCount('chapters') // => chapters_count
            // Eager load 1 bab terbaru per story (hindari N+1)
            ->with(['chapters' => function ($q) {
                $q->select('id','story_id','title','number','slug','updated_at')
                  ->orderByDesc('number')
                  ->limit(1);
            }])
            // Filter pencarian judul jika ada keyword
            ->when($searchTerm !== '', function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%");
            })
            ->orderByDesc('updated_at')
            ->paginate(6, ['*'], 'storiesPage')
            ->withQueryString();
        // ---------- END: Query Stories milik user ----------


        // ---------- START: Return ke view ----------
        return view('workdesk', [
            'posts'      => $posts,
            'stories'    => $stories,
            'searchTerm' => $searchTerm, // supaya input search tetap terisi
        ]);
        // ---------- END: Return ke view ----------
    }
    // ===================== END: Index (Workdesk) =====================
}
