<?php

namespace Tests\Feature\Family;

use App\Models\User;
use App\Models\Family;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetFamilyTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;
    private Family $family;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar um usuário e gerar token para todos os testes
        $this->user = User::factory()->create();
        $this->family = Family::factory()->create(['user_id' => $this->user->id]);
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_user_can_get_their_family()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/family/{$this->family->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'nome',
                'foto',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJson([
                'id' => $this->family->id,
                'nome' => $this->family->nome,
                'foto' => $this->family->foto,
                'user_id' => $this->user->id
            ]);
    }

    public function test_user_cannot_get_other_users_family()
    {
        // Criar outro usuário com sua família
        $otherUser = User::factory()->create();
        $otherFamily = Family::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/family/{$otherFamily->id}");

        $response->assertStatus(404);
    }

    public function test_unauthenticated_user_cannot_get_family()
    {
        $response = $this->getJson("/api/family/{$this->family->id}");

        $response->assertStatus(401);
    }

    public function test_returns_404_for_nonexistent_family()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/family/99999');

        $response->assertStatus(404);
    }
}
