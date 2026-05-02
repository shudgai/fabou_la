<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\DharmaName;

$dName = DharmaName::where('name', '元續')->first();
if (!$dName) {
    echo "錯誤：找不到法號 '元續'\n";
    // 如果找不到，列出所有法號供參考
    echo "現有法號列表：\n";
    foreach(DharmaName::all() as $dn) echo "- {$dn->name}\n";
    exit;
}

$user = User::where('email', 'shudgai999@gmail.com')->first();
if ($user) {
    $user->update(['dharma_name_id' => $dName->id]);
    echo "成功！已將 'shudgai999@gmail.com' 綁定法號為 '元續' (ID: {$dName->id})\n";
} else {
    echo "錯誤：找不到 Email 'shudgai999@gmail.com'\n";
}
