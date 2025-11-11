<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackAdminController extends Controller
{
    /**
     * Tampilkan daftar feedback dengan filter & statistik ringkas.
     */
    public function index(Request $request)
    {
        $q        = trim((string) $request->get('q', ''));
        $status   = (string) $request->get('status', '');
        $category = (string) $request->get('category', '');
        $sort     = (string) $request->get('sort', '-created'); // -created = terbaru, +created = terlama

        $query = Feedback::query();

        // Pencarian bebas di beberapa kolom
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('message', 'like', "%{$q}%")
                  ->orWhere('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('page_url', 'like', "%{$q}%")
                  ->orWhere('ip_address', 'like', "%{$q}%")
                  ->orWhere('user_agent', 'like', "%{$q}%");
            });
        }

        // Filter status
        if ($status !== '' && in_array($status, Feedback::STATUSES ?? ['open','in_progress','resolved'], true)) {
            $query->where('status', $status);
        }

        // Filter kategori
        if ($category !== '' && in_array($category, Feedback::CATEGORIES ?? [], true)) {
            $query->where('category', $category);
        }

        // Urutan
        if ($sort === '+created') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $feedback = $query->paginate(20)->withQueryString();

        // Statistik ringkas (untuk cards di atas tabel)
        $stats = [
            'open'        => Feedback::where('status', 'open')->count(),
            'in_progress' => Feedback::where('status', 'in_progress')->count(),
            'resolved'    => Feedback::where('status', 'resolved')->count(),
            'total'       => Feedback::count(),
        ];

        $categories = Feedback::CATEGORIES ?? [];

        return view('admin.feedback.index', compact(
            'feedback', 'q', 'status', 'category', 'sort', 'stats', 'categories'
        ));
    }

    /**
     * Detail satu feedback.
     */
    public function show(Feedback $feedback)
    {
        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Update status feedback (open|in_progress|resolved).
     */
    public function update(Request $request, Feedback $feedback)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', Feedback::STATUSES ?? ['open','in_progress','resolved']),
        ]);

        $feedback->update([
            'status' => $request->string('status'),
        ]);

        return back()->with('ok', 'Status diperbarui.');
    }

    /**
     * Hapus feedback.
     */
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();

        return redirect()
            ->route('admin.feedback.index')
            ->with('ok', 'Feedback dihapus.');
    }
}
