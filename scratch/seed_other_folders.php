<?php

use App\Models\OtherFolder;

// Clear existing folders if needed, or just insert new ones
$folders = ['老祖仙師', '元始仙師', '道祖仙師', '靈寶仙師'];

foreach ($folders as $name) {
    OtherFolder::firstOrCreate(
        ['name' => $name],
        ['color' => '#ef4444'] // Red color
    );
}

echo "Folders established successfully.\n";
