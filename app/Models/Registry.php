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
        'name'               => \App\Casts\TolerantEncrypted::class,
        'purpose'            => \App\Casts\TolerantEncrypted::class,
        'effect'             => \App\Casts\TolerantEncrypted::class,
        'acquisition_method' => \App\Casts\TolerantEncrypted::class,
        'content'            => \App\Casts\TolerantEncrypted::class,
        'remarks'            => \App\Casts\TolerantEncrypted::class,
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
