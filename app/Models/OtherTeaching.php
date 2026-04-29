<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherTeaching extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sort_order', 'date', 'is_daily', 'master_id', 
        'content', 'supplement', 'target_remarks', 
        'remarks', 'items', 'items_footer_remarks', 'user_id'
    ];

    protected $casts = [
        'items' => 'array',
        'is_daily' => 'boolean'
    ];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
