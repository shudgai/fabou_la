<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Treasure;

$teachings = ['法器', '金紙', '法水', '護身符', '金丹', '元丹', '靈丹', '極丹', '太玄靈丹', '道源極丹', '三光金丹', '回春金丹', '龍罡焰丹', '森羅金丹', '救命金丹', '符令', '睡覺符令', '吸煞符令', '替身符令', '龍令', '靈令', '太令', '令旗', '金印', '淨塵', '令牌', '天筆', '法筆', '玉筆', '寶鏡', '寶劍', '八卦', '油燈', '塩寶', '香灰', '高梁法酒', '法酒', '龍涎', '香環'];
foreach($teachings as $name) {
    Treasure::updateOrCreate(['name' => $name, 'type' => 'teaching'], ['master_id' => 0, 'category' => '常用開示']);
}

$contents = ['香灰', '清煞法寶', '昊光', '金丹', '靈丹', '元丹', '金紙'];
foreach($contents as $name) {
    Treasure::updateOrCreate(['name' => $name, 'type' => 'content'], ['master_id' => 0, 'category' => '常用內容物']);
}

echo "All treasures synced successfully\n";
