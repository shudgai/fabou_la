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
            6 => '閻帝',
            7 => '鳳媓',
            8 => '靈昡',
            9 => '龍勝',
            10 => '龍戰',
            11 => '閻尊',
            12 => '閻爵',
            13 => '閻澤',
            14 => '閻闇',
            15 => '閻願',
            16 => '靈果',
            17 => '靈妙',
            18 => '元續',
            19 => '赤峰',
            20 => '金頤',
            21 => '靈心',
            22 => '金振',
            23 => '金了',
            24 => '金曉',
            25 => '道妙',
            26 => '金悟',
            27 => '金淑',
            28 => '金源',
            29 => '靈智',
            30 => '靈慧',
            31 => '金雲',
            32 => '金戒',
            33 => '閻㻇',
            34 => '金知',
            35 => '金忠',
            36 => '金孝',
            37 => '金諦',
            38 => '金彩',
            39 => '金茹',
            40 => '金熹',
            41 => '金德',
            42 => '靈平',
            43 => '金惜',
            44 => '金護',
            45 => '靈奇',
            46 => '靈傾'
        ];

        foreach ($dharmaNames as $order => $name) {
            \App\Models\DharmaName::updateOrCreate(
                ['order' => $order],
                ['name' => $name]
            );
        }
    }
}
