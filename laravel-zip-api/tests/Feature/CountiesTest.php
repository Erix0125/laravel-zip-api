<?php

namespace Tests\Feature;

use App\Models\County;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class CountiesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function test_index_returns_counties()
    {
        County::factory()->create(['name' => 'Pest']);
        County::factory()->create(['name' => 'Baranya']);

        $response = $this->getJson('/api/counties');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Pest'])
            ->assertJsonFragment(['name' => 'Baranya']);
    }

    public function test_create_requires_authentication()
    {
        $response = $this->postJson('/api/counties', ['name' => 'NewCounty']);
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_county()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/counties', ['name' => 'NewCounty']);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'NewCounty']);

        $this->assertDatabaseHas('counties', ['name' => 'NewCounty']);
    }

    public function test_modify_returns_404_when_not_found()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->patchJson('/api/counties/9999', ['name' => 'X']);
        $response->assertStatus(404)
            ->assertJson(['message' => 'County not found']);
    }

    public function test_authenticated_user_can_modify_county()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $county = County::factory()->create(['name' => 'OldName']);

        $response = $this->patchJson("/api/counties/".$county->id, ['name' => 'NewName']);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'NewName']);

        $this->assertDatabaseHas('counties', ['id' => $county->id, 'name' => 'NewName']);
    }

    public function test_delete_returns_404_when_not_found()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->deleteJson('/api/counties/9999');
        $response->assertStatus(404)
            ->assertJson(['message' => 'County not found']);
    }

    public function test_authenticated_user_can_delete_county()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $county = County::factory()->create(['name' => 'ToDelete']);

        $response = $this->deleteJson("/api/counties/{$county->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'County deleted successfully']);

        $this->assertDatabaseMissing('counties', ['id' => $county->id]);
    }

    // $token = $user->createToken('TestToken')->plainTextToken;

    // $response = $this->withHeaders([
    //     'Authorization' => 'Bearer ' . $token,
    // ])->postJson...
}
