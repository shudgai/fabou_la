<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grudge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'destination',
        'quantity',
        'remarks',
        'know_date',
        'process_date',
        'record_date',
    ];

    protected $casts = [
        'remarks' => 'array',
        'know_date' => 'date',
        'process_date' => 'date',
        'record_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
