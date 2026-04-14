<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImperialGraceRegistry extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_id',
        'name',
        'purpose',
        'acquisition_method',
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
        return $this->hasMany(UserImperialGrace::class, 'registry_id');
    }
}
