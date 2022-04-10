<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'url_id',
        'location',
        'created_at'
    ];
}
