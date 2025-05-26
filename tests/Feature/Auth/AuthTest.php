<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Family;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $family = Family::create([
            'nome' => 'Test Family',
            'foto' => null
        ]);

        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'families_id' => $family->id
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at'
                ],
                'token'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'families_id' => $family->id
        ]);
    }

    public function test_user_can_login()
    {
        $family = Family::create([
            'nome' => 'Test Family',
            'foto' => null
        ]);

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'families_id' => $family->id
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at'
                ],
                'token'
            ]);
    }

    public function test_user_can_logout()
    {
        $family = Family::create([
            'nome' => 'Test Family',
            'foto' => null
        ]);

        $user = User::factory()->create([
            'families_id' => $family->id
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'See you soon'
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
