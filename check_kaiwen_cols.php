<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = ['weekly_posts', 'self_posts'];

foreach($tables as $table) {
    if (Schema::hasTable($table)) {
        $cols = Schema::getColumnListing($table);
        echo "Table: $table\nColumns: " . implode(', ', $cols) . "\n\n";
    } else {
        echo "Table: $table NOT FOUND\n\n";
    }
}
