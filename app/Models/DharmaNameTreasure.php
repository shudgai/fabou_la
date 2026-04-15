<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DharmaNameTreasure extends Model
{
    use HasFactory;

    protected $fillable = [
        'treasure_id',
        'dharma_name_id',
        'obtained_date',
        'remarks'
    ];

    public function treasure()
    {
        return $this->belongsTo(Treasure::class);
    }

    public function dharmaName()
    {
        return $this->belongsTo(DharmaName::class);
    }
}
