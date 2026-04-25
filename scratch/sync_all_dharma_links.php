<?php

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';
$app = require_once $root.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\DharmaName;

$users = User::all();
$count = 0;

foreach ($users as $user) {
    if ($user->dharma_name_id) {
        $dharma = DharmaName::find($user->dharma_name_id);
        if ($dharma) {
            if ($dharma->user_id !== $user->id) {
                $dharma->update(['user_id' => $user->id]);
                echo "Fixed Dharma Name {$dharma->name} (ID: {$dharma->id}) to point to User {$user->email} (ID: {$user->id})\n";
                $count++;
            }
        }
    }
}

// Also check the other direction: DharmaNames that have a user_id
$dharmas = DharmaName::whereNotNull('user_id')->get();
foreach ($dharmas as $dharma) {
    $user = User::find($dharma->user_id);
    if ($user) {
        if ($user->dharma_name_id !== $dharma->id) {
            $user->update(['dharma_name_id' => $dharma->id]);
            echo "Fixed User {$user->email} (ID: {$user->id}) to point to Dharma Name {$dharma->name} (ID: {$dharma->id})\n";
            $count++;
        }
    }
}

echo "Bidirectional check complete. Total fixes: $count\n";
