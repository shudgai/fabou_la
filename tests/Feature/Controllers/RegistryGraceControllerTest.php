<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use App\Models\Master;
use App\Models\DharmaName;
use App\Models\Registry;
use App\Models\DharmaNameRegistry;
use App\Models\ImperialGrace;

class RegistryGraceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user1;
    protected $user2;
    protected $master;
    protected $dharmaName;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        $this->user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        $this->master = Master::create(['name' => '老祖仙師']);
        $this->dharmaName = DharmaName::create(['name' => '靈妙', 'order' => 1]);
    }

    /** @test */
    public function test_registry_controller_crud()
    {
        $this->actingAs($this->user1);

        // 1. Store
        $storeData = [
            'name' => '護身符',
            'master_id' => $this->master->id,
            'category' => 'major',
            'purpose' => '護身',
            'effect' => '平安',
            'acquisition_method' => '求得',
            'content' => '密咒',
            'remarks' => '無',
            'record_date' => '2026-05-20',
            'obtained_date' => '2026-05-21',
            'status' => '已求得',
            'dharma_name_registries' => [
                [
                    'dharma_name_id' => $this->dharmaName->id,
                    'status' => '已求得',
                    'remarks' => ['特註'],
                    'related_personnel' => ['親友A']
                ]
            ]
        ];

        $response = $this->postJson('/registries', $storeData);
        $response->assertStatus(201); // returns 201 Created
        $registryId = $response->json()['id'];

        $registry = Registry::find($registryId);
        $this->assertNotNull($registry);
        $this->assertEquals('護身符', $registry->name);
        $this->assertEquals($this->user1->id, $registry->user_id);

        $this->assertDatabaseHas('dharma_name_registries', [
            'registry_id' => $registryId,
            'dharma_name_id' => $this->dharmaName->id
        ]);

        // 2. Index
        $response = $this->getJson('/registries?category=major&master_id=' . $this->master->id);
        $response->assertStatus(200);
        
        // Match the first record inside the paginated data
        $data = $response->json()['data'];
        $this->assertNotEmpty($data);
        $this->assertEquals('護身符', $data[0]['name']);

        // 3. Update
        $updateData = [
            'name' => '護身符(更新)',
            'master_id' => $this->master->id,
            'category' => 'major',
            'dharma_name_registries' => [
                [
                    'dharma_name_id' => $this->dharmaName->id,
                    'status' => '已求得',
                    'remarks' => ['更新特註'],
                    'related_personnel' => ['親友A', '親友B']
                ]
            ]
        ];

        $response = $this->putJson("/registries/{$registryId}", $updateData);
        $response->assertStatus(200);
        
        $registry = $registry->fresh();
        $this->assertEquals('護身符(更新)', $registry->name);

        // 4. Update Personnel Remarks
        $dnr = DharmaNameRegistry::where('registry_id', $registryId)->firstOrFail();
        $response = $this->patchJson("/registries/personnel/{$dnr->id}/remarks", [
            'remarks' => ['最新備註']
        ]);
        $response->assertStatus(200);
        $this->assertEquals(['最新備註'], $dnr->fresh()->remarks);

        // 5. Reorder
        $response = $this->postJson('/registries/reorder', [
            'orders' => [
                ['id' => $registryId, 'sort_order' => 5]
            ]
        ]);
        $response->assertStatus(200);
        $this->assertEquals(5, Registry::find($registryId)->sort_order);

        // 6. Delete Dharma Name Registry
        $response = $this->deleteJson("/dharma-name-registries/{$dnr->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('dharma_name_registries', ['id' => $dnr->id]);

        // 7. Destroy Registry
        $response = $this->deleteJson("/registries/{$registryId}");
        $response->assertStatus(200);
        $this->assertSoftDeleted('registries', ['id' => $registryId]);
    }

    /** @test */
    public function test_registry_batch_store()
    {
        $this->actingAs($this->user1);

        $batchData = [
            [
                'name' => '批量寶1',
                'master_id' => $this->master->id,
                'category' => 'major',
                'dharma_name_registries' => [
                    [
                        'custom_name' => '靈妙',
                        'obtained_date' => '2026-05-21',
                        'remarks' => ['批註1']
                    ]
                ]
            ],
            [
                'name' => '批量寶2',
                'master_id' => $this->master->id,
                'category' => 'minor'
            ]
        ];

        $response = $this->postJson('/registries/batch', $batchData);
        $response->assertStatus(200);

        $registries = Registry::where('user_id', $this->user1->id)->get();
        $this->assertCount(2, $registries);
        $this->assertTrue($registries->contains(fn($r) => $r->name === '批量寶1'));
        $this->assertTrue($registries->contains(fn($r) => $r->name === '批量寶2'));
    }

    /** @test */
    public function test_registry_isolation()
    {
        $registry = Registry::create([
            'user_id' => $this->user2->id,
            'name' => 'User2的寶',
            'master_id' => $this->master->id,
            'category' => 'major'
        ]);

        $this->actingAs($this->user1);

        // Should not see in index
        $response = $this->getJson('/registries');
        $response->assertStatus(200);
        
        $data = $response->json()['data'];
        $names = collect($data)->pluck('name');
        $this->assertNotContains('User2的寶', $names);

        // Should not update
        $response = $this->putJson("/registries/{$registry->id}", ['name' => '非法修改']);
        $response->assertStatus(403);

        // Should not delete
        $response = $this->deleteJson("/registries/{$registry->id}");
        $response->assertStatus(403);
    }

    /** @test */
    public function test_imperial_grace_controller_crud()
    {
        $this->actingAs($this->user1);

        // 1. Store Registry
        $storeData = [
            'name' => '皇恩寶',
            'master_id' => $this->master->id,
            'purpose' => '超拔',
            'record_date' => '2026-05-20',
            'obtained_date' => '2026-05-21',
            'status' => '已登記',
            'is_multi' => true,
            'remarks' => '說明',
            'dharma_name_registries' => [
                [
                    'dharma_name_id' => $this->dharmaName->id,
                    'status' => '已求得',
                    'remarks' => ['特註皇恩']
                ]
            ]
        ];

        $response = $this->postJson('/imperial-graces/registry', $storeData);
        $response->assertStatus(201);
        $graceId = $response->json()['id'];

        $grace = ImperialGrace::find($graceId);
        $this->assertNotNull($grace);
        $this->assertEquals('皇恩寶', $grace->name);
        $this->assertEquals($this->user1->id, $grace->user_id);

        // 2. Index
        $response = $this->getJson('/imperial-graces?master_id=' . $this->master->id);
        $response->assertStatus(200);
        $response->assertJsonStructure(['registries', 'userGraces', 'folderCounts', 'unobtainedCount']);

        // 3. Update Registry
        $updateData = [
            'name' => '皇恩寶(更新)',
            'master_id' => $this->master->id,
            'purpose' => '超拔更新',
            'dharma_name_registries' => [
                [
                    'dharma_name_id' => $this->dharmaName->id,
                    'status' => '已求得',
                    'remarks' => ['特註皇恩更新']
                ]
            ]
        ];

        $response = $this->putJson("/imperial-graces/registry/{$graceId}", $updateData);
        $response->assertStatus(200);
        
        $grace = $grace->fresh();
        $this->assertEquals('皇恩寶(更新)', $grace->name);

        // 4. Reorder
        $response = $this->postJson('/imperial-graces/registry/reorder', [
            'orders' => [
                ['id' => $graceId, 'sort_order' => 10]
            ]
        ]);
        $response->assertStatus(200);
        $this->assertEquals(10, ImperialGrace::find($graceId)->sort_order);

        // 5. Destroy Registry
        $response = $this->deleteJson("/imperial-graces/registry/{$graceId}");
        $response->assertStatus(200);
        $this->assertSoftDeleted('imperial_graces', ['id' => $graceId]);
    }

    /** @test */
    public function test_imperial_grace_batch_store()
    {
        $this->actingAs($this->user1);

        $batchData = [
            'master_id' => $this->master->id,
            'items' => [
                [
                    'name' => '批量皇恩1',
                    'purpose' => '超拔1',
                    'status' => '已登記',
                    'dharma_name_registries' => [
                        [
                            'custom_name' => '靈妙',
                            'obtained_date' => '2026-05-21',
                            'remarks' => ['批註皇恩1']
                        ]
                    ]
                ],
                [
                    'name' => '批量皇恩2',
                    'purpose' => '超拔2'
                ]
            ]
        ];

        $response = $this->postJson('/imperial-graces/registry/batch', $batchData);
        $response->assertStatus(200);

        $graces = ImperialGrace::where('user_id', $this->user1->id)->get();
        $this->assertCount(2, $graces);
        $this->assertTrue($graces->contains(fn($g) => $g->name === '批量皇恩1'));
        $this->assertTrue($graces->contains(fn($g) => $g->name === '批量皇恩2'));
    }

    /** @test */
    public function test_imperial_grace_isolation()
    {
        $grace = ImperialGrace::create([
            'user_id' => $this->user2->id,
            'name' => 'User2的皇恩',
            'master_id' => $this->master->id,
            'status' => '未求得'
        ]);

        $this->actingAs($this->user1);

        // Should not see in index
        $response = $this->getJson('/imperial-graces');
        $response->assertStatus(200);
        
        $registries = collect($response->json()['registries']['data']);
        $this->assertFalse($registries->contains('name', 'User2的皇恩'));

        // Should not update
        $response = $this->putJson("/imperial-graces/registry/{$grace->id}", ['name' => '非法修改']);
        $response->assertStatus(403);

        // Should not delete
        $response = $this->deleteJson("/imperial-graces/registry/{$grace->id}");
        $response->assertStatus(403);
    }
}
