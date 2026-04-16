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
        'obtained_date',
        'remarks',
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
