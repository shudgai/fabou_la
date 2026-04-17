<?php

namespace Database\Seeders;

use App\Models\DharmaName;
use App\Models\Group;
use Illuminate\Database\Seeder;

class DharmaGroupSeeder extends Seeder
{
    /**
     * 依照使用者指示：不新增資料，僅將現有法號與群組進行隨機連動。
     */
    public function run(): void
    {
        $allDharmaNames = DharmaName::all();
        $allGroups = Group::all();

        if ($allGroups->isEmpty() || $allDharmaNames->isEmpty()) {
            // 如果目前資料庫完全沒有資料，則不執行連動邏輯
            return;
        }

        // 隨機連動：讓每個人隨機加入數個群組，模擬「一人多組」的場景
        foreach ($allDharmaNames as $dn) {
            // 隨機選取 1~3 個現有群組
            $groupCount = rand(1, min(3, $allGroups->count()));
            $randomGroups = $allGroups->random($groupCount);
            
            foreach ($randomGroups as $group) {
                // 建立中間表關聯 (如果尚未存在)
                if (!$group->dharmaNames()->where('dharma_name_id', $dn->id)->exists()) {
                    $group->dharmaNames()->attach($dn->id);
                }
            }
        }
    }
}
