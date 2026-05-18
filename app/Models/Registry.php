<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Registry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'registries';

    protected $casts = [
        'name'               => 'encrypted',
        'purpose'            => 'encrypted',
        'effect'             => 'encrypted',
        'acquisition_method' => 'encrypted',
        'content'            => 'encrypted',
        'remarks'            => 'encrypted',
        'is_multi'           => 'boolean',
    ];

    protected $fillable = [
        'user_id',
        'master_id',
        'sort_order',
        'name',
        'purpose',
        'effect',
        'acquisition_method',
        'content',
        'remarks',
        'category',
        'record_date',
        'obtained_date',
        'status',
        'is_multi',
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
