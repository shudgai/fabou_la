<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Treasure;

$units = ['天份', '次性', '顆', '張', '個', '盒'];
foreach($units as $name) {
    Treasure::updateOrCreate(
        ['name' => $name, 'type' => 'unit'],
        ['master_id' => 0, 'category' => '單位']
    );
}
echo "Units synced successfully\n";
