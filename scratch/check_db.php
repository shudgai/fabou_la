<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Grudge;

$latest = Grudge::latest('id')->first();
echo "Latest Grudge ID: " . $latest->id . "\n";
echo "Destination: " . $latest->destination . "\n";
echo "Quantity: " . $latest->quantity . "\n";
echo "Remarks JSON: " . json_encode($latest->remarks) . "\n";
echo "Remarks Raw: " . $latest->getRawOriginal('remarks') . "\n";
