<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'group_id',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function dharmaName()
    {
        return $this->hasOne(DharmaName::class);
    }

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

    public function isAdmin()
    {
        return $this->roles()->where('name', '管理員')->exists();
    }

    public function isChijue()
    {
        return $this->dharmaName && $this->dharmaName->name === '赤覺';
    }
}
