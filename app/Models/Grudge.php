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
        'user_name' => \App\Casts\TolerantEncrypted::class,
        'user_remarks' => \App\Casts\TolerantEncrypted::class,
        'destination' => \App\Casts\TolerantEncrypted::class,
        'remarks_text' => \App\Casts\TolerantEncrypted::class,
        'remarks' => 'array',
        'know_date' => 'date:Y-m-d',
        'process_date' => 'date:Y-m-d',
        'record_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
