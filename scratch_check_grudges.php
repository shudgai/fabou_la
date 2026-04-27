<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Grudge;
use Illuminate\Support\Facades\DB;

echo "--- Schema ---\n";
$columns = DB::select('DESCRIBE grudges');
foreach($columns as $col) {
    echo "{$col->Field} | {$col->Type}\n";
}

echo "\n--- Sample Records ---\n";
$grudges = Grudge::latest()->take(10)->get();
foreach($grudges as $g) {
    echo "ID: {$g->id} | Name: {$g->user_name} | KnowDate: {$g->know_date} | Status: {$g->status} | Dest: {$g->destination}\n";
}
