<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

foreach (User::with('dharmaName')->get() as $u) {
    $dName = trim($u->dharmaName ? $u->dharmaName->name : $u->name);
    echo "ID: {$u->id} | Name: {$u->name} | D-Name: " . ($u->dharmaName ? $u->dharmaName->name : 'NULL') . " | Derived: {$dName} | Permissions: " . json_encode($u->getPermissions()) . PHP_EOL;
}
