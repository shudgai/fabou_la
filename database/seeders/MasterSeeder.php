<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $masters = [
            '老祖仙師',
            '元始仙師',
            '道祖仙師',
            '靈寶仙師',
            '父皇',
            '太宰仙師',
            '太子',
            '閻王仙師',
        ];

        foreach ($masters as $name) {
            \App\Models\Master::create(['name' => $name]);
        }
    }
}
