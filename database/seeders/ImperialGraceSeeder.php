<?php

namespace Database\Seeders;

use App\Models\ImperialGrace;
use App\Models\User;
use Illuminate\Database\Seeder;

class ImperialGraceSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'shudgai999@gmail.com')->first() ?: User::first();
        if (!$user) return;

        $graces = [
            [
                'name' => 'Ggyh',
                'master_id' => 1,
                'record_date' => '2026-05-15',
                'status' => '未求得',
            ],
            [
                'name' => 'ddd',
                'master_id' => 2,
                'record_date' => '2026-05-15',
                'status' => '未求得',
            ]
        ];

        foreach ($graces as $g) {
            ImperialGrace::updateOrCreate(
                ['user_id' => $user->id, 'name' => $g['name']],
                [
                    'master_id' => $g['master_id'],
                    'record_date' => $g['record_date'],
                    'status' => $g['status'],
                ]
            );
        }
    }
}
