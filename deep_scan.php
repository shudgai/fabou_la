<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\DharmaName;

echo "--- 深度掃描 --- \n";

// 1. 檢查 Users 表的 name 欄位
$userByName = User::where('name', 'LIKE', '%元續%')->get();
echo "Users 表 (name 欄位) 含有 '元續' 的數量: " . $userByName->count() . "\n";
foreach($userByName as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email}\n";
}

// 2. 檢查 DharmaNames 表
$dnByName = DharmaName::where('name', 'LIKE', '%元續%')->get();
echo "DharmaNames 表 含有 '元續' 的數量: " . $dnByName->count() . "\n";

// 3. 檢查 Admin 權限
$adminEmail = 'shudgai999@gmail.com';
$user = User::where('email', $adminEmail)->first();
if ($user) {
    echo "您的帳號狀況: ID: {$user->id} | Name: {$user->name} | Role: {$user->role}\n";
}
