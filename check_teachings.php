<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

// Find users whose dharma name contains 金熹 or name contains 金熹
$users = \App\Models\User::whereHas('dharmaName', function($q){ $q->where('name','like','%金熹%'); })
    ->orWhere('name','like','%金熹%')
    ->get(['id','name']);
echo "Users found:\n";
foreach($users as $u) echo "  id={$u->id} name={$u->name}\n";

// Get recent teachings
$recs = \App\Models\Teaching::orderBy('id','desc')->take(10)->get(['id','user_id','master_id','is_daily','date','target_remarks']);
echo "\nLast 10 teachings:\n";
foreach($recs as $r) {
    echo "  id={$r->id} user_id={$r->user_id} master_id={$r->master_id} is_daily={$r->is_daily} date={$r->date} target={$r->target_remarks}\n";
}
