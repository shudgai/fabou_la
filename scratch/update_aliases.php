<?php
require __DIR__ . '/public/index.php';
use App\Models\DharmaName;

$map = [
    '龍勝' => '金耀',
    '龍戰' => '金瑞',
    '靈平' => '金平',
    '靈情' => '金熙',
    '靈果' => '金容',
    '靈妙' => '金梅',
    '靈智' => '金蘭',
    '靈慧' => '金涓',
    '靈心' => '金旭'
];

foreach ($map as $name => $alias) {
    $count = DharmaName::where('name', $name)->update(['alias' => $alias]);
    echo "Updated $name with alias $alias ($count rows)\n";
}
