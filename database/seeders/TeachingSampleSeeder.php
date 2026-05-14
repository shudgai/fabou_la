<?php

namespace Database\Seeders;

use App\Models\Master;
use App\Models\Teaching;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeachingSampleSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment('local', 'testing')) {
            return;
        }

        $samplePath = base_path('tests/fixtures/samples/teaching_sample.txt');
        if (!file_exists($samplePath)) {
            return;
        }

        $content = file_get_contents($samplePath);
        $lines = explode("\n", $content);
        // Remove trailing "完畢" line if present
        $filtered = array_filter($lines, fn($l) => trim($l) !== '完畢');
        $content = implode("\n", $filtered);

        $user = User::where('name', '元續')->first() ?: User::first();
        if (!$user) return;

        $master = Master::where('name', '道祖仙師')->first();
        if (!$master) return;

        Teaching::firstOrCreate(
            [
                'date' => now()->format('Y-m-d'),
                'master_id' => $master->id,
                'content' => trim($content),
            ],
            [
                'user_id' => $user->id,
                'is_daily' => true,
                'sort_order' => Teaching::where('date', now()->format('Y-m-d'))->count() + 1,
            ]
        );
    }
}
