<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treasure extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_id',
        'name',
        'purpose',
        'acquisition_method',
        'remarks',
        'record_date',
        'obtained_date',
    ];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }
}
