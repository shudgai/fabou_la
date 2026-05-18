<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class ImperialGrace extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'imperial_graces';

    protected $casts = [
        'name' => 'encrypted',
        'purpose' => 'encrypted',
        'remarks' => 'encrypted',
        'is_multi' => 'boolean',
    ];

    protected $fillable = [
        'user_id',
        'master_id',
        'sort_order',
        'name',
        'count',
        'purpose',
        'record_date',
        'obtained_date',
        'is_multi',
        'status',
        'remarks',
    ];

    public function dharmaNameRegistries()
    {
        return $this->hasMany(DharmaNameRegistry::class, 'imperial_grace_id');
    }

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function userGraces()
    {
        return $this->hasMany(UserImperialGrace::class);
    }
}
