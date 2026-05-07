<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$userId = 1;
$registryCount = \App\Models\Registry::where('user_id', $userId)->count();
$dnrCount = \App\Models\DharmaNameRegistry::whereHas('registry', function($q) use ($userId) {
    $q->where('user_id', $userId);
})->count();

echo "Registry: $registryCount\n";
echo "DharmaNameRegistry: $dnrCount\n";
