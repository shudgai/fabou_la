<?php
use App\Models\Registry;
use App\Models\DharmaNameRegistry;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

DB::transaction(function() {
    $registries = Registry::all();
    $groups = $registries->groupBy(function($r) {
        return trim($r->name) . '-' . $r->master_id . '-' . ($r->category ?? 'major');
    });

    foreach ($groups as $key => $group) {
        if ($group->count() > 1) {
            $main = $group->shift();
            echo "Merging duplicates for: {$main->name} (Master ID: {$main->master_id})\n";
            
            foreach ($group as $duplicate) {
                // Move all personnel records to the main registry
                DharmaNameRegistry::where('registry_id', $duplicate->id)
                    ->update(['registry_id' => $main->id]);
                
                // Transfer notes/methods if main is empty
                if (empty($main->acquisition_method) && !empty($duplicate->acquisition_method)) {
                    $main->update(['acquisition_method' => $duplicate->acquisition_method]);
                }
                
                $duplicate->delete();
                echo " - Deleted duplicate ID: {$duplicate->id}\n";
            }
        }
    }
});

echo "Cleanup finished.\n";
