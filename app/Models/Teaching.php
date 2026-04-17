<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teaching extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'master_id', 'group_id', 'dharma_name_id', 
        'title', 'content', 'supplement', 'target_remarks', 
        'remarks', 'items', 'items_footer_remarks', 'user_id'
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function dharmaName()
    {
        return $this->belongsTo(DharmaName::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
