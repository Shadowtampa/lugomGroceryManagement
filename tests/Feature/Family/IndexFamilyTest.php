<?php

namespace Tests\Feature\Family;

use App\Models\User;
use App\Models\Family;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexFamilyTest extends TestCase
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

    public function test_user_can_list_their_families()
    {
        // Criar mais algumas famílias para o mesmo usuário
        Family::factory()->count(2)->create(['user_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/family');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'nome',
                    'foto',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJsonCount(3); // 3 famílias no total (1 do setUp + 2 criadas aqui)

        // Verificar se todas as famílias pertencem ao usuário
        $response->assertJson(function ($json) {
            return collect($json)->every(function ($family) {
                return $family['user_id'] === $this->user->id;
            });
        });
    }

    public function test_unauthenticated_user_cannot_list_families()
    {
        $response = $this->getJson('/api/family');

        $response->assertStatus(401);
    }

    public function test_user_cannot_see_other_users_families()
    {
        // Criar outro usuário com suas famílias
        $otherUser = User::factory()->create();
        Family::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/family');

        $response->assertStatus(200)
            ->assertJsonCount(1) // Apenas a família do usuário autenticado
            ->assertJson(function ($json) {
                return collect($json)->every(function ($family) {
                    return $family['user_id'] === $this->user->id;
                });
            });
    }

    public function test_empty_families_list_returns_empty_array()
    {
        // Deletar a família criada no setUp
        $this->family->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/family');

        $response->assertStatus(200)
            ->assertJson([]);
    }
}
