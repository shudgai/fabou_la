<?php
use App\Models\DharmaName;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$dn = DharmaName::where('name', '金巧')->first();
if ($dn) {
    $existing = DharmaName::where('name', '道霞龍妃')->first();
    if (!$existing) {
        DharmaName::create([
            'name' => '道霞龍妃',
            'order' => DharmaName::max('order') + 1,
            'user_id' => $dn->user_id
        ]);
        echo "Created 道霞龍妃\n";
    } else {
        echo "道霞龍妃 already exists\n";
    }
} else {
    echo "金巧 not found\n";
}
