<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\WeeklyPost;

try {
    $p = WeeklyPost::find(1);
    if ($p) {
        $p->modified_content = str_repeat('A', 1000000); // 1MB
        $p->save();
        echo "SUCCESS: Saved 1MB string to modified_content\n";
    } else {
        echo "ERROR: Post ID 1 not found\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
