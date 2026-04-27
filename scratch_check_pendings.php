<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Grudge;

$pendings = Grudge::where('status', '待處理')->get();
echo "Found " . $pendings->count() . " pending records.\n";

foreach($pendings as $g) {
    echo "ID: {$g->id} | Name: {$g->user_name} | Dest: {$g->destination} | Remarks: {$g->remarks_text}\n";
}
