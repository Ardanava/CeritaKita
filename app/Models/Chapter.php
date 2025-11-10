<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    /* ============================================================
    | TRAITS
    |============================================================ */
    use HasFactory;


    /* ============================================================
    | TABLE NAME
    |============================================================ */
    protected $table = 'chapters';


    /* ============================================================
    | MASS ASSIGNMENT (fillable)
    |============================================================ */
    protected $fillable = [
        'story_id',
        'user_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'author_note',
        'status',
        'number',
        'word_count',
        'published_at',
    ];


    /* ============================================================
    | ROUTE BINDING
    | Gunakan slug sebagai primary route key
    |============================================================ */
    public function getRouteKeyName()
    {
        return 'slug';
    }


    /* ============================================================
    | RELATIONSHIPS
    |============================================================ */

    /**
     * Chapter → Story
     * Setiap chapter dimiliki oleh satu story
     */
    public function story()
    {
        return $this->belongsTo(Story::class, 'story_id');
    }


    /* ============================================================
    | ATTRIBUTE CASTING
    |============================================================ */
    protected $casts = [
        'published_at' => 'datetime',   // otomatis jadi Carbon instance
        'reading_time' => 'integer',
        'number'       => 'integer',
        'word_count'   => 'integer',
    ];
}
