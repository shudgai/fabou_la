<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DharmaNameRegistry extends Model
{
    use HasFactory;

    protected $table = 'dharma_name_registries';

    protected $fillable = [
        'registry_id',
        'dharma_name_id',
        'custom_name',
        'obtained_date',
        'remarks',
        'related_personnel',
    ];

    protected $casts = [
        'related_personnel' => 'array',
        'remarks' => 'array',
    ];

    public function registry()
    {
        return $this->belongsTo(Registry::class);
    }

    public function dharmaName()
    {
        return $this->belongsTo(DharmaName::class);
    }
}
