<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeeklyPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'date',
        'status',
        'title',
        'original_content',
        'modified_content',
        'sort_order',
    ];

    protected $casts = [
        'title' => \App\Casts\TolerantEncrypted::class,
        'original_content' => \App\Casts\TolerantEncrypted::class,
        'modified_content' => \App\Casts\TolerantEncrypted::class,
    ];
}
