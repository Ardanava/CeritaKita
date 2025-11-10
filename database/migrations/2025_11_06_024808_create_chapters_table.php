<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();

            // 1. Relasi ke tabel 'stories'
            // Ini akan menghubungkan setiap bab ke cerita induknya.
            $table->foreignId('story_id')
                  ->constrained('stories') // Terhubung ke tabel 'stories'
                  ->onDelete('cascade'); // Jika cerita dihapus, bab-nya juga.

            // 2. Judul Bab (dari input 'chapter_title')
            $table->string('title');
            
            // Slug unik untuk URL bab (akan kita buat dari 'title' di controller)
            // Kita buat nullable() untuk draf, tapi unik saat diisi
            $table->string('slug')->unique()->nullable(); 

            // 3. Konten Bab (dari 'chapter_content')
            // longText adalah tipe data terbesar untuk teks, cocok untuk isi bab
            $table->longText('content');

            // 4. Catatan Penulis (dari 'author_note')
            // Nullable karena opsional
            $table->text('author_note')->nullable();

            // 5. Status Bab (dari 'status')
            // Default 'draft' sesuai JS Anda
            $table->string('status')->default('draft'); // 'draft' or 'published'

            // 6. Data Meta (Tambahan yang Bermanfaat)
            // Akan kita isi dari controller
            $table->integer('word_count')->default(0); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
