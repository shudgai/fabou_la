<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherFolder extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'color'];

    public function otherRecords()
    {
        return $this->hasMany(OtherRecord::class);
    }
}
