<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$record = \App\Models\ImperialGrace::get()->first(function($g) { 
    return str_contains($g->name, '八卦陣'); 
});

if ($record) {
    echo $record->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
    echo "NOT FOUND";
}
