<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ImperialGrace;

$total = ImperialGrace::count();
echo "Total Imperial Graces: " . $total . "\n";

$latest = ImperialGrace::latest()->take(5)->get();
foreach ($latest as $l) {
    echo "ID: {$l->id}, Name: {$l->name}, Master: {$l->master_id}, User: {$l->user_id}, Status: {$l->status}\n";
}
