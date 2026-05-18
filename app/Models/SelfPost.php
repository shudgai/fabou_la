<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SelfPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'date',
        'message_type',
        'master_id',
        'original_content',
        'modified_content',
        'modified_by',
        'sort_order',
    ];

    protected $casts = [
        'original_content' => 'encrypted',
        'modified_content' => 'encrypted',
    ];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }
}
