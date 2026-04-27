<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach(['dharma_names'] as $table) {
    echo "--- $table ---\n";
    foreach(DB::select("DESCRIBE $table") as $c) {
        echo $c->Field . " (" . $c->Type . ")\n";
    }
}
