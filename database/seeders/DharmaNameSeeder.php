<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DharmaNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dharmaNames = [
            1 => '鳳尊',
            2 => '金巧',
            3 => '赤覺',
            4 => '紫元',
            5 => '靈情',
            6 => '靈奇',
            7 => '靈傾',
            8 => '閻帝',
            9 => '鳳媓',
            10 => '靈昡',
            11 => '龍勝',
            12 => '龍戰',
            13 => '閻尊',
            14 => '閻爵',
            15 => '閻澤',
            16 => '閻闇',
            17 => '閻願',
            18 => '靈果',
            19 => '靈妙',
            20 => '元續',
            21 => '赤峰',
            22 => '金頤',
            23 => '靈心',
            24 => '金振',
            25 => '金了',
            26 => '金曉',
            27 => '道妙',
            28 => '金悟',
            29 => '金淑',
            30 => '金源',
            31 => '靈智',
            32 => '靈慧',
            33 => '金雲',
            34 => '金戒',
            35 => '閻㻇',
            36 => '金知',
            37 => '金忠',
            38 => '金孝',
            39 => '金諦',
            40 => '金彩',
            41 => '金茹',
            42 => '金熹',
            43 => '金德',
            44 => '靈平',
            45 => '金惜',
            46 => '金護',
            47 => '紫真'
        ];

        foreach ($dharmaNames as $order => $name) {
            \App\Models\DharmaName::updateOrCreate(
                ['order' => $order],
                ['name' => $name]
            );
        }
    }
}
