<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('reports', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
      $table->foreignId('story_id')->nullable()->constrained()->nullOnDelete();
      $table->foreignId('chapter_id')->nullable()->constrained()->nullOnDelete();
      $table->string('category', 50);
      $table->text('description');
      $table->string('page_url')->nullable();
      $table->string('user_agent')->nullable();
      $table->string('ip_address', 45)->nullable();
      $table->string('status', 20)->default('open'); // open|in_progress|resolved
      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('reports');
  }
};
