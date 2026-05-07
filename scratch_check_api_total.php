<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Auth::loginUsingId(1);
$request = \Illuminate\Http\Request::create('/registries', 'GET');
$controller = app(\App\Http\Controllers\RegistryController::class);
$response = $controller->index($request);
$data = json_decode($response->getContent(), true);

echo "Total from API: " . ($data['total'] ?? 'N/A') . "\n";
echo "Data count: " . (isset($data['data']) ? count($data['data']) : 'N/A') . "\n";
