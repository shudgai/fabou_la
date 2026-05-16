<?php

namespace Database\Seeders;

use App\Models\Teaching;
use App\Models\DharmaName;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeachingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'shudgai999@gmail.com')->first() ?: User::first();
        if (!$user) return;

        $teachings = [
            [
                'date' => '2026-05-15',
                'is_daily' => 0,
                'master_id' => 2,
                'content' => "元始仙師開示：\n（1）元丹：三粒金丹吃（9天份）\n（2）把元光寶戒和隨身寶請出求寶\n（3）補身體不足之元素：一粒金丹吃（5天份）\n（4）昊光：求一張貼紙符令貼於隨身寶上，請金印加蓋於第三大穴",
                'target_remarks' => '對象',
                'items' => [
                    ['treasure_name' => '（1）元丹', 'details' => '三粒金丹吃（9天份）'],
                    ['treasure_name' => '（2）把元光寶戒和隨身寶請出求寶'],
                    ['treasure_name' => '（3）補身體不足之元素', 'details' => '一粒金丹吃（5天份）'],
                    ['treasure_name' => '（4）昊光', 'details' => '求一張貼紙符令貼於隨身寶上，請金印加蓋於第三大穴'],
                    ['treasure_name' => '完畢']
                ],
                'items_footer_remarks' => "完畢\n完畢",
                'recipients' => []
            ],
            [
                'date' => '2026-05-15',
                'is_daily' => 1,
                'master_id' => 2,
                'target_remarks' => '玉聖大四喜',
                'items' => [
                    ['treasure_name' => '元丹', 'quantity' => 1]
                ],
                'items_footer_remarks' => "*允同享皇恩\n完畢",
                'recipients' => ['金巧', '赤覺', '紫元', '靈情']
            ],
            [
                'date' => '2026-05-16',
                'is_daily' => 1,
                'master_id' => 8,
                'content' => "閻王仙師開示:\n全體殿生：有關開文；\n大家練習開文已有很長一段時間了，要練習一次就把文全部開完，不要分好幾回才開完文。",
                'target_remarks' => '對象',
                'items' => [],
                'items_footer_remarks' => "完畢\n完畢",
                'recipients' => []
            ],
            [
                'date' => '2026-05-16',
                'is_daily' => 0,
                'master_id' => 2,
                'content' => "元始仙師開示：李李木\n（1）元丹：三粒金丹吃（7天份）\n（2）把元光寶戒和隨身寶請出求寶\n（3）補身體不足之元素：一粒金丹吃（5天份）\n（4）昊光：求一張貼紙符令貼於隨身寶上，請金印加蓋於第三大穴",
                'target_remarks' => '對象',
                'items' => [
                    ['treasure_name' => '（1）元丹', 'details' => '三粒金丹吃（9天份）'],
                    ['treasure_name' => '（2）把元光寶戒和隨身寶請出求寶'],
                    ['treasure_name' => '（3）補身體不足之元素', 'details' => '一粒金丹吃（5天份）'],
                    ['treasure_name' => '（4）昊光', 'details' => '求一張貼紙符令貼於隨身寶上，請金印加蓋於第三大穴'],
                    ['treasure_name' => '完畢']
                ],
                'items_footer_remarks' => "完畢\n完畢",
                'recipients' => []
            ]
        ];

        foreach ($teachings as $t) {
            $teaching = Teaching::updateOrCreate(
                [
                    'user_id' => $user->id, 
                    'date' => $t['date'], 
                    'master_id' => $t['master_id'],
                    'is_daily' => $t['is_daily'],
                    'target_remarks' => $t['target_remarks']
                ],
                [
                    'content' => $t['content'] ?? null,
                    'items' => $t['items'] ?? [],
                    'items_footer_remarks' => $t['items_footer_remarks'] ?? null,
                ]
            );

            if (!empty($t['recipients'])) {
                $ids = DharmaName::whereIn('name', $t['recipients'])->pluck('id')->toArray();
                $teaching->dharmaNames()->sync($ids);
            }
        }
    }
}
