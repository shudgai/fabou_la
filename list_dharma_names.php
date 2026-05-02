<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DharmaName;

$list = DharmaName::all();

echo "--- 完整法號表列表 (共 " . $list->count() . " 筆) ---\n";
if ($list->count() > 0) {
    foreach($list as $dn) {
        echo "ID: {$dn->id} | 法號: {$dn->name} | 順序(order): {$dn->order}\n";
    }
} else {
    echo "警告：法號表目前是空的 (Empty)\n";
}
