<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'other_folder_id', 'master_id', 'title', 
        'content', 'record_date', 'excel_rows', 'user_id'
    ];

    protected $casts = [
        'excel_rows' => 'array',
        'record_date' => 'date',
    ];

    public function folder()
    {
        return $this->belongsTo(OtherFolder::class, 'other_folder_id');
    }

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
