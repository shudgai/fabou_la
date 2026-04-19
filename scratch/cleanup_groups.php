<?php
$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$app = require_once $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Group;

$toDelete = ['元帥', '副元帥', '信眾', '元師'];
foreach ($toDelete as $name) {
    $group = Group::where('name', $name)->first();
    if ($group) {
        echo "Deleting group: {$name}\n";
        $group->dharmaNames()->detach();
        $group->delete();
    }
}
echo "Cleanup complete.\n";
