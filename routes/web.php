<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StoryController; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkdeskController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\EditorUploadController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportAdminController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\Admin\FeedbackAdminController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===== ROUTE UTAMA (Publik) =====
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/proyek-kami', [HomeController::class, 'proyekKami'])->name('proyek.kami');

// ===== ROUTE POSTINGAN (Publik) =====
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

// ===== ROUTE STATIS LAIN (Publik) =====
Route::view('/developer', 'pengembang.index')->name('about.developer');

// ===== ROUTE PUBLIK: Cerita & Bab =====
Route::get('/stories/{slug}', [StoryController::class, 'show'])->name('stories.show'); // versi path lain
Route::get('/stories/{storySlug}/{chapterSlug}', [ChapterController::class, 'showChapter'])->name('stories.chapter');

// ===== ROUTE LAPORAN (Publik) =====
Route::post('/reports', [ReportController::class, 'store'])
  ->name('reports.store')
  ->middleware('throttle:10,1'); // anti-spam ringan

// ===== ROUTE SARAN/MASUKAN (Publik) =====
    Route::get('/feedback',  [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');





// ===== ROUTE WORKDESK & KONTEN ADMIN (Dilindungi) =====
Route::middleware(['auth'])->group(function () {

    // --- Workdesk (dashboard penulis/admin)
    Route::get('/workdesk', [WorkdeskController::class, 'index'])->name('workdesk'); 

    // --- Postingan (Papan Info / Pengumuman)
    Route::get('/workdesk/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/workdesk/posts', [PostController::class, 'store'])->name('posts.store');

    // Manajemen posting (daftar, edit, update, hapus)
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    
    // --- Karya (Cerita)
    Route::get('/workdesk/stories/create', [StoryController::class, 'create'])->name('stories.create');
    Route::post('/workdesk/stories', [StoryController::class, 'store'])->name('stories.store');

    // Kelola cerita tertentu (by slug pada show/manage, by id pada edit/update/hapus)
    Route::get('/workdesk/stories/{story:slug}', [StoryController::class,'manage'])->name('stories.manage');
    Route::get('/workdesk/stories/{story}/edit', [StoryController::class, 'edit'])->name('stories.edit');
    Route::delete('/workdesk/stories/{story}', [StoryController::class, 'destroy'])->name('stories.destroy');

    // NOTE: Baris berikut duplikat dengan baris edit di atas (rute & nama sama).
    Route::get('/workdesk/stories/{story}/edit', [StoryController::class, 'edit'])->name('stories.edit');

    Route::put('/workdesk/stories/{story}', [StoryController::class, 'update'])->name('stories.update'); 

    // --- Bab (Chapters)
    Route::get('/stories/chapters/create/{story}', [ChapterController::class, 'create'])->name('chapters.create');

    // Menyimpan bab baru untuk sebuah cerita
    Route::post('/workdesk/stories/{story_slug}/chapters', [ChapterController::class, 'store'])->name('chapters.store');
    
    // Edit & update bab
    Route::get('/stories/{story:slug}/chapters/{chapter}/edit', [ChapterController::class, 'edit'])->name('chapters.edit');
    Route::put('/stories/{story:slug}/chapters/{chapter}', [ChapterController::class, 'update'])->name('chapters.update');

    // Hapus bab
    Route::delete('/stories/{story}/chapters/{chapter}', [ChapterController::class, 'destroy'])->name('chapters.destroy');

});



// ===== ROUTE AUTHENTIKASI =====
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login')->middleware('guest');
    Route::post('/login', 'login')->middleware('guest');
    Route::post('/logout', 'logout')->name('logout');
});

// ===== ROUTE UPLOAD (Editor) =====
Route::post('/editor/upload-image', [EditorUploadController::class, 'image'])->name('editor.image.upload');

// ===== ROUTE ADMIN: Manajemen Laporan =====
Route::middleware(['auth']) // kalau mau batasi admin saja, tambahkan middleware 'can:manage-reports'
    ->prefix('admin/reports')
    ->name('admin.reports.')
    ->group(function () {
        Route::get('/', [ReportAdminController::class, 'index'])->name('index');
        Route::get('/{report}', [ReportAdminController::class, 'show'])->name('show');
        Route::patch('/{report}', [ReportAdminController::class, 'update'])->name('update'); // ubah status
        Route::delete('/{report}', [ReportAdminController::class, 'destroy'])->name('destroy'); // opsional
    });

// ===== ROUTE ADMIN: Manajemen Saran/Masukan =====
    Route::middleware(['auth'])->prefix('admin/feedback')->name('admin.feedback.')->group(function(){
        Route::get('/',            [FeedbackAdminController::class, 'index'])->name('index');
        Route::get('/{feedback}',  [FeedbackAdminController::class, 'show'])->name('show');
        Route::patch('/{feedback}',[FeedbackAdminController::class, 'update'])->name('update');
        Route::delete('/{feedback}',[FeedbackAdminController::class, 'destroy'])->name('destroy');
    });
