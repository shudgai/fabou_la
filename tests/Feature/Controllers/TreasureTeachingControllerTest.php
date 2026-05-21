<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use App\Models\Master;
use App\Models\Treasure;
use App\Models\Teaching;
use App\Models\OtherTeaching;

class TreasureTeachingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $master;

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

        $this->master = Master::create([
            'name' => '測試老祖仙師',
            'category' => 'teaching'
        ]);
    }

    /** @test */
    public function test_master_controller_actions()
    {
        $this->actingAs($this->adminUser);

        // Index JSON
        $response = $this->getJson('/masters');
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => '測試老祖仙師']);

        // Store
        $response = $this->postJson('/masters', [
            'name' => '新仙師',
            'category' => 'imperial'
        ]);
        $response->assertStatus(201);
        $newMasterId = $response->json()['id'];

        // Update
        $response = $this->putJson("/masters/{$newMasterId}", [
            'name' => '新仙師修改',
            'category' => 'teaching'
        ]);
        $response->assertStatus(200);

        // Destroy
        $response = $this->deleteJson("/masters/{$newMasterId}");
        $response->assertStatus(200);
    }

    /** @test */
    public function test_treasure_controller_actions()
    {
        $this->actingAs($this->adminUser);

        // Store treasure
        $response = $this->postJson('/treasures', [
            'name' => '特別法印',
            'type' => 'teaching'
        ]);
        $response->assertStatus(201);
        $treasureId = $response->json()['id'];

        // Index treasures
        $response = $this->getJson('/treasures');
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => '特別法印']);

        // Destroy treasure
        $response = $this->deleteJson("/treasures/{$treasureId}");
        $response->assertStatus(200);
    }

    /** @test */
    public function test_teaching_controller_actions()
    {
        $this->actingAs($this->adminUser);

        // Rules
        $response = $this->getJson('/api/teaching-rules');
        $response->assertStatus(200);

        // Store teaching
        $response = $this->postJson('/teachings', [
            'title' => '每日法語',
            'content' => '這是開示內容',
            'date' => '2026-05-21',
            'master_id' => $this->master->id,
            'is_daily' => 0
        ]);
        $response->assertStatus(201);
        $teachingId = $response->json()['id'];

        // Index teachings
        $response = $this->getJson('/teachings');
        $response->assertStatus(200);

        // Show teaching
        $response = $this->getJson("/teachings/{$teachingId}");
        $response->assertStatus(200);

        // Update teaching
        $response = $this->putJson("/teachings/{$teachingId}", [
            'title' => '每日法語修改',
            'content' => '修改後的開示內容',
            'date' => '2026-05-21',
            'master_id' => $this->master->id
        ]);
        $response->assertStatus(200);

        // Reorder
        $response = $this->postJson('/teachings/reorder', [
            'orders' => [
                ['id' => $teachingId, 'sort_order' => 10]
            ]
        ]);
        $response->assertStatus(200);

        // Destroy teaching
        $response = $this->deleteJson("/teachings/{$teachingId}");
        $response->assertStatus(200);
    }

    /** @test */
    public function test_other_teaching_controller_actions()
    {
        $this->actingAs($this->adminUser);

        // Store other teaching
        $response = $this->postJson('/other-teachings', [
            'title' => '其他開示紀錄',
            'content' => '開示本文',
            'date' => '2026-05-21',
            'master_id' => $this->master->id
        ]);
        $response->assertStatus(201);
        $otherId = $response->json()['id'];

        // Index
        $response = $this->getJson('/other-teachings');
        $response->assertStatus(200);

        // Update
        $response = $this->putJson("/other-teachings/{$otherId}", [
            'title' => '修改其他開示'
        ]);
        $response->assertStatus(200);

        // Reorder
        $response = $this->postJson('/other-teachings/reorder', [
            'orders' => [
                ['id' => $otherId, 'sort_order' => 5]
            ]
        ]);
        $response->assertStatus(200);

        // Destroy
        $response = $this->deleteJson("/other-teachings/{$otherId}");
        $response->assertStatus(200);
    }
}
