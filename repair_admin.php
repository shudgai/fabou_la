<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\DharmaName;

// 1. 強制建立 元續
$dName = DharmaName::firstOrCreate(
    ['name' => '元續'],
    ['order' => 1]
);

// 2. 找到您的帳號並綁定
$user = User::where('email', 'shudgai999@gmail.com')->first();
if ($user) {
    $user->update([
        'dharma_name_id' => $dName->id,
        'role' => 'admin' // 確保權限也是管理員
    ]);
    echo "成功！\n";
    echo "1. 已建立法號：元續 (ID: {$dName->id})\n";
    echo "2. 已將帳號 shudgai999@gmail.com 綁定至此法號，並確認為管理員權限。\n";
    echo "\n現在您可以重新去頁面輸入「元續」與您的 Email 來重設密碼了！\n";
} else {
    echo "錯誤：找不到您的 Email 帳號。\n";
}
