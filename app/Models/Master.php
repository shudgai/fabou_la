<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function imperialGraces()
    {
        return $this->hasMany(ImperialGrace::class);
    }

    public function teachings()
    {
        return $this->hasMany(Teaching::class);
    }

    public function otherRecords()
    {
        return $this->hasMany(OtherRecord::class);
    }
}
