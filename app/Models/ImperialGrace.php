<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImperialGrace extends Model
{
    use HasFactory;

    protected $table = 'imperial_graces';

    protected $fillable = [
        'master_id',
        'name',
        'count',
        'purpose',
        'record_date',
        'obtained_date',
        'status',
        'remarks',
    ];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function userGraces()
    {
        return $this->hasMany(UserImperialGrace::class);
    }
}
