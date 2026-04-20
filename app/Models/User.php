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
        $dharmaName = $this->dharmaName ? $this->dharmaName->name : null;
        return $this->role === 'admin' || 
               ($dharmaName === '元續') ||
               $this->roles()->where('name', '管理員')->exists();
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
            'can_see_daily_teachings' => $isAdmin || in_array($dharmaName, [
                '閻帝', '閻爵', '閻澤', '閻願', '靈果', '靈妙', '元續', '金頤', '靈心', '金振', 
                '金了', '金曉', '道妙', '金悟', '金淑', '金源', '靈智', '靈慧', '金雲', '金戒', 
                '閻珍', '金知', '金忠', '金孝', '金諦', '金彩', '金茹', '金齋', '金德', '靈平', 
                '金惜', '金護', '靈奇', '靈傾'
            ]),
            'can_see_other_folders' => $isAdmin || in_array($dharmaName, [
                '鳳尊', '金巧', '赤覺', '紫元', '靈情', '鳳媓', '靈昡', '龍勝', '龍戰', '閻尊', 
                '閻闇', '元續', '赤峰'
            ]),
            'can_see_treasures' => $isAdmin || in_array($dharmaName, ['赤覺', '元續']),
            'can_see_military' => $isAdmin || in_array($dharmaName, [
                '閻帝', '龍勝', '龍戰', '閻尊', '閻爵', '閻澤', '閻闇', '閻願', '元續'
            ]),
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
