<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'name',
        'email',
        'category',
        'message',
        'status',
        'ip_address',
        'user_agent',
    ];

        public const CATEGORIES = [
        'Saran',
        'Masukan',
        'Permintaan Fitur',
        'Permintaan Judul',
        'Lainnya',
    ];

    public const STATUSES = [
        'open',
        'read',
        'resolved',
    ];


}
