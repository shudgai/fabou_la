<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Group;

$groups = Group::with('dharmaNames')->get();
foreach ($groups as $g) {
    echo $g->name . ": " . $g->dharmaNames->pluck('name')->implode(', ') . "\n";
}
