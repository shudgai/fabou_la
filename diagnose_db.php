<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Registry;
use App\Models\ImperialGrace;
use App\Models\WeeklyPost;

echo "--- 診斷報告 ---\n";

echo "\n1. 法寶登記 (Registries):\n";
$regs = Registry::select('id', 'user_id', 'name')->get();
foreach($regs as $r) {
    echo "ID: {$r->id} | UserID: " . ($r->user_id ?: 'NULL') . " | Name: {$r->name}\n";
}

echo "\n2. 重大皇恩 (Imperial Graces):\n";
$graces = ImperialGrace::select('id', 'user_id', 'name')->get();
foreach($graces as $g) {
    echo "ID: {$g->id} | UserID: " . ($g->user_id ?: 'NULL') . " | Name: {$g->name}\n";
}

echo "\n3. 開文專區 (Weekly Posts):\n";
$posts = WeeklyPost::select('id', 'user_id', 'title')->get();
foreach($posts as $p) {
    echo "ID: {$p->id} | UserID: " . ($p->user_id ?: 'NULL') . " | Title: {$p->title}\n";
}
