<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$search = '龍妃';
$tables = DB::select('SHOW TABLES');
$database = env('DB_DATABASE');
$property = "Tables_in_{$database}";

foreach ($tables as $table) {
    $tableName = $table->$property;
    $columns = Schema::getColumnListing($tableName);
    
    $query = DB::table($tableName);
    $first = true;
    foreach ($columns as $column) {
        if ($first) {
            $query->where($column, 'like', "%{$search}%");
            $first = false;
        } else {
            $query->orWhere($column, 'like', "%{$search}%");
        }
    }
    
    $results = $query->get();
    if ($results->count() > 0) {
        echo "Table: {$tableName}\n";
        print_r($results->toArray());
        echo "\n";
    }
}
