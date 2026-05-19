<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$tables = [
    'users',
    'roles',
    'role_user',
    'masters',
    'dharma_names',
    'groups',
    'dharma_groups',
    'treasures',
    'imperial_graces',
    'registries',
    'dharma_name_registries',
    'teachings',
    'grudges',
    'military_records',
    'other_records',
    'weekly_posts',
    'self_posts',
    'other_folders'
];

$dir = database_path('seeders/data');
if (!file_exists($dir)) {
    mkdir($dir, 0755, true);
}

echo "Starting database backup to JSON...\n";

foreach ($tables as $table) {
    if (!Schema::hasTable($table)) {
        echo "Table '{$table}' does not exist, skipping.\n";
        continue;
    }
    
    $rows = DB::table($table)->get()->toArray();
    
    // Cast stdClass objects to arrays
    $rowsArray = array_map(function($row) {
        return (array)$row;
    }, $rows);
    
    $jsonPath = $dir . '/' . $table . '.json';
    
    // Save with pretty print and unescaped unicode for readability
    file_put_contents($jsonPath, json_encode($rowsArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo "  - Backup table '{$table}' -> " . count($rowsArray) . " rows saved.\n";
}

echo "Database backup completed successfully! Files saved in 'database/seeders/data/'\n";
