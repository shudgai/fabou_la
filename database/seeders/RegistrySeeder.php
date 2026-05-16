<?php

namespace Database\Seeders;

use App\Models\Registry;
use App\Models\DharmaName;
use App\Models\DharmaNameRegistry;
use App\Models\User;
use Illuminate\Database\Seeder;

class RegistrySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'shudgai999@gmail.com')->first() ?: User::first();
        if (!$user) return;

        $registries = [
            [
                'name' => '全球',
                'master_id' => 1,
                'record_date' => '2026-05-15',
                'status' => '已求得',
                'recipients' => [
                    ['name' => '鳳尊', 'date' => '2026-05-15'],
                    ['name' => '金巧', 'date' => '2026-05-15'],
                    ['name' => '赤覺', 'date' => '2026-05-15'],
                    ['name' => '靈情', 'date' => '2026-05-15'],
                    ['name' => '閻帝', 'date' => '2026-05-15'],
                ]
            ],
            [
                'name' => '親收義女',
                'master_id' => 1,
                'record_date' => '2021-07-25',
                'obtained_date' => '2021-07-25',
                'status' => '未求得',
                'recipients' => [
                    ['name' => '鳳尊', 'date' => '2021-07-25'],
                    ['name' => '靈情', 'date' => '2021-08-08'],
                    ['custom_name' => '道霞龍妃', 'date' => '2021-08-08', 'status' => '已求得'],
                ]
            ],
            [
                'name' => '日後出世車只合眾仙師所賜降所有的法寶皆可承接不用另外請示',
                'master_id' => 1,
                'record_date' => '2021-08-01',
                'obtained_date' => '2021-08-01',
                'status' => '未求得',
                'recipients' => [
                    ['name' => '靈情', 'date' => '2021-08-01'],
                ]
            ],
            [
                'name' => '太髓,真氣',
                'master_id' => 1,
                'record_date' => '2021-08-10',
                'obtained_date' => '2021-08-10',
                'status' => '未求得',
                'recipients' => [
                    ['name' => '金巧', 'date' => '2021-08-10'],
                ]
            ]
        ];

        foreach ($registries as $r) {
            $reg = Registry::updateOrCreate(
                ['user_id' => $user->id, 'name' => $r['name']],
                [
                    'master_id' => $r['master_id'],
                    'record_date' => $r['record_date'],
                    'obtained_date' => $r['obtained_date'] ?? null,
                    'status' => $r['status'] ?? '未求得',
                    'category' => 'major',
                ]
            );

            foreach ($r['recipients'] as $rec) {
                if (isset($rec['name'])) {
                    $dn = DharmaName::where('name', $rec['name'])->first();
                    if ($dn) {
                        DharmaNameRegistry::updateOrCreate(
                            ['registry_id' => $reg->id, 'dharma_name_id' => $dn->id],
                            [
                                'obtained_date' => $rec['date'],
                                'status' => $rec['status'] ?? null,
                                'custom_name' => $rec['name'] // Also store in custom_name for fidelity
                            ]
                        );
                    }
                } else if (isset($rec['custom_name'])) {
                    DharmaNameRegistry::updateOrCreate(
                        ['registry_id' => $reg->id, 'custom_name' => $rec['custom_name']],
                        [
                            'obtained_date' => $rec['date'],
                            'status' => $rec['status'] ?? null
                        ]
                    );
                }
            }
        }
    }
}
