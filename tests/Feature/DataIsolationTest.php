<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Registry;
use App\Models\ImperialGrace;
use App\Models\WeeklyPost;
use App\Models\Grudge;
use App\Models\Teaching;
use App\Models\DharmaName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected $userA;
    protected $userB;
    protected $dharmaNameB;
    protected $master;

    protected function setUp(): void
    {
        parent::setUp();

        // 建立兩個使用者
        $this->userA = User::factory()->create(['name' => 'User A', 'email' => 'a@example.com']);
        $this->userB = User::factory()->create(['name' => 'User B', 'email' => 'b@example.com']);
        
        // 建立 B 的法號並關連
        $this->dharmaNameB = DharmaName::create(['name' => '法號B', 'order' => 1]);
        $this->userB->update(['dharma_name_id' => $this->dharmaNameB->id]);

        // 建立預設仙師
        $this->master = \App\Models\Master::create(['name' => '預設仙師']);
    }

    /** @test */
    public function test_registry_isolation()
    {
        // A 建立法寶
        Registry::create([
            'user_id' => $this->userA->id,
            'name' => 'A的秘密法寶',
            'master_id' => $this->master->id,
            'category' => 'major'
        ]);

        // B 登入
        $this->actingAs($this->userB);
        $response = $this->getJson('/registries');

        // B 應該看不到 A 的法寶
        $response->assertStatus(200);
        $this->assertCount(0, $response->json()['data']);
    }

    /** @test */
    public function test_imperial_grace_isolation()
    {
        // A 建立皇恩
        ImperialGrace::create([
            'user_id' => $this->userA->id,
            'name' => 'A的皇恩',
            'master_id' => $this->master->id
        ]);

        // B 登入
        $this->actingAs($this->userB);
        $response = $this->getJson('/imperial-graces');

        // B 應該看不到 A 的皇恩
        $response->assertStatus(200);
        $this->assertCount(0, $response->json()['registries']['data']);
    }

    /** @test */
    public function test_kaiwen_isolation()
    {
        // A 建立開文
        WeeklyPost::create([
            'user_id' => $this->userA->id,
            'title' => 'A的週記',
            'original_content' => '內容'
        ]);

        // B 登入
        $this->actingAs($this->userB);
        $response = $this->getJson('/kaiwen');

        // B 應該看不到 A 的週記
        $response->assertStatus(200);
        $this->assertCount(0, $response->json()['weeklyPosts']);
    }

    /** @test */
    public function test_grudge_isolation_even_with_dharma_name_tag()
    {
        // A 建立怨靈，並標記 B 的法號
        $grudge = Grudge::create([
            'user_id' => $this->userA->id,
            'user_name' => 'A的怨靈',
            'army_type' => '黑曜軍',
            'destination' => '黑曜軍',
            'quantity' => 1,
            'status' => '已處理'
        ]);

        // B 登入 (即使 B 有黑曜軍權限或被標記，現在也應該看不到 A 的資料)
        $this->actingAs($this->userB);
        $response = $this->getJson('/grudges');

        $response->assertStatus(200);
        $this->assertCount(0, $response->json()['paginator']['data']);
    }

    /** @test */
    public function test_teaching_linkage_is_preserved()
    {
        // 建立一個仙師，以免 master_id 為空
        $master = \App\Models\Master::create(['name' => '測試仙師']);

        // A 建立開示，並標記 B 的法號，且設定為每日開示 (is_daily=1)
        $teaching = Teaching::create([
            'user_id' => $this->userA->id,
            'title' => '給B的開示',
            'content' => '內容',
            'date' => now()->format('Y-m-d'),
            'master_id' => $master->id,
            'is_daily' => 1
        ]);
        $teaching->dharmaNames()->attach($this->dharmaNameB->id);

        // B 登入
        $this->actingAs($this->userB);
        $response = $this->getJson('/teachings?master_id=0');

        // B 「應該」要能看到這筆開示
        $response->assertStatus(200);
        $this->assertCount(1, $response->json()['records']['data']);
    }
}
