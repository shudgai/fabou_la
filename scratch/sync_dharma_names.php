<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$names = [
    '金忠' => 35,
    '金孝' => 36,
    '金諦' => 37,
    '金彩' => 38,
    '金茹' => 39,
    '金熹' => 40,
    '金德' => 41,
    '靈平' => 42,
    '金惜' => 43,
    '金護' => 44,
];

// First, move potential conflicting orders to high range to avoid unique constraint error
foreach ($names as $name => $ord) {
    \App\Models\DharmaName::where('order', $ord)->update(['order' => $ord + 1000]);
}

foreach ($names as $name => $ord) {
    \App\Models\DharmaName::updateOrCreate(['name' => $name], ['order' => $ord]);
    echo "Syncing $name as $ord\n";
}
