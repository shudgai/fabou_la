<?php

namespace Database\Seeders;

use App\Models\DharmaName;
use App\Models\Group;
use Illuminate\Database\Seeder;

class DharmaGroupSeeder extends Seeder
{
    public function run(): void
    {
        // Source of Truth: Group-Centric Definitions
        $groupMembers = [
            '殿中之人' => ['鳳尊', '金巧', '赤覺', '紫元', '靈情', '閻帝', '鳳媓', '靈昡', '龍勝', '龍戰', '閻尊'],
            '世代交替' => ['鳳尊', '鳳媓', '龍勝', '龍戰', '閻尊', '閻澤', '閻闇', '赤峰', '靈慧', '閻㻇'],
            '龍鳳閻合脈' => ['鳳尊', '鳳媓', '龍勝', '龍戰', '閻尊', '閻闇'],
            '同享皇恩' => ['鳳尊', '金巧', '赤覺', '紫元', '靈情', '閻帝', '鳳媓', '靈昡', '龍勝', '龍戰', '閻尊', '閻闇', '元續', '赤峰', '閻㻇', '金熹'],
            '享享皇天恩' => ['鳳尊', '金巧', '赤覺', '紫元', '靈情', '閻帝', '鳳媓', '靈昡', '龍勝', '龍戰', '閻尊', '元續', '金熹', '靈平'],
            '太玄三連脈' => ['紫元', '赤覺', '金巧'],
            '太玄四連脈' => ['金巧', '赤覺', '紫元', '靈昡'],
            '玉聖大四喜' => ['金巧', '赤覺', '紫元', '靈情'],
            '極玄靈合脈' => ['金巧', '赤覺', '紫元', '靈情', '靈昡'],
            '虎賁軍' => ['閻帝', '閻願'],
            '元帥副元帥' => ['閻帝', '龍勝', '龍戰', '閻尊', '閻爵', '閻澤', '閻闇', '閻願'],
            '帝爵兩兄弟' => ['閻帝', '閻爵'],
            '玄法宮' => ['靈昡'],
            '耀紫軍' => ['龍勝', '龍戰'],
            '玄心宮' => ['龍勝', '龍戰', '金熹'],
            '黑曜軍' => ['閻尊', '閻闇'],
            '玄閻宮' => ['閻尊'],
            '虎甲軍' => ['閻爵', '閻澤'],
            '玄昇宮' => ['閻爵', '閻澤', '靈心', '金護'],
            '玄應宮' => ['閻闇', '元續', '赤峰', '金悟'],
            '玄義宮' => ['閻願'],
            '玄通宮' => ['靈果', '靈妙', '靈智', '靈慧'],
            '宮主' => ['靈果', '元續', '金頤', '靈心', '金振', '金曉', '道妙'],
            '副宮主' => ['靈妙', '赤峰', '金了'],
            '玄妙宮' => ['金頤'],
            '玄願宮' => ['金振', '金淑'],
            '玄窕宮' => ['金曉'],
            '玄瑤宮' => ['道妙'],
            '老祖仙師直屬弟子' => ['紫元', '金雲', '金熹'],
            '元始仙師直屬弟子' => ['赤覺', '紫元', '龍勝', '龍戰', '元續'],
            '道祖仙師直屬弟子' => ['金巧', '道妙', '金悟'],
            '靈寶仙師直屬弟子' => ['靈情', '靈昡', '龍勝', '龍戰', '靈果', '靈妙', '靈心', '金源', '靈智', '靈慧', '金戒', '靈平'],
            '父皇直屬弟子' => ['鳳尊', '鳳媓'],
            '太宰仙師直屬弟子' => ['赤覺', '金頤', '金振', '金了', '金曉', '金茹'],
            '閻王仙師直屬弟子' => ['閻帝', '閻尊', '閻爵', '閻澤', '閻闇', '閻願', '赤峰', '閻㻇'],
            '太子直屬弟子' => ['龍勝', '龍戰']
        ];

        // Process Person-by-Person Truth
        $truthMatrix = [];
        foreach ($groupMembers as $groupName => $people) {
            foreach ($people as $p) {
                $truthMatrix[$p][] = $groupName;
            }
        }

        // Apply strict synchronization to ALL DharmaNames in database
        $allDNs = DharmaName::all();
        $groupCache = Group::all()->keyBy('name');

        foreach ($allDNs as $dn) {
            $intendedGroups = $truthMatrix[$dn->name] ?? [];

            $groupIds = [];
            foreach ($intendedGroups as $gName) {
                if ($groupCache->has($gName)) {
                    $groupIds[] = $groupCache->get($gName)->id;
                }
            }

            // Sync will detach all old erroneous memberships not in the intended list
            $dn->groups()->sync($groupIds);
        }
    }
}
