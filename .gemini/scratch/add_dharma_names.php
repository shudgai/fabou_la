<?php

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DharmaName;

$max = DharmaName::max('order') ?? 0;
DharmaName::create(['name' => '靈奇', 'order' => $max + 1]);
DharmaName::create(['name' => '靈傾', 'order' => $max + 2]);

echo "Added 靈奇 and 靈傾 successfully.\n";
