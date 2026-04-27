<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Group;

$groups = Group::where('name', 'LIKE', '第%組')->with('dharmaNames')->get();
foreach ($groups as $g) {
    echo $g->name . ': ' . $g->dharmaNames->pluck('name')->implode(', ') . "\n";
}
