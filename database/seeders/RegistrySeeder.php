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
        $user = User::where('name', '元續')->first() ?: User::first();
        if (!$user) return;

        $registries = [
            [
                'name' => '森羅戒',
                'category' => 'major',
                'purpose' => '隨身保平安、清煞',
                'master_id' => 1,
                'recipients' => ['靈昡', '元續']
            ],
            [
                'name' => '金印',
                'category' => 'major',
                'purpose' => '鎮宅、清磁場',
                'master_id' => 2,
                'recipients' => ['閻尊']
            ],
            [
                'name' => '清煞法寶',
                'category' => 'major',
                'purpose' => '清除深層煞氣',
                'master_id' => 1,
                'recipients' => ['靈果', '龍戰']
            ]
        ];

        foreach ($registries as $r) {
            $reg = Registry::updateOrCreate(
                ['user_id' => $user->id, 'name' => $r['name']],
                [
                    'master_id' => $r['master_id'],
                    'category' => $r['category'],
                    'purpose' => $r['purpose'],
                    'record_date' => now()->format('Y-m-d'),
                ]
            );

            foreach ($r['recipients'] as $name) {
                $dn = DharmaName::where('name', $name)->first();
                if ($dn) {
                    DharmaNameRegistry::updateOrCreate(
                        ['registry_id' => $reg->id, 'dharma_name_id' => $dn->id],
                        ['obtained_date' => now()->format('Y-m-d')]
                    );
                }
            }
        }
    }
}
