<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Grudge;

$all = Grudge::all();
foreach ($all as $g) {
    echo "ID: " . $g->id . " | Dest: " . $g->destination . " | Quant: " . $g->quantity . " | Remarks: " . json_encode($g->remarks) . "\n";
}
