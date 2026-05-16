<?php

namespace Database\Seeders;

use App\Models\MilitaryRecord;
use App\Models\User;
use Illuminate\Database\Seeder;

class MilitarySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'shudgai999@gmail.com')->first() ?: User::first();
        if (!$user) return;

        $records = [
            [
                'user_name' => '赤覺',
                'army_type' => '虎甲軍',
                'quantity' => '5',
                'status' => '待處理',
                'know_date' => '2026-05-15',
            ]
        ];

        foreach ($records as $r) {
            MilitaryRecord::create(array_merge($r, ['user_id' => $user->id]));
        }
    }
}
