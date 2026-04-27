<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DharmaName;

foreach(['靈奇', '靈傾'] as $name) {
    DharmaName::firstOrCreate(['name' => $name], ['order' => DharmaName::max('order') + 1]);
    echo "Created/Found: " . $name . "\n";
}
