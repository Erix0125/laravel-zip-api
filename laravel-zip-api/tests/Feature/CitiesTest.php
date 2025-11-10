<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\County;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class CitiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_cities_in_county()
    {
        $county = County::factory()->create(['id' => '1', 'name' => 'Pest']);
        City::factory()->create(['id' => '1', 'name' => 'CityA', 'zip_code' => '1000', 'county_id' => $county->id]);
        City::factory()->create(['id' => '2', 'name' => 'CityB', 'zip_code' => '2000', 'county_id' => $county->id]);

        $response = $this->getJson("/api/counties/{$county->id}/cities");

        $response->assertStatus(200)
            ->assertJsonStructure(['cities' => [['id', 'name', 'zip', 'county']]])
            ->assertJsonFragment(['county' => $county->name])
            ->assertJsonFragment(['name' => 'CityA']);
    }

    public function test_list_cities_returns_404_when_county_missing()
    {
        $response = $this->getJson('/api/counties/9999/cities');
        $response->assertStatus(404)
            ->assertJson(['message' => 'County not found']);
    }

    public function test_list_first_letters()
    {
        $county = County::factory()->create(['id' => '1', 'name' => 'Pest']);
        City::factory()->create(['name' => 'Alpha', 'zip_code' => '1000', 'county_id' => $county->id]);
        City::factory()->create(['name' => 'Beta', 'zip_code' => '2000', 'county_id' => $county->id]);
        City::factory()->create(['name' => 'Alfa', 'zip_code' => '3000', 'county_id' => $county->id]);

        $response = $this->getJson("/api/counties/{$county->id}/abc");

        $response->assertStatus(200)
            ->assertJsonStructure(['letters']);

        $letters = $response->json('letters');
        $this->assertContains('A', $letters);
        $this->assertContains('B', $letters);
    }

    public function test_list_by_first_letter()
    {
        $county = County::factory()->create(['id' => '1', 'name' => 'Pest']);
        City::factory()->create(['name' => 'Alpha', 'zip_code' => '1000', 'county_id' => $county->id]);
        City::factory()->create(['name' => 'Beta', 'zip_code' => '2000', 'county_id' => $county->id]);

        $response = $this->getJson("/api/counties/{$county->id}/abc/A");

        $response->assertStatus(200)
            ->assertJsonStructure(['cities' => [['id', 'name', 'zip', 'county']]]);

        $this->assertCount(1, $response->json('cities'));
        $this->assertEquals('Alpha', $response->json('cities.0.name'));
    }

    public function test_create_city_requires_auth()
    {
        $county = County::factory()->create(['id' => '1', 'name' => 'Pest']);

        $this->postJson("/api/counties/{$county->id}/cities", [
            'name' => 'NewCity',
            'zip_code' => '3000'
        ])->assertStatus(401);
    }

    public function test_authenticated_user_can_create_city()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $county = County::factory()->create(['id' => '1', 'name' => 'Pest']);
        $response = $this->postJson("/api/counties/{$county->id}/cities", [
            'name' => 'NewCity',
            'zip_code' => '3000'
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'NewCity']);

        $this->assertDatabaseHas('cities', ['name' => 'NewCity', 'county_id' => $county->id]);
    }

    public function test_create_returns_404_for_missing_county()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/counties/9999/cities', [
            'name' => 'X',
            'zip_code' => '1234'
        ]);

        $response->assertStatus(404)
            ->assertJson(['message' => 'County not found']);
    }

    public function test_modify_returns_404_when_county_missing()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->patchJson('/api/counties/9999/cities/1', ['name' => 'X']);
        $response->assertStatus(404)
            ->assertJson(['message' => 'County not found']);
    }

    public function test_modify_returns_404_when_city_missing()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $county = County::factory()->create(['id' => '1', 'name' => 'Pest']);

        $response = $this->patchJson("/api/counties/{$county->id}/cities/9999", ['name' => 'X']);
        $response->assertStatus(404)
            ->assertJson(['message' => 'City not found in the specified county']);
    }

    public function test_authenticated_user_can_modify_city()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $county = County::factory()->create(['id' => '1', 'name' => 'Pest']);
        $city = City::factory()->create(['id' => '1', 'name' => 'OldCity', 'zip_code' => '4000', 'county_id' => $county->id]);

        $response = $this->patchJson("/api/counties/{$county->id}/cities/{$city->id}", [
            'name' => 'UpdatedCity',
            'zip_code' => '5000'
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'UpdatedCity']);

        $this->assertDatabaseHas('cities', ['id' => $city->id, 'name' => 'UpdatedCity', 'zip_code' => '5000']);
    }

    public function test_delete_returns_404_when_county_missing()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->deleteJson('/api/counties/9999/cities/1');
        $response->assertStatus(404)
            ->assertJson(['message' => 'County not found']);
    }

    public function test_delete_returns_404_when_city_missing()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $county = County::factory()->create(['id' => '1', 'name' => 'Pest']);

        $response = $this->deleteJson("/api/counties/{$county->id}/cities/9999");
        $response->assertStatus(404)
            ->assertJson(['message' => 'City not found in the specified county']);
    }

    public function test_authenticated_user_can_delete_city()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $county = County::factory()->create(['id' => '1', 'name' => 'Pest']);
        $city = City::factory()->create(['name' => 'ToDelete', 'zip_code' => '6000', 'county_id' => $county->id]);

        $response = $this->deleteJson("/api/counties/{$county->id}/cities/{$city->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'City deleted successfully']);

        $this->assertDatabaseMissing('cities', ['id' => $city->id]);
    }
}
