<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('chapters', function (Blueprint $table) {
            $table->index(['story_id', 'number']);
            $table->index(['story_id', 'title']);
        });
    }
    public function down(): void {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropIndex(['chapters_story_id_number_index']);
            $table->dropIndex(['chapters_story_id_title_index']);
        });
    }
};
