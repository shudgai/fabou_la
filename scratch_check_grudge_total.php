<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Auth::loginUsingId(1);
$request = \Illuminate\Http\Request::create('/grudges', 'GET');
$controller = app(\App\Http\Controllers\GrudgeController::class);
$response = $controller->index($request);
$data = json_decode($response->getContent(), true);

echo "Grudge Total: " . ($data['paginator']['total'] ?? $data['total'] ?? 'N/A') . "\n";
