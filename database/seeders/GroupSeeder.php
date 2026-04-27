<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * 從 Excel 提取的正式群組/職務清單
     */
    public function run(): void
    {
        $groups = [
            '殿中之人',
            '世代交替',
            '龍鳳閻合脈',
            '同享皇恩',
            '享享皇天恩',
            '太玄三連脈',
            '太玄四連脈',
            '玉聖大四喜',
            '極玄靈合脈',
            '虎賁軍',
            '元帥副元帥',
            '帝爵兩兄弟',
            '玄法宮',
            '耀紫軍',
            '玄心宮',
            '黑曜軍',
            '玄閻宮',
            '虎甲軍',
            '玄昇宮',
            '玄應宮',
            '玄義宮',
            '玄通宮',
            '各宮',
            '宮主',
            '宮主副宮主',
            '玄妙宮',
            '玄願宮',
            '玄窕宮',
            '玄瑤宮',
            '老祖仙師直屬弟子',
            '元始仙師直屬弟子',
            '道祖仙師直屬弟子',
            '靈寶仙師直屬弟子',
            '父皇直屬弟子',
            '太宰仙師直屬弟子',
            '閻王仙師直屬弟子',
            '太子直屬弟子'
        ];

        foreach ($groups as $name) {
            Group::updateOrCreate(['name' => $name]);
        }
    }
}
