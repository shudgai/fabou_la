<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Group;

$group = Group::where('name', '玄義宮')->with('dharmaNames')->first();
if ($group) {
    echo "Group: " . $group->name . "\n";
    echo "Members count: " . $group->dharmaNames->count() . "\n";
    echo "Members: " . $group->dharmaNames->pluck('name')->implode(', ') . "\n";
} else {
    echo "Group not found\n";
}
