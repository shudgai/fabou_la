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
                'name' => '重大皇恩項目A',
                'master_id' => 1,
                'record_date' => '2026-05-15',
                'status' => '已求得',
                'obtained_date' => '2026-05-16',
            ],
            [
                'name' => '重大皇恩項目B',
                'master_id' => 2,
                'record_date' => '2026-05-15',
                'status' => '未求得',
            ],
            [
                'name' => '重大皇恩項目C',
                'master_id' => 5,
                'record_date' => '2026-05-10',
                'status' => '已登記',
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
