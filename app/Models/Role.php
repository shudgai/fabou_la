<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'role_code', 'allowed_master_ids'];

    protected $casts = [
        'allowed_master_ids' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
