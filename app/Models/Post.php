<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /* ============================================================
    | TRAITS
    |============================================================ */
    use HasFactory;


    /* ============================================================
    | MASS ASSIGNMENT (fillable)
    |============================================================ */
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'summary',
        'excerpt',
        'tags',
        'type',
        'visibility',
        'is_pinned',
        'priority',
    ];


    /* ============================================================
    | ATTRIBUTE CASTING
    |============================================================ */
    protected $casts = [
        'is_pinned'    => 'boolean',    // pin post: true/false
        'published_at' => 'datetime',   // otomatis ke Carbon instance
        'tags'         => 'array',      // simpan dalam JSON → array
    ];


    /* ============================================================
    | ROUTE BINDING
    | Gunakan slug sebagai route key
    |============================================================ */
    public function getRouteKeyName()
    {
        return 'slug';
    }


    /* ============================================================
    | QUERY SCOPES
    |============================================================ */

    public function scopePublished($q)
    {
        return $q->where('status', 'published')
                 ->where(function ($qq) {
                     $qq->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                 });
    }


    /* ============================================================
    | RELATIONSHIPS
    |============================================================ */

    /**
     * Post → User
     * Satu post dimiliki oleh seorang user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
