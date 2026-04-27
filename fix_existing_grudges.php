<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Grudge;
use Illuminate\Support\Facades\DB;

echo "Fixing Grudge records...\n";

$count = 0;
// 1. Convert all '待處理' to '已處理' (as none say '未處理')
$pendings = Grudge::where('status', '待處理')->get();
foreach($pendings as $g) {
    $g->status = '已處理';
    // If destination was '未處理', set it to '已處理' as a fallback
    if ($g->destination === '未處理') {
        $g->destination = '已處理';
    }
    // If process_date is null, set it to know_date
    if (!$g->process_date) {
        $g->process_date = $g->know_date;
    }
    $g->save();
    $count++;
}
echo "Updated $count pending records to processed.\n";

// 2. Ensure results are in remarks_text
$processed = Grudge::where('status', '已處理')->get();
$remCount = 0;
foreach($processed as $g) {
    $changed = false;
    // If remarks_text is empty but destination has value (other than '已處理')
    if (empty($g->remarks_text) && $g->destination !== '已處理' && $g->destination !== '未處理') {
        $g->remarks_text = $g->destination;
        $changed = true;
    }
    
    // Also fix the case where user_name looks like a result (e.g. ends with '軍隊)')
    if (strpos($g->user_name, '軍隊') !== false || strpos($g->user_name, '歸於') !== false) {
        if (empty($g->remarks_text)) {
             $g->remarks_text = $g->user_name;
        } else {
             $g->remarks_text = $g->user_name . ' | ' . $g->remarks_text;
        }
        $changed = true;
    }

    if ($changed) {
        $g->save();
        $remCount++;
    }
}
echo "Fixed remarks/results for $remCount records.\n";

echo "Done.\n";
