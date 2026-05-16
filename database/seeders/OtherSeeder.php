<?php

namespace Database\Seeders;

use App\Models\OtherFolder;
use App\Models\User;
use Illuminate\Database\Seeder;

class OtherSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'shudgai999@gmail.com')->first() ?: User::first();
        if (!$user) return;

        $folders = [
            ['name' => '開文核定表', 'color' => '#6366f1'],
            ['name' => '隨機分組', 'color' => '#10b981'],
            ['name' => '抽籤紀錄', 'color' => '#ef4444'],
        ];

        foreach ($folders as $f) {
            OtherFolder::updateOrCreate(
                ['user_id' => $user->id, 'name' => $f['name']],
                ['color' => $f['color']]
            );
        }
    }
}
