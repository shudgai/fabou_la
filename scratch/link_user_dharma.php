<?php

// Fix paths to be relative to the project root
$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';
$app = require_once $root.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\DharmaName;

$user = User::where('email', 'shudgai999@gmail.com')->first();
$dharma = DharmaName::where('name', '元續')->first();

if ($user && $dharma) {
    $user->update(['dharma_name_id' => $dharma->id]);
    $dharma->update(['user_id' => $user->id]);
    echo "Successfully linked user {$user->email} (ID: {$user->id}) to Dharma Name {$dharma->name} (ID: {$dharma->id})\n";
} else {
    echo "Error: Could not find user or dharma name.\n";
}
