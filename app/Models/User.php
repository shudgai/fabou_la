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
        'role',
        'dharma_name_id',
        'group_id',
        'google_id',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin' || $this->roles()->where('name', '管理員')->exists();
    }

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
        return $this->belongsTo(DharmaName::class);
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

    public function isChijue()
    {
        return $this->dharmaName && $this->dharmaName->name === '赤覺';
    }

    public function getPermissions()
    {
        $dharmaName = $this->dharmaName ? $this->dharmaName->name : null;
        $isAdmin = $this->isAdmin();

        $permissions = [
            'can_see_daily_teachings' => $isAdmin || in_array($dharmaName, ['金巧', '赤覺', '紫元', '鳳尊', '鳳媓', '靈情', '靈平']),
            'can_see_other_folders' => $isAdmin || in_array($dharmaName, ['金巧', '赤覺', '紫元', '鳳尊', '鳳媓', '靈情', '靈平']),
            'can_see_treasures' => $isAdmin || $dharmaName === '赤覺',
            'can_see_military' => $isAdmin || in_array($dharmaName, ['閻尊', '閻闇', '龍勝', '龍戰', '閻爵', '閻澤', '閻帝', '閻願']),
            'allowed_armies' => [],
        ];

        if ($isAdmin) {
            $permissions['allowed_armies'] = ['黑曜軍', '耀紫軍', '虎甲軍', '虎賁軍'];
        } else {
            if (in_array($dharmaName, ['閻尊', '閻闇'])) $permissions['allowed_armies'][] = '黑曜軍';
            if (in_array($dharmaName, ['龍勝', '龍戰'])) $permissions['allowed_armies'][] = '耀紫軍';
            if (in_array($dharmaName, ['閻爵', '閻澤'])) $permissions['allowed_armies'][] = '虎甲軍';
            if (in_array($dharmaName, ['閻帝', '閻願'])) $permissions['allowed_armies'][] = '虎賁軍';
        }

        return $permissions;
    }
}
