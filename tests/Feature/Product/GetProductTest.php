<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\User;
use App\Models\Family;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetProductTest extends TestCase
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

    public function test_user_can_get_their_product()
    {
        $product = Product::factory()->create([
            'families_id' => $this->family->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/product/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'nome',
                'preco',
                'quantidade_estoque',
                'foto',
                'local_compra',
                'local_casa',
                'departamento',
                'families_id',
                'created_at',
                'updated_at'
            ])
            ->assertJson([
                'id' => $product->id,
                'families_id' => $this->family->id
            ]);
    }

    public function test_user_cannot_get_other_users_product()
    {
        $otherUser = User::factory()->create();
        $otherFamily = Family::factory()->create(['user_id' => $otherUser->id]);
        $product = Product::factory()->create([
            'families_id' => $otherFamily->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/product/{$product->id}");

        $response->assertStatus(404);
    }

    public function test_unauthenticated_user_cannot_get_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/product/{$product->id}");

        $response->assertStatus(401);
    }

    public function test_returns_404_for_nonexistent_product()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/product/999');

        $response->assertStatus(404);
    }
}
