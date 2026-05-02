<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\DharmaName;

// 1. 建立法號 (如果不存在)
$dName = DharmaName::firstOrCreate(
    ['name' => '元續'],
    ['order' => 1] // 給它一個順序
);

// 2. 綁定使用者
$user = User::where('email', 'shudgai999@gmail.com')->first();
if ($user) {
    $user->update(['dharma_name_id' => $dName->id]);
    echo "完成！法號 '元續' 已建立並綁定至 'shudgai999@gmail.com'。\n";
} else {
    echo "錯誤：找不到 Email 'shudgai999@gmail.com'\n";
}
