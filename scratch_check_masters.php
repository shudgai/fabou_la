<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$masters = \App\Models\Master::all();
echo "Masters Table:\n";
foreach ($masters as $m) {
    echo "ID: {$m->id}, Name: {$m->name}\n";
}
