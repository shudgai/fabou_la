<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserImperialGrace extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'registry_id',
        'obtained_date',
        'remarks',
        'record_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registry()
    {
        return $this->belongsTo(ImperialGraceRegistry::class, 'registry_id');
    }
}
