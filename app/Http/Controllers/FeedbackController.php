<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function create(Request $request)
    {
        $categories = Feedback::CATEGORIES;
        return view('feedback.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'nullable|string|max:100',
            'email'     => 'nullable|email|max:150',
            'category'  => 'required|string|in:' . implode(',', Feedback::CATEGORIES),
            'message'   => 'required|string|min:10|max:3000',
            'page_url'  => 'nullable|url|max:2048',
            'hp_field'  => 'nullable|string|max:0', // honeypot
        ], ['hp_field.max'=>'Bot detected.']);

        $fb = Feedback::create([
            'user_id'    => optional($request->user())->id,
            'name'       => $data['name'] ?? optional($request->user())->name,
            'email'      => $data['email'] ?? optional($request->user())->email,
            'category'   => $data['category'],
            'message'    => $data['message'],
            'page_url'   => $data['page_url'] ?? $request->headers->get('referer'),
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'status'     => 'open',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ok'=>true, 'id'=>$fb->id]);
        }
        return back()->with('ok', 'Terima kasih! Saran/masukan kamu sudah kami terima.');
    }
}
