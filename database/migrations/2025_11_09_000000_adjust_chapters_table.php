<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * 1) Tambah kolom-kolom baru jika belum ada
         */
        if (!Schema::hasColumn('chapters', 'number')) {
            Schema::table('chapters', function (Blueprint $table) {
                // Urutan bab per cerita (1,2,3,...) — hindari nama 'order' (reserved word SQL)
                $table->unsignedInteger('number')->default(0)->after('story_id');
            });
        }

        if (!Schema::hasColumn('chapters', 'reading_time')) {
            Schema::table('chapters', function (Blueprint $table) {
                // Perkiraan menit baca (~200 kata/menit)
                $table->unsignedSmallInteger('reading_time')->nullable()->after('word_count');
            });
        }

        if (!Schema::hasColumn('chapters', 'excerpt')) {
            Schema::table('chapters', function (Blueprint $table) {
                $table->text('excerpt')->nullable()->after('content');
            });
        }

        if (!Schema::hasColumn('chapters', 'published_at')) {
            Schema::table('chapters', function (Blueprint $table) {
                $table->timestamp('published_at')->nullable()->after('status');
            });
        }

        if (!Schema::hasColumn('chapters', 'user_id')) {
            Schema::table('chapters', function (Blueprint $table) {
                // Opsional: pencatat penulis bab (kalau kolom ini diinginkan)
                $table->foreignId('user_id')->nullable()
                      ->constrained()->nullOnDelete()->after('story_id');
            });
        }

        /**
         * 2) Pastikan slug tidak null & unik per (story_id, slug)
         *    - isi slug kosong/null dengan fallback
         *    - ubah kolom slug jadi NOT NULL (raw SQL agar tidak perlu doctrine/dbal)
         *    - tambahkan unique key komposit
         */
        DB::statement("UPDATE chapters SET slug = CONCAT('chapter-', id) WHERE slug IS NULL OR slug = ''");

        // Ubah NOT NULL (tanpa require doctrine/dbal)
        DB::statement("ALTER TABLE chapters MODIFY slug varchar(255) NOT NULL");

        // Tambahkan unique key komposit jika belum ada
        // (nama index diset eksplisit supaya mudah di-drop pada down)
        try {
            DB::statement("ALTER TABLE chapters ADD CONSTRAINT chapters_story_slug_unique UNIQUE (story_id, slug)");
        } catch (\Throwable $e) {
            // abaikan jika sudah ada
        }

        // Index bantu untuk query “per cerita urut number”
        Schema::table('chapters', function (Blueprint $table) {
            $table->index(['story_id', 'number'], 'chapters_story_number_idx');
        });

        /**
         * 3) Normalisasi status & backfill metadata lama
         */
        // Jadikan lowercase agar konsisten ('draft' / 'published')
        DB::statement("UPDATE chapters SET status = LOWER(status)");

        // Hitung reading_time dari word_count (>= 1 menit)
        DB::statement("UPDATE chapters SET reading_time = GREATEST(1, CEIL(word_count/200)) WHERE reading_time IS NULL OR reading_time = 0");

        // Isi excerpt ringkas dari content (180 char) — lakukan simpel di SQL
        // NB: ini tidak menghapus tag HTML. Kalau mau 'bersih', lakukan via job/artisan belakangan.
        DB::statement("UPDATE chapters SET excerpt = LEFT(content, 180) WHERE (excerpt IS NULL OR excerpt = '')");

        // Beri nomor urut per story berdasarkan id (1,2,3,...) — MySQL var trick
        DB::statement("SET @prev_story := NULL, @rn := 0");
        DB::statement("
            UPDATE chapters c
            JOIN (
                SELECT id, story_id,
                       IF(@prev_story = story_id, @rn := @rn + 1, @rn := 1) AS rownum,
                       @prev_story := story_id AS _p
                FROM chapters
                ORDER BY story_id, id
            ) x ON x.id = c.id
            SET c.number = x.rownum
        ");
    }

    public function down(): void
    {
        // Hapus index bantu
        Schema::table('chapters', function (Blueprint $table) {
            try { $table->dropIndex('chapters_story_number_idx'); } catch (\Throwable $e) {}
        });

        // Drop unique key komposit
        try { DB::statement("ALTER TABLE chapters DROP INDEX chapters_story_slug_unique"); } catch (\Throwable $e) {}

        // Drop kolom-kolom yang ditambahkan
        Schema::table('chapters', function (Blueprint $table) {
            if (Schema::hasColumn('chapters', 'number'))       $table->dropColumn('number');
            if (Schema::hasColumn('chapters', 'reading_time')) $table->dropColumn('reading_time');
            if (Schema::hasColumn('chapters', 'excerpt'))      $table->dropColumn('excerpt');
            if (Schema::hasColumn('chapters', 'published_at')) $table->dropColumn('published_at');

            if (Schema::hasColumn('chapters', 'user_id')) {
                // helper di Laravel 9+: dropConstrainedForeignId
                try { $table->dropConstrainedForeignId('user_id'); }
                catch (\Throwable $e) {
                    // fallback
                    try { $table->dropForeign(['user_id']); } catch (\Throwable $e2) {}
                    try { $table->dropColumn('user_id'); } catch (\Throwable $e3) {}
                }
            }
        });

        // (opsional) biarkan slug tetap NOT NULL; jika ingin kembalikan ke nullable, lakukan raw SQL:
        // DB::statement("ALTER TABLE chapters MODIFY slug varchar(255) NULL");
    }
};
