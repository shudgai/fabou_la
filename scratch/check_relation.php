<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Group;
use App\Models\DharmaName;

echo "All Groups in DB:\n";
foreach (Group::all() as $g) {
    echo "- " . $g->name . "\n";
}

$dharmaName = '閻願';
$dharma = DharmaName::where('name', $dharmaName)->first();

if ($dharma) {
    echo "\nDharmaName: " . $dharma->name . "\n";
    $groups = $dharma->groups()->pluck('name')->toArray();
    echo "Groups for this DharmaName: " . implode(', ', $groups) . "\n";
} else {
    echo "\nDharmaName '閻願' not found.\n";
}
