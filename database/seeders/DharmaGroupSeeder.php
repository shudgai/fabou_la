<?php

namespace Database\Seeders;

use App\Models\DharmaName;
use App\Models\Group;
use Illuminate\Database\Seeder;

class DharmaGroupSeeder extends Seeder
{
    public function run(): void
    {
        // 建立測試群組
        $groups = [
            '老祖組' => ['老祖門生A', '老祖門生B', '老祖門生C'],
            '元始組' => ['元始門生X', '元始門生Y'],
            '第一組' => ['成員1', '成員2', '成員3', '成員4'],
        ];

        foreach ($groups as $groupName => $members) {
            $group = Group::firstOrCreate(['name' => $groupName]);
            
            foreach ($members as $name) {
                $dn = DharmaName::firstOrCreate(['name' => $name], ['user_id' => 1]);
                
                // 關聯群組
                if (!$group->dharmaNames()->where('dharma_name_id', $dn->id)->exists()) {
                    $group->dharmaNames()->attach($dn->id);
                }
            }
        }
    }
}
