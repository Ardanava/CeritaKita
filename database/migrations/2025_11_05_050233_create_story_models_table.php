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
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke pemilik
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Info Dasar
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('author_name');
            
            // Sinopsis dari Quill (mediumText lebih besar dari text)
            $table->mediumText('synopsis');
            
            // Path ke file gambar sampul, misal: "covers/image.jpg"
            // Nullable jika pengguna tidak mengunggah sampul
            $table->string('cover_image_path')->nullable(); 

            // Detail Cerita
            $table->json('genres')->nullable(); // Simpan array genre sebagai JSON
            $table->string('type')->default('Novel Web'); // (Novel Web, Cerpen)
            $table->string('status')->default('Berlanjut'); // (Berlanjut, Tamat)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
