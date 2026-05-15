<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DharmaName;

echo "Dharma Name Alias Mappings:\n";
echo "==========================\n";
$mappings = DharmaName::whereNotNull('alias')->get(['name', 'alias']);
if ($mappings->isEmpty()) {
    echo "No alias mappings found in database.\n";
} else {
    foreach ($mappings as $m) {
        echo "{$m->alias} => {$m->name}\n";
    }
}
echo "==========================\n";
