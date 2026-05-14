<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$latest = \App\Models\Teaching::orderBy('id', 'desc')->limit(5)->get();
foreach ($latest as $r) {
    echo "ID: {$r->id}\n";
    echo "Content: " . json_encode($r->content) . "\n";
    echo "Items Count: " . count($r->items || []) . "\n";
    echo "-------------------\n";
}
