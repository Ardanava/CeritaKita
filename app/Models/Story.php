<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Story extends Model
{
    /* ============================================================
    | TRAITS
    |============================================================ */
    use HasFactory;


    /* ============================================================
    | TABLE NAME
    |============================================================ */
    protected $table = 'stories';


    /* ============================================================
    | MASS ASSIGNMENT (fillable)
    | Field yang dapat diisi secara mass assignment
    |============================================================ */
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'synopsis',
        'author_name',
        'artist',
        'cover_image_path',
        'genres',
        'type',
        'status',
        'origin',
        'translator',
        'proofreader',
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
    | ATTRIBUTE CASTING
    |============================================================ */
    protected $casts = [
        'genres' => 'array',   // Simpan daftar genre dalam bentuk array (json)
    ];


    /* ============================================================
    | RELASI: Story → Chapters
    | One Story memiliki banyak Chapter
    |============================================================ */
    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }


    /* ============================================================
    | BOOT: Auto-delete children
    | Saat Story dihapus → semua Chapters ikut dihapus
    |============================================================ */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($story) {
            $story->chapters()->delete();
        });
    }


    /* ============================================================
    | RELASI: Story → User
    | Story dimiliki oleh User
    |============================================================ */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
