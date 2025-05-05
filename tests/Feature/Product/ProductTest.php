<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
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

    public function test_user_can_create_product()
    {
        $productData = [
            'nome' => 'Arroz',
            'preco' => 20.50,
            'quantidade_estoque' => 5,
            'foto' => 'https://example.com/arroz.jpg',
            'local_compra' => 'Supermercado XYZ',
            'departamento' => 'Alimentos'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/product', $productData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'nome',
                'preco',
                'quantidade_estoque',
                'foto',
                'local_compra',
                'departamento',
                'user_id',
                'created_at',
                'updated_at'
            ]);

        $this->assertDatabaseHas('products', [
            'nome' => 'Arroz',
            'user_id' => $this->user->id
        ]);
    }

    public function test_user_can_update_their_product()
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id
        ]);

        $updateData = [
            'nome' => 'Arroz Atualizado',
            'preco' => 25.90
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/product/{$product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'nome' => 'Arroz Atualizado',
                'preco' => 25.90
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'nome' => 'Arroz Atualizado',
            'preco' => 25.90
        ]);
    }

    public function test_user_cannot_update_other_users_product()
    {
        $otherUser = User::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $updateData = [
            'nome' => 'Arroz Atualizado',
            'preco' => 25.90
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/product/{$product->id}", $updateData);

        $response->assertStatus(404);
    }

}
