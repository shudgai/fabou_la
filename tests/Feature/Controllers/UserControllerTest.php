<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use App\Models\Group;
use App\Models\DharmaName;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $normalUser;

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

        $this->normalUser = User::create([
            'name' => 'Normal User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);
    }

    /** @test */
    public function test_user_controller_actions()
    {
        $this->actingAs($this->adminUser);

        // Index
        $response = $this->getJson('/users');
        $response->assertStatus(200);

        // Store
        $dn = DharmaName::create(['name' => '元功', 'order' => 5]);
        $storeData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'role' => 'user',
            'dharma_name_id' => $dn->id
        ];
        $response = $this->postJson('/users', $storeData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);

        $newUserId = $response->json()['id'];

        // Update
        $updateData = [
            'name' => 'Updated User Name',
            'email' => 'newuser@example.com',
            'role' => 'user'
        ];
        $response = $this->putJson("/users/{$newUserId}", $updateData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $newUserId, 'name' => 'Updated User Name']);

        // Destroy self should fail
        $response = $this->deleteJson("/users/{$this->adminUser->id}");
        $response->assertStatus(400);

        // Destroy other should succeed
        $response = $this->deleteJson("/users/{$newUserId}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $newUserId]);
    }

    /** @test */
    public function test_group_controller_actions()
    {
        $this->actingAs($this->adminUser);

        // Store group
        $response = $this->postJson('/api/groups', ['name' => '宮A']);
        $response->assertStatus(201);
        $groupId = $response->json()['id'];

        // Index groups
        $response = $this->getJson('/api/groups');
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => '宮A']);

        // Show group
        $response = $this->getJson("/api/groups/{$groupId}");
        $response->assertStatus(200);

        // Update group
        $dn1 = DharmaName::create(['name' => '法A', 'order' => 1]);
        $dn2 = DharmaName::create(['name' => '法B', 'order' => 2]);
        $response = $this->putJson("/api/groups/{$groupId}", [
            'name' => '更新宮A',
            'dharma_name_ids' => [$dn1->id, $dn2->id]
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('groups', ['id' => $groupId, 'name' => '更新宮A']);

        // Destroy group
        $response = $this->deleteJson("/api/groups/{$groupId}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('groups', ['id' => $groupId]);
    }

    /** @test */
    public function test_dharma_name_controller_actions()
    {
        $this->actingAs($this->adminUser);

        DharmaName::create(['name' => '法名A', 'order' => 1]);
        DharmaName::create(['name' => '法名B', 'order' => 2]);

        $response = $this->getJson('/dharma-names');
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => '法名A']);
    }

    /** @test */
    public function test_role_controller_actions()
    {
        $this->actingAs($this->adminUser);

        // Role controller index is currently blank, but route should be defined
        $response = $this->getJson('/roles');
        $response->assertStatus(200);
    }
}
