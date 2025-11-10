<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** quick helper: does an index exist? */
    private function indexExists(string $table, string $index): bool
    {
        $db = DB::getDatabaseName();
        $res = DB::selectOne(
            "SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1",
            [$db, $table, $index]
        );
        return (bool) $res;
    }

    public function up(): void
    {
        // add columns only if they don't exist
        if (!Schema::hasColumn('posts', 'type')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('type', 32)->default('info')->after('content'); // info|announcement|update|maintenance
            });
        }

        if (!Schema::hasColumn('posts', 'status')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('status', 16)->default('published')->after('type'); // draft|published
            });
        }

        if (!Schema::hasColumn('posts', 'published_at')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->timestamp('published_at')->nullable()->after('status');
            });
        }

        if (!Schema::hasColumn('posts', 'is_pinned')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->boolean('is_pinned')->default(false)->after('published_at');
            });
        }

        if (!Schema::hasColumn('posts', 'thumbnail_path')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('thumbnail_path')->nullable()->after('is_pinned');
            });
        }

        if (!Schema::hasColumn('posts', 'excerpt')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->text('excerpt')->nullable()->after('thumbnail_path');
            });
        }

        if (!Schema::hasColumn('posts', 'views')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedBigInteger('views')->default(0)->after('excerpt');
            });
        }

        if (!Schema::hasColumn('posts', 'tags')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->json('tags')->nullable()->after('views');
            });
        }

        // indexes (create if missing)
        Schema::table('posts', function (Blueprint $table) {
            // unique slug (wrap in try/catch OR check)
            // safer to check via information_schema
        });

        if (!$this->indexExists('posts', 'posts_slug_unique')) {
            try { Schema::table('posts', fn (Blueprint $t) => $t->unique('slug')); } catch (\Throwable $e) {}
        }

        if (!$this->indexExists('posts', 'posts_type_published_at_index')) {
            try { Schema::table('posts', fn (Blueprint $t) => $t->index(['type','published_at'])); } catch (\Throwable $e) {}
        }

        if (!$this->indexExists('posts', 'posts_is_pinned_index')) {
            try { Schema::table('posts', fn (Blueprint $t) => $t->index('is_pinned')); } catch (\Throwable $e) {}
        }

        // FULLTEXT for search (if supported)
        try {
            if (!$this->indexExists('posts', 'fulltext_title_content')) {
                DB::statement('ALTER TABLE posts ADD FULLTEXT fulltext_title_content (title, content)');
            }
        } catch (\Throwable $e) {
            // ignore if engine doesn’t support fulltext or index already exists
        }
    }

    public function down(): void
    {
        // drop indexes if exist
        try { if ($this->indexExists('posts', 'fulltext_title_content')) {
            DB::statement('ALTER TABLE posts DROP INDEX fulltext_title_content');
        }} catch (\Throwable $e) {}

        try { if ($this->indexExists('posts', 'posts_type_published_at_index')) {
            Schema::table('posts', fn (Blueprint $t) => $t->dropIndex(['type','published_at']));
        }} catch (\Throwable $e) {}

        try { if ($this->indexExists('posts', 'posts_is_pinned_index')) {
            Schema::table('posts', fn (Blueprint $t) => $t->dropIndex(['is_pinned']));
        }} catch (\Throwable $e) {}

        // drop columns only if they exist (do individually)
        foreach (['tags','views','excerpt','thumbnail_path','is_pinned','published_at','status','type'] as $col) {
            if (Schema::hasColumn('posts', $col)) {
                Schema::table('posts', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }
    }
};
