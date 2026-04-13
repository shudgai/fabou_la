<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImperialGrace extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_id', 'treasure_name', 'description', 'record_date', 
        'know_date', 'report_date', 'display_status', 'status', 
        'order', 'excel_rows', 'user_id'
    ];

    protected $casts = [
        'excel_rows' => 'array',
        'record_date' => 'date',
        'know_date' => 'date',
        'report_date' => 'date',
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
