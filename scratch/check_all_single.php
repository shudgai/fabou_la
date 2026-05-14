<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Group;

echo "Groups with exactly 1 member:\n";
foreach (Group::with('dharmaNames')->get() as $g) {
    if ($g->dharmaNames->count() === 1) {
        echo "- " . $g->name . " (" . $g->dharmaNames->first()->name . ")\n";
    }
}
