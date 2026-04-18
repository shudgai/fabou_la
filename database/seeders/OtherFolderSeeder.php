<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OtherFolder;

class OtherFolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $folders = ['老祖仙師', '元始仙師', '道祖仙師', '靈寶仙師'];

        foreach ($folders as $name) {
            OtherFolder::firstOrCreate(
                ['name' => $name],
                ['color' => '#ef4444']
            );
        }
    }
}
