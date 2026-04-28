<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Grudge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_remarks',
        'status',
        'destination',
        'quantity',
        'remarks',
        'remarks_text',
        'know_date',
        'process_date',
        'record_date',
    ];

    protected $casts = [
        'remarks' => 'array',
        'know_date' => 'string',
        'process_date' => 'string',
        'record_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
