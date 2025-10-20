<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials_returns_token()
    {
        $password = 'secret123';
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt($password),
        ]);

        $response = $this->postJson('/api/users/login', [
            'email' => 'test@example.com',
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user' => ['id', 'email', 'token']]);

        // token should be present on the returned user
        $this->assertArrayHasKey('token', $response->json('user'));
    }

    public function test_login_with_invalid_credentials_returns_401()
    {
        $user = User::factory()->create([
            'email' => 'someone@example.com',
            'password' => bcrypt('rightpassword'),
        ]);

        $response = $this->postJson('/api/users/login', [
            'email' => 'someone@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid email or password']);
    }

    public function test_get_users_requires_authentication()
    {
        $response = $this->getJson('/api/users');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_get_users()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure(['users']);
    }
}
