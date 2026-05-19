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
        'status',
        'imperial_grace_id',
    ];

    protected $casts = [
        'custom_name' => \App\Casts\TolerantEncrypted::class,
        'remarks' => \App\Casts\TolerantEncryptedJson::class,
        'related_personnel' => \App\Casts\TolerantEncryptedJson::class,
    ];

    public function registry()
    {
        return $this->belongsTo(Registry::class);
    }

    public function imperialGrace()
    {
        return $this->belongsTo(ImperialGrace::class, 'imperial_grace_id');
    }

    public function dharmaName()
    {
        return $this->belongsTo(DharmaName::class);
    }
}
