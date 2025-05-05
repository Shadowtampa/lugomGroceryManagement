<?php

namespace Tests\Feature\Family;

use App\Models\User;
use App\Models\Family;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreFamilyTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar um usuário e gerar token para todos os testes
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_user_can_create_family()
    {
        $familyData = [
            'nome' => 'Família Silva',
            'foto' => 'https://exemplo.com/foto.jpg'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/family', $familyData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'nome',
                'foto',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJson([
                'nome' => 'Família Silva',
                'foto' => 'https://exemplo.com/foto.jpg',
                'user_id' => $this->user->id
            ]);

        $this->assertDatabaseHas('families', [
            'nome' => 'Família Silva',
            'foto' => 'https://exemplo.com/foto.jpg',
            'user_id' => $this->user->id
        ]);
    }

    public function test_unauthenticated_user_cannot_create_family()
    {
        $familyData = [
            'nome' => 'Família Silva',
            'foto' => 'https://exemplo.com/foto.jpg'
        ];

        $response = $this->postJson('/api/family', $familyData);

        $response->assertStatus(401);
    }

    public function test_cannot_create_family_with_invalid_data()
    {
        $invalidData = [
            'nome' => '', // nome é obrigatório
            'foto' => str_repeat('a', 256) // foto excede o limite de 255 caracteres
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/family', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'foto']);
    }

    public function test_can_create_family_with_minimal_data()
    {
        $minimalData = [
            'nome' => 'Família Silva'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/family', $minimalData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'nome',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJson([
                'nome' => 'Família Silva',
                'user_id' => $this->user->id
            ]);

        $this->assertDatabaseHas('families', [
            'nome' => 'Família Silva',
            'user_id' => $this->user->id
        ]);
    }
}
