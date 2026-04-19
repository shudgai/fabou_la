<?php
$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$app = require_once $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DharmaName;

$dn = DharmaName::where('name', '金彩')->first();
if ($dn) {
    echo "DharmaName: {$dn->name} (ID: {$dn->id})\n";
    foreach ($dn->groups as $g) {
        echo "- Group: {$g->name} (ID: {$g->id})\n";
    }
} else {
    echo "DharmaName 金彩 not found.\n";
}
