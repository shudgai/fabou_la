<?php

namespace Database\Seeders;

use App\Models\Grudge;
use App\Models\User;
use App\Models\DharmaName;
use Illuminate\Database\Seeder;

class GrudgeSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'shudgai999@gmail.com')->first() ?: User::first();
        if (!$user) return;

        $grudges = [
            [
                'user_name' => '金巧',
                'quantity' => '1',
                'status' => '待處理',
                'know_date' => '2026-05-15',
            ],
            [
                'user_name' => '金巧',
                'quantity' => '1',
                'status' => '待處理',
                'know_date' => '2026-05-15',
            ]
        ];

        foreach ($grudges as $g) {
            Grudge::create(array_merge($g, ['user_id' => $user->id]));
        }
    }
}
