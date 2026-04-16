<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MilitaryRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_remarks',
        'status',
        'army_type',
        'destination',
        'quantity',
        'yan_zun',
        'yan_an',
        'long_sheng',
        'long_zhan',
        'know_date',
        'process_date',
        'remarks_text'
    ];

    protected $casts = [
        'know_date' => 'date',
        'process_date' => 'date',
        'quantity' => 'integer',
        'yan_zun' => 'integer',
        'yan_an' => 'integer',
        'long_sheng' => 'integer',
        'long_zhan' => 'integer',
    ];
}
