<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserImperialGrace extends Model
{
    use HasFactory;

    protected $table = 'user_imperial_graces';

    protected $fillable = [
        'user_id',
        'imperial_grace_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function imperialGrace()
    {
        return $this->belongsTo(ImperialGrace::class);
    }
}
