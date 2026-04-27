<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;


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

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function isAdmin()
    {
        $dName = trim($this->dharmaName ? $this->dharmaName->name : $this->name);
        return $this->role === 'admin' || 
               in_array($dName, ['元續', '赤峰', '閻闇']) ||
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
        $dName = trim($this->dharmaName ? $this->dharmaName->name : $this->name);
        return $dName === '赤覺';
    }

    public function getPermissions()
    {
        $dName = trim($this->dharmaName ? $this->dharmaName->name : $this->name);
        
        // 超級管理員 (Owner)
        $isFullAdmin = $this->isAdmin();

        // 高級權限者 (具有完整父皇仙師專區與其他專區權限)
        $isAdvanced = in_array($dName, [
            '鳳尊', '金巧', '赤覺', '紫元', '靈情', '鳳媓', '靈昡', '龍勝', '龍戰', '閻尊'
        ]);

        $permissions = [
            // 1. 基本權限 (所有人皆有)
            'can_see_grace' => true,
            'can_see_kaiwen' => true,
            'can_see_grudge' => true,
            'can_see_trash' => true,
            
            // 2. 父皇仙師專區連動
            // 基礎用戶僅能看「父皇仙師每日開示」(can_see_daily_teachings)
            // 管理員及高級用戶可看「八位仙師專區」(can_see_teaching_folders)
            'can_see_daily_teachings' => true, 
            'can_see_teaching_folders' => $isFullAdmin || $isAdvanced,
            
            // 3. 其他專區與法寶登記
            'can_see_other_folders' => $isFullAdmin || $isAdvanced,
            'can_see_treasures' => $isFullAdmin || in_array($dName, ['赤覺']),
            
            // 4. 軍隊專區
            'can_see_military' => $isFullAdmin || in_array($dName, [
                '閻尊', '龍勝', '龍戰', '閻爵', '閻澤', '閻帝', '閻願'
            ]),
            'allowed_armies' => [],
        ];

        // 軍隊權限細分
        if ($isFullAdmin) {
            $permissions['allowed_armies'] = ['黑曜軍', '耀紫軍', '虎甲軍', '虎賁軍'];
        } else {
            if (in_array($dName, ['閻尊'])) $permissions['allowed_armies'][] = '黑曜軍';
            if (in_array($dName, ['龍勝', '龍戰'])) $permissions['allowed_armies'][] = '耀紫軍';
            if (in_array($dName, ['閻爵', '閻澤'])) $permissions['allowed_armies'][] = '虎甲軍';
            if (in_array($dName, ['閻帝', '閻願'])) $permissions['allowed_armies'][] = '虎賁軍';
        }

        return $permissions;
    }
}
