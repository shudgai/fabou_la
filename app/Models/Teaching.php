<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teaching extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'master_id', 
        'content', 'supplement', 'target_remarks', 
        'remarks', 'items', 'items_footer_remarks', 'user_id'
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function dharmaNames()
    {
        return $this->belongsToMany(DharmaName::class, 'teaching_dharma_name');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
