<?php

namespace Tests\Feature\Family;

use App\Models\User;
use App\Models\Family;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateFamilyTest extends TestCase
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

    public function test_user_can_update_their_family()
    {
        $updateData = [
            'nome' => 'Família Silva Atualizada',
            'foto' => 'https://exemplo.com/foto-atualizada.jpg'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/family/{$this->family->id}", $updateData);

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
                'nome' => 'Família Silva Atualizada',
                'foto' => 'https://exemplo.com/foto-atualizada.jpg',
                'user_id' => $this->user->id
            ]);

        $this->assertDatabaseHas('families', [
            'id' => $this->family->id,
            'nome' => 'Família Silva Atualizada',
            'foto' => 'https://exemplo.com/foto-atualizada.jpg',
            'user_id' => $this->user->id
        ]);
    }

    public function test_user_cannot_update_other_users_family()
    {
        // Criar outro usuário com sua família
        $otherUser = User::factory()->create();
        $otherFamily = Family::factory()->create(['user_id' => $otherUser->id]);

        $updateData = [
            'nome' => 'Família Atualizada'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/family/{$otherFamily->id}", $updateData);

        $response->assertStatus(404);
    }

    public function test_unauthenticated_user_cannot_update_family()
    {
        $updateData = [
            'nome' => 'Família Atualizada'
        ];

        $response = $this->putJson("/api/family/{$this->family->id}", $updateData);

        $response->assertStatus(401);
    }

    public function test_cannot_update_family_with_invalid_data()
    {
        $invalidData = [
            'nome' => '', // nome é obrigatório
            'foto' => str_repeat('a', 256) // foto excede o limite de 255 caracteres
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/family/{$this->family->id}", $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'foto']);
    }

    public function test_can_update_family_partially()
    {
        $partialData = [
            'nome' => 'Família Silva Atualizada'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/family/{$this->family->id}", $partialData);

        $response->assertStatus(200)
            ->assertJson([
                'nome' => 'Família Silva Atualizada',
                'user_id' => $this->user->id
            ]);

        $this->assertDatabaseHas('families', [
            'id' => $this->family->id,
            'nome' => 'Família Silva Atualizada',
            'user_id' => $this->user->id
        ]);
    }
}
