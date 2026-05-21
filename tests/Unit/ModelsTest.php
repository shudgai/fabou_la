<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use App\Models\Group;
use App\Models\DharmaName;
use App\Models\Master;
use App\Models\Treasure;
use App\Models\Teaching;
use App\Models\OtherTeaching;
use App\Models\Registry;
use App\Models\DharmaNameRegistry;
use App\Models\ImperialGrace;
use App\Models\UserImperialGrace;
use App\Models\Grudge;
use App\Models\MilitaryRecord;
use App\Models\OtherFolder;
use App\Models\OtherRecord;
use App\Models\SelfPost;
use App\Models\WeeklyPost;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_attributes_relations_and_permissions()
    {
        // Setup Role
        $roleAdmin = Role::create(['name' => '管理員', 'role_code' => 'admin']);
        $roleUser = Role::create(['name' => '一般使用者', 'role_code' => 'user']);

        // Setup Dharma Names
        $dnA = DharmaName::create(['name' => '元續', 'order' => 1]);
        $dnChijue = DharmaName::create(['name' => '赤覺', 'order' => 2]);
        $dnNormal = DharmaName::create(['name' => '一般法號', 'order' => 3]);
        $dnMilitary = DharmaName::create(['name' => '閻尊', 'order' => 4]);

        // Setup Group
        $group = Group::create(['name' => '玄義宮']);

        // Create Users
        $userAdminRole = User::create([
            'name' => 'Admin Role User',
            'email' => 'admin_role@example.com',
            'password' => bcrypt('password')
        ]);
        $userAdminRole->roles()->attach($roleAdmin->id);

        $userAdminName = User::create([
            'name' => 'Normal Name',
            'email' => 'admin_name@example.com',
            'password' => bcrypt('password'),
            'dharma_name_id' => $dnA->id
        ]);

        $userChijue = User::create([
            'name' => 'Chijue User',
            'email' => 'chijue@example.com',
            'password' => bcrypt('password'),
            'dharma_name_id' => $dnChijue->id
        ]);

        $userNormal = User::create([
            'name' => 'Normal User',
            'email' => 'normal@example.com',
            'password' => bcrypt('password'),
            'dharma_name_id' => $dnNormal->id,
            'group_id' => $group->id
        ]);

        $userMilitary = User::create([
            'name' => 'Military User',
            'email' => 'military@example.com',
            'password' => bcrypt('password'),
            'dharma_name_id' => $dnMilitary->id
        ]);

        // Assert Admin Status
        $this->assertTrue($userAdminRole->isAdmin());
        $this->assertTrue($userAdminName->isAdmin());
        $this->assertFalse($userNormal->isAdmin());

        // Assert Chijue Status
        $this->assertTrue($userChijue->isChijue());
        $this->assertFalse($userNormal->isChijue());

        // Assert Display Names
        $this->assertEquals('元續', $userAdminName->display_name);
        $this->assertEquals('Normal User', $userNormal->name);
        $this->assertEquals('一般法號', $userNormal->display_name);

        // Assert Group and Role Relations
        $this->assertEquals($group->id, $userNormal->group->id);
        $this->assertTrue($userNormal->group->users->contains($userNormal));
        $this->assertTrue($userAdminRole->roles->contains($roleAdmin));

        // Assert Permissions Matrix
        // Admin permissions
        $adminPerms = $userAdminRole->getPermissions();
        $this->assertTrue($adminPerms['can_see_daily_teachings']);
        $this->assertEquals(['黑曜軍', '耀紫軍', '虎甲軍', '虎賁軍'], $adminPerms['allowed_armies']);

        // Advanced permissions (e.g. Chijue is in the advanced list)
        $chijuePerms = $userChijue->getPermissions();
        $this->assertTrue($chijuePerms['can_see_daily_teachings']);
        $this->assertTrue($chijuePerms['can_see_treasures']);

        // Normal user permissions
        $normalPerms = $userNormal->getPermissions();
        $this->assertFalse($normalPerms['can_see_daily_teachings']);
        $this->assertFalse($normalPerms['can_see_treasures']);

        // Military specific permission
        $militaryPerms = $userMilitary->getPermissions();
        $this->assertTrue($militaryPerms['can_see_military']);
        $this->assertEquals(['黑曜軍'], $militaryPerms['allowed_armies']);
    }

    /** @test */
    public function test_dharma_name_relations()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);
        $dn = DharmaName::create(['name' => '金巧', 'order' => 10, 'user_id' => $user->id]);
        $user->update(['dharma_name_id' => $dn->id]);

        $group = Group::create(['name' => '測試宮']);
        $dn->groups()->attach($group->id);

        $master = Master::create(['name' => '測試仙師']);
        $teaching = Teaching::create([
            'user_id' => $user->id,
            'title' => '測試開示',
            'content' => '開示內容',
            'date' => '2026-05-21',
            'master_id' => $master->id
        ]);
        $dn->teachings()->attach($teaching->id);

        // Verify relationships
        $this->assertEquals($user->id, $dn->user->id);
        $this->assertTrue($dn->groups->contains($group));
        $this->assertTrue($group->dharmaNames->contains($dn));
        $this->assertTrue($dn->teachings->contains($teaching));
    }

    /** @test */
    public function test_registry_and_dharma_name_registry_relations_and_casts()
    {
        $user = User::create(['name' => 'Tester', 'email' => 't@example.com', 'password' => bcrypt('password')]);
        $master = Master::create(['name' => '仙師']);
        
        // Test TolerantEncrypted cast
        $registry = Registry::create([
            'user_id' => $user->id,
            'master_id' => $master->id,
            'name' => '極秘法寶',
            'purpose' => '鎮宅',
            'effect' => '辟邪',
            'acquisition_method' => '授權',
            'content' => '密碼內容',
            'remarks' => '這是備註',
            'category' => 'major',
            'record_date' => '2026-05-21',
            'is_multi' => true,
        ]);

        // Verify values are transparently decrypted
        $this->assertEquals('極秘法寶', $registry->name);
        $this->assertEquals('鎮宅', $registry->purpose);
        $this->assertEquals('辟邪', $registry->effect);
        $this->assertEquals('授權', $registry->acquisition_method);
        $this->assertEquals('密碼內容', $registry->content);
        $this->assertEquals('這是備註', $registry->remarks);

        // Verify relationships
        $this->assertEquals($master->id, $registry->master->id);

        // Test DharmaNameRegistry pivot
        $dn = DharmaName::create(['name' => '紫元', 'order' => 1]);
        $dnRegistry = DharmaNameRegistry::create([
            'registry_id' => $registry->id,
            'dharma_name_id' => $dn->id,
            'custom_name' => '專用法寶',
            'related_personnel' => '親友A',
            'status' => '已領取'
        ]);

        $this->assertEquals($registry->id, $dnRegistry->registry->id);
        $this->assertEquals($dn->id, $dnRegistry->dharmaName->id);
        $this->assertTrue($registry->dharmaNameRegistries->contains($dnRegistry));
    }

    /** @test */
    public function test_imperial_grace_and_user_imperial_grace_relations()
    {
        $user = User::create(['name' => 'Tester', 'email' => 't@example.com', 'password' => bcrypt('password')]);
        $master = Master::create(['name' => '仙師']);
        
        $grace = ImperialGrace::create([
            'user_id' => $user->id,
            'master_id' => $master->id,
            'name' => '特別皇恩',
            'type' => 'grace',
            'record_date' => '2026-05-21',
        ]);

        $dnRegistry = DharmaNameRegistry::create([
            'imperial_grace_id' => $grace->id,
            'dharma_name_id' => DharmaName::create(['name' => '靈情', 'order' => 1])->id,
            'custom_name' => '登記名稱'
        ]);

        $userGrace = UserImperialGrace::create([
            'user_id' => $user->id,
            'imperial_grace_id' => $grace->id,
            'status' => 'completed'
        ]);

        // Assert relationships
        $this->assertEquals($master->id, $grace->master->id);
        $this->assertEquals($user->id, $grace->userGraces->first()->user->id);
        $this->assertEquals($grace->id, $grace->userGraces->first()->imperialGrace->id);
        $this->assertTrue($grace->dharmaNameRegistries->contains($dnRegistry));
    }

    /** @test */
    public function test_grudge_and_military_records()
    {
        $user = User::create(['name' => 'Tester', 'email' => 't@example.com', 'password' => bcrypt('password')]);
        
        $grudge = Grudge::create([
            'user_id' => $user->id,
            'user_name' => '測試對象',
            'army_type' => '黑曜軍',
            'destination' => '地府',
            'quantity' => 150,
            'status' => '待處理',
            'remarks' => '怨靈備註'
        ]);

        $military = MilitaryRecord::create([
            'user_id' => $user->id,
            'date' => '2026-05-21',
            'army_type' => '耀紫軍',
            'quantity' => 500,
            'operation_type' => '增加',
            'remarks' => '軍事備註'
        ]);

        $this->assertEquals($user->id, $grudge->user->id);
        $this->assertEquals(150, $grudge->quantity);
        $this->assertEquals(500, $military->quantity);
    }

    /** @test */
    public function test_other_folders_records_and_teachings()
    {
        $user = User::create(['name' => 'Tester', 'email' => 't@example.com', 'password' => bcrypt('password')]);
        $master = Master::create(['name' => '仙師']);
        
        $folder = OtherFolder::create(['name' => '分類資料夾', 'section' => 'other']);
        
        $record = OtherRecord::create([
            'user_id' => $user->id,
            'other_folder_id' => $folder->id,
            'title' => '隨手記',
            'content' => '紀錄內容'
        ]);

        $otherTeaching = OtherTeaching::create([
            'user_id' => $user->id,
            'master_id' => $master->id,
            'title' => '其他開示',
            'content' => '開示內容',
            'date' => '2026-05-21'
        ]);

        $this->assertEquals($folder->id, $record->folder->id);
        $this->assertTrue($folder->otherRecords->contains($record));
        $this->assertEquals($master->id, $otherTeaching->master->id);
        $this->assertEquals($user->id, $otherTeaching->user->id);
    }

    /** @test */
    public function test_treasures_and_posts()
    {
        $master = Master::create(['name' => '仙師']);
        $treasure = Treasure::create([
            'name' => '法印',
            'master_id' => $master->id,
            'type' => 'teaching'
        ]);

        $user = User::create(['name' => 'Tester', 'email' => 't@example.com', 'password' => bcrypt('password')]);

        $weekly = WeeklyPost::create([
            'user_id' => $user->id,
            'title' => '第一週週記',
            'original_content' => '原內容',
            'modified_content' => '修改後內容',
            'sort_order' => 1
        ]);

        $self = SelfPost::create([
            'user_id' => $user->id,
            'master_id' => $master->id,
            'title' => '自我開文',
            'original_content' => '原自我內容',
            'sort_order' => 1
        ]);

        $this->assertEquals($master->id, $self->master->id);
        $this->assertEquals($user->id, $weekly->user_id);
    }
}
