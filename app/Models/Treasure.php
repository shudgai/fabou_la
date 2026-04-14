<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treasure extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'options',
    ];

    protected $casts = [
        'options' => 'array',
    ];
}
