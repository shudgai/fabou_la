<?php

namespace Database\Seeders;

use App\Models\DharmaName;
use App\Models\Group;
use Illuminate\Database\Seeder;

class DharmaGroupSeeder extends Seeder
{
    public function run(): void
    {
        // Source of Truth: Group-Centric Definitions (Synchronized with excel_data.json)
        $groupMembers = [
            '玄通宮' => ['靈果', '靈妙', '靈智', '靈慧'],
            '玄應宮' => ['元續', '赤峰', '閻闇', '金悟'],
            '玄心宮' => ['龍戰', '龍勝', '金熹'],
            '玄妙宮' => ['金頤'],
            '玄昇宮' => ['靈心', '閻澤', '閻爵', '金護'],
            '玄願宮' => ['金振', '金淑'],
            '玄法宮' => ['靈昡'],
            '玄閻宮' => ['閻尊', '金了'],
            '玄窕宮' => ['金曉', '金諦', '金彩'],
            '玄瑤宮' => ['道妙'],
            '玄義宮' => ['閻願'],

            '殿中之人' => ['鳳尊', '金巧', '赤覺', '紫元', '靈情', '閻帝', '鳳媓', '靈昡', '龍勝', '龍戰', '閻尊'],
            '世代交替' => ['鳳尊', '鳳媓', '龍勝', '龍戰', '閻尊', '閻澤', '閻闇', '赤峰', '靈慧', '閻㻇'],
            '龍鳳閻合脈' => ['鳳尊', '鳳媓', '龍勝', '龍戰', '閻尊', '閻闇'],
            '同享皇恩' => ['鳳尊', '金巧', '赤覺', '紫元', '靈情', '閻帝', '鳳媓', '靈昡', '龍勝', '龍戰', '閻尊', '閻闇', '元續', '赤峰', '閻㻇', '金熹'],
            '享享皇天恩' => ['鳳尊', '金巧', '赤覺', '紫元', '靈情', '閻帝', '鳳媓', '靈昡', '龍勝', '龍戰', '閻尊', '元續', '金熹', '靈平'],

            '太玄三連脈' => ['金巧', '赤覺', '紫元'],
            '太玄四連脈' => ['金巧', '赤覺', '紫元', '靈昡'],
            '玉聖大四喜' => ['金巧', '赤覺', '紫元', '靈情'],
            '極玄靈合脈' => ['金巧', '赤覺', '紫元', '靈情', '靈昡'],

            '虎賁軍' => ['閻帝', '閻願'],
            '元帥副元帥' => ['閻帝', '閻尊', '閻爵', '閻澤', '閻闇', '閻願'],
            '耀紫軍' => ['龍勝', '龍戰'],
            '黑曜軍' => ['閻尊', '閻闇'],
            '虎甲軍' => ['閻爵', '閻澤'],
            '帝爵兩兄弟' => ['閻帝', '閻爵'],

            '副宮主' => ['靈妙', '赤峰', '閻澤', '金了'],
            '宮主' => ['靈果', '元續', '龍戰', '金頤', '靈心', '金振', '靈昡', '閻尊', '金曉', '道妙', '閻願'],
            '宮主副宮主' => ['靈果', '靈妙', '元續', '赤峰', '龍戰', '金頤', '靈心', '閻澤', '金振', '靈昡', '閻尊', '金了', '金曉', '道妙', '閻願'],
            '各宮' => ['玄通宮', '玄應宮', '玄心宮', '玄妙宮', '玄昇宮', '玄願宮', '玄法宮', '玄閻宮', '玄窕宮', '玄瑤宮', '玄義宮'],

            '老祖仙師直屬弟子' => ['紫元', '金雲', '金熹'],
            '元始仙師直屬弟子' => ['赤覺', '紫元', '龍勝', '龍戰', '元續'],
            '道祖仙師直屬弟子' => ['金巧', '道妙', '金悟'],
            '靈寶仙師直屬弟子' => ['靈情', '靈昡', '龍勝', '龍戰', '靈果', '靈妙', '靈心', '金源', '靈智', '靈慧', '金戒', '靈平'],
            '父皇直屬弟子' => ['鳳尊', '鳳媓'],
            '太宰仙師直屬弟子' => ['赤覺', '金頤', '金振', '金了', '金曉', '金茹'],
            '閻王仙師直屬弟子' => ['閻帝', '閻尊', '閻爵', '閻澤', '閻闇', '閻願', '赤峰', '閻㻇'],
            '太子直屬弟子' => ['龍勝', '龍戰'],
            '全體殿生' => [],
        ];

        // Ensure '全體殿生' includes everyone
        $groupMembers['全體殿生'] = DharmaName::pluck('name')->toArray();

        // Resolve Meta-Groups: If a group's "member" list contains the name of another group (like '各宮' containing '玄通宮'), 
        // expand it to include all members of that referenced group.
        $finalGroupMembers = [];
        foreach ($groupMembers as $groupName => $people) {
            $expandedList = [];
            foreach ($people as $p) {
                if (isset($groupMembers[$p]) && $p !== $groupName) {
                    $expandedList = array_merge($expandedList, $groupMembers[$p]);
                } else {
                    $expandedList[] = $p;
                }
            }
            $finalGroupMembers[$groupName] = array_unique($expandedList);
        }

        // Process Person-by-Person Truth
        $truthMatrix = [];
        foreach ($finalGroupMembers as $groupName => $people) {
            foreach ($people as $p) {
                $truthMatrix[$p][] = $groupName;
            }
        }

        $groupCache = Group::all()->keyBy('name');

        // Apply strict synchronization to ALL DharmaNames in database
        $allDNs = DharmaName::all();
        foreach ($allDNs as $dn) {
            $intendedGroups = $truthMatrix[$dn->name] ?? [];
            $groupIds = [];
            foreach ($intendedGroups as $gName) {
                if ($groupCache->has($gName)) {
                    $groupIds[] = $groupCache->get($gName)->id;
                }
            }
            $dn->groups()->sync($groupIds);
        }
    }
}
