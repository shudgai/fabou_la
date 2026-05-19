<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Teaching extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sort_order', 'date', 'is_daily', 'master_id', 
        'content', 'is_content_literal', 'supplement', 'target_remarks', 
        'remarks', 'items', 'items_footer_remarks', 'user_id'
    ];

    protected $casts = [
        'content' => \App\Casts\TolerantEncrypted::class,
        'is_content_literal' => 'boolean',
        'supplement' => \App\Casts\TolerantEncrypted::class,
        'target_remarks' => \App\Casts\TolerantEncrypted::class,
        'items' => \App\Casts\TolerantEncryptedJson::class,
        'remarks' => \App\Casts\TolerantEncryptedJson::class,
        'items_footer_remarks' => \App\Casts\TolerantEncrypted::class,
    ];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function dharmaNames()
    {
        return $this->belongsToMany(DharmaName::class, 'teaching_dharma_name');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
