<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    use HasFactory;

    protected $fillable = [
        'destination_url',
        'alias',
        'created_at'
    ];

    protected $hidden = [
        'id',
        'updated_at'
    ];
}
