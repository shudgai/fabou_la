<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Registry;

$records = Registry::with('dharmaNameRegistries')->get();
$count = 0;

foreach ($records as $r) {
    if (($r->is_multi || $r->dharmaNameRegistries->count() > 0) && $r->dharmaNameRegistries->count() > 0) {
        $hasUnobtained = $r->dharmaNameRegistries->contains('status', '未求得');
        $allRegistered = $r->dharmaNameRegistries->every('status', '已登記');
        
        $newStatus = $hasUnobtained ? '未求得' : ($allRegistered ? '已登記' : '已求得');
        
        if ($r->status !== $newStatus) {
            $r->update(['status' => $newStatus]);
            echo "Updated [{$r->name}] Status: {$r->status} -> {$newStatus}\n";
            $count++;
        }
    }
}

echo "Finished repairing {$count} records.\n";
