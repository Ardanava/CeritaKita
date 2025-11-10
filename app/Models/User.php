<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /* ============================================================
    | TRAITS
    |============================================================ */
    use HasFactory, Notifiable;   // Factory support + notifications


    /* ============================================================
    | MASS ASSIGNMENT
    | Fields yang boleh di-isi secara mass-assignment
    |============================================================ */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];


    /* ============================================================
    | HIDDEN FIELDS
    | Tidak akan ikut terekspos saat model di-serialize → array/JSON
    |============================================================ */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /* ============================================================
    | ATTRIBUTE CASTING
    | Konversi otomatis kolom ke tipe tertentu
    |============================================================ */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // ubah otomatis ke Carbon datetime
            'password'          => 'hashed',   // hashing otomatis saat set password
        ];
    }
}
