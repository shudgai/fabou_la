<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Registry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'registries';

    protected $fillable = [
        'master_id',
        'name',
        'purpose',
        'acquisition_method',
        'remarks',
        'category',
        'record_date',
        'obtained_date',
    ];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function dharmaNameRegistries()
    {
        return $this->hasMany(DharmaNameRegistry::class);
    }
}
