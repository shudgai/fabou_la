<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'other_folder_id', 'title', 'content', 'record_date', 'extra_data'
    ];

    protected $casts = [
        'extra_data' => 'array',
        'record_date' => 'date',
    ];

    public function folder()
    {
        return $this->belongsTo(OtherFolder::class, 'other_folder_id');
    }
}
