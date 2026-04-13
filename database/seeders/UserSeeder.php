<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. 建立管理員角色
        $adminRole = \App\Models\Role::firstOrCreate(
            ['role_code' => 'R_ADMIN'],
            ['name' => '管理員']
        );

        // 2. 建立管理員帳號
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'shudgai999@gmail.com'],
            [
                'name' => 'Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('abc1234'),
            ]
        );

        // 3. 綁定角色
        if (!$user->roles()->where('role_code', 'R_ADMIN')->exists()) {
            $user->roles()->attach($adminRole->id);
        }
    }
}
