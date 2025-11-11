<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;             // ⬅️ tambahkan
use App\Mail\ReportCreated;                      // ⬅️ pastikan ada mailable ini

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'category'    => 'required|string|in:Typo,Link rusak,Bug UI/UX,Konten tidak sesuai,Lainnya',
            'description' => 'required|string|min:10|max:2000',
            'story_id'    => 'nullable|integer|exists:stories,id',
            'chapter_id'  => 'nullable|integer|exists:chapters,id',
            'page_url'    => 'nullable|url|max:2048',
            'hp_field'    => 'nullable|string|max:0', // honeypot (harus kosong)
        ], [
            'hp_field.max' => 'Bot detected.'
        ]);

        $report = Report::create([
            'user_id'     => optional($request->user())->id,
            'story_id'    => $data['story_id'] ?? null,
            'chapter_id'  => $data['chapter_id'] ?? null,
            'category'    => $data['category'],
            'description' => $data['description'],
            'page_url'    => $data['page_url'] ?? $request->headers->get('referer'),
            'user_agent'  => $request->userAgent(),
            'ip_address'  => $request->ip(),
            'status'      => 'open',
        ]);

        // (opsional) kirim notifikasi
        try {
            // Gunakan send() jika belum pakai queue worker
            Mail::to(config('mail.from.address'))->send(new ReportCreated($report));
            // Mail::to(config('mail.from.address'))->queue(new ReportCreated($report));
        } catch (\Throwable $e) {
            // Jangan gagalkan laporan hanya karena email gagal
            report($e);
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'id' => $report->id], 201); // 201 Created
        }

        return back()->with('report_ok', true);
    }
}
