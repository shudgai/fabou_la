<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            '世代交替',
            '太玄三連脈',
            '太玄四連脈',
            '極玄靈合脈',
            '玉聖大四喜',
            '龍鳳閻合脈',
            '帝爵兩兄弟',
            '老祖仙師直屬弟子',
            '元始仙師直屬弟子',
            '道祖仙師直屬弟子',
            '靈寶仙師直屬弟子',
            '父皇直屬弟子',
            '太宰仙師直屬弟子',
            '閻王仙師直屬弟子',
            '太子直屬弟子',
            '元師',
            '副元帥',
            '各宮宮主',
            '各宮副宮主',
            '同享皇恩群組',
            '享享皇恩群組',
            '閻字輩',
            '長輩',
            '信眾',
            '玄通宮',
            '玄應宮',
            '玄心宮',
            '玄妙宮',
            '玄昇宮',
            '玄願宮',
            '玄法宮',
            '玄閻宮',
            '玄窕宮',
            '玄瑤宮',
            '玄義宮',
            '黑曜軍',
            '虎甲軍',
            '虎賁軍',
            '耀紫軍',

        ];

        foreach ($groups as $name) {
            Group::updateOrCreate(['name' => $name]);
        }
    }
}
