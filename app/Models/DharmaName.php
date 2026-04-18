<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DharmaName extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'alias', 'order'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_dharma_name');
    }

    public function teachings()
    {
        return $this->belongsToMany(Teaching::class, 'teaching_dharma_name');
    }
}
