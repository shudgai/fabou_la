<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class ImperialGrace extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'imperial_graces';

    protected $fillable = [
        'master_id',
        'sort_order',
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
