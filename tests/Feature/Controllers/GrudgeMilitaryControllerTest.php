<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use App\Models\DharmaName;
use App\Models\Grudge;
use App\Models\MilitaryRecord;

class GrudgeMilitaryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $normalUser1;
    protected $normalUser2;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::create(['name' => '管理員', 'role_code' => 'admin']);
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
        $this->adminUser->roles()->attach($roleAdmin->id);

        // Setup dharma name '閻尊' for User 1 to allow '黑曜軍'
        $dn1 = DharmaName::create(['name' => '閻尊', 'order' => 1]);
        $this->normalUser1 = User::create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'dharma_name_id' => $dn1->id
        ]);

        $this->normalUser2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);
    }

    /** @test */
    public function test_grudge_controller_crud()
    {
        $this->actingAs($this->normalUser1);

        // 1. Store
        $storeData = [
            'user_name' => '怨靈A',
            'quantity' => 10,
            'status' => '已處理',
            'destination' => '已處理',
            'remarks_text' => '測試'
        ];

        $response = $this->postJson('/grudges', $storeData);
        $response->assertStatus(201);
        $grudgeId = $response->json()['id'];

        // Retrieve and assert decrypted
        $grudge = Grudge::find($grudgeId);
        $this->assertNotNull($grudge);
        $this->assertEquals('怨靈A', $grudge->user_name);
        $this->assertEquals(10, $grudge->quantity);

        // 2. Index
        $response = $this->getJson('/grudges');
        $response->assertStatus(200);
        
        $data = $response->json()['paginator']['data'];
        $this->assertNotEmpty($data);
        $this->assertEquals('怨靈A', $data[0]['user_name']);

        // 3. Date Groups
        $response = $this->getJson('/grudges/date-groups');
        $response->assertStatus(200);

        // 4. Update
        $updateData = [
            'user_name' => '怨靈A(更新)',
            'quantity' => 15
        ];
        $response = $this->putJson("/grudges/{$grudgeId}", $updateData);
        $response->assertStatus(200);
        $this->assertEquals('怨靈A(更新)', $grudge->fresh()->user_name);

        // 5. Destroy
        $response = $this->deleteJson("/grudges/{$grudgeId}");
        $response->assertStatus(200);
        $this->assertSoftDeleted('grudges', ['id' => $grudgeId]);
    }

    /** @test */
    public function test_grudge_batch_store()
    {
        $this->actingAs($this->normalUser1);

        $batchData = [
            'items' => [
                [
                    'user_name' => '批量怨1',
                    'quantity' => 5,
                    'status' => '已處理',
                    'destination' => '已處理',
                    'remarks_text' => '閻尊(全數)'
                ],
                [
                    'user_name' => '批量怨2',
                    'quantity' => 8,
                    'status' => '待處理',
                    'remarks_text' => '未處理'
                ]
            ]
        ];

        $response = $this->postJson('/grudges/batch', $batchData);
        $response->assertStatus(201);

        $grudges = Grudge::where('user_id', $this->normalUser1->id)->get();
        $this->assertCount(2, $grudges);
        $this->assertTrue($grudges->contains(fn($g) => $g->user_name === '批量怨1'));
        $this->assertTrue($grudges->contains(fn($g) => $g->user_name === '批量怨2'));
    }

    /** @test */
    public function test_grudge_isolation()
    {
        $grudge = Grudge::create([
            'user_id' => $this->normalUser2->id,
            'user_name' => 'User2的怨靈',
            'quantity' => 5,
            'status' => '已處理'
        ]);

        $this->actingAs($this->normalUser1);

        // Should not see in index
        $response = $this->getJson('/grudges');
        $response->assertStatus(200);
        $data = $response->json()['paginator']['data'];
        $names = collect($data)->pluck('user_name');
        $this->assertNotContains('User2的怨靈', $names);

        // Should not update
        $response = $this->putJson("/grudges/{$grudge->id}", ['user_name' => '非法修改']);
        $response->assertStatus(400); // GrudgeService returns false on unauthorized

        // Should not delete
        $response = $this->deleteJson("/grudges/{$grudge->id}");
        $response->assertStatus(400); // GrudgeService returns false on unauthorized
    }

    /** @test */
    public function test_military_record_controller_crud()
    {
        $this->actingAs($this->normalUser1); // Has '黑曜軍' permission via '閻尊'

        // 1. Store
        $storeData = [
            'user_name' => '張三',
            'army_type' => '黑曜軍',
            'quantity' => '100',
            'know_date' => '2026-05-20',
            'process_date' => '2026-05-21',
            'destination' => '前線',
            'user_remarks' => '一般備註',
            'remarks_text' => '詳細備註',
            'yan_zun' => '50',
            'yan_an' => '50'
        ];

        $response = $this->postJson('/military-records', $storeData);
        $response->assertStatus(201);
        $recordId = $response->json()['id'];

        $record = MilitaryRecord::find($recordId);
        $this->assertNotNull($record);
        $this->assertEquals('張三', $record->user_name);
        $this->assertEquals(100, $record->quantity);

        // 2. Index
        $response = $this->getJson('/military-records?army_type=黑曜軍');
        $response->assertStatus(200);
        $response->assertJsonStructure(['records', 'armyCounts', 'breakdownTotals']);

        // 3. Date Groups
        $response = $this->getJson('/military-records/date-groups?army_type=黑曜軍');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'armyCounts']);

        // 4. Update
        $updateData = [
            'user_name' => '張三(更新)',
            'quantity' => '120'
        ];
        $response = $this->putJson("/military-records/{$recordId}", $updateData);
        $response->assertStatus(200);
        $this->assertEquals('張三(更新)', $record->fresh()->user_name);

        // 5. Destroy
        $response = $this->deleteJson("/military-records/{$recordId}");
        $response->assertStatus(200);
        $this->assertSoftDeleted('military_records', ['id' => $recordId]);
    }

    /** @test */
    public function test_military_record_authorization()
    {
        $this->actingAs($this->normalUser2); // Has NO army permissions

        $storeData = [
            'user_name' => '張三',
            'army_type' => '黑曜軍',
            'quantity' => '100'
        ];

        $response = $this->postJson('/military-records', $storeData);
        $response->assertStatus(403);
    }
}
