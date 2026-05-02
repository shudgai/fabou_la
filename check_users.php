<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "--- 使用者帳號診斷 ---\n";

$users = User::with('dharmaName')->get();
foreach($users as $u) {
    echo "ID: {$u->id} | Email: {$u->email} | 法號: " . ($u->dharmaName ? $u->dharmaName->name : '未設定') . " | 姓名: {$u->name}\n";
}
