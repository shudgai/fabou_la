<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherTeaching extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sort_order', 'date', 'is_daily', 'master_id', 
        'content', 'supplement', 'target_remarks', 
        'remarks', 'items', 'items_footer_remarks', 'user_id'
    ];

    protected $casts = [
        'items' => \App\Casts\TolerantEncryptedJson::class,
        'is_daily' => 'boolean',
        'content' => \App\Casts\TolerantEncrypted::class,
        'supplement' => \App\Casts\TolerantEncrypted::class,
        'target_remarks' => \App\Casts\TolerantEncrypted::class,
        'remarks' => \App\Casts\TolerantEncryptedJson::class,
        'items_footer_remarks' => \App\Casts\TolerantEncrypted::class,
    ];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
