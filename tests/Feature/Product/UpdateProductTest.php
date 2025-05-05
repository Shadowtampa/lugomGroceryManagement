<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\User;
use App\Models\Family;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateProductTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;
    private Family $family;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar um usuário e gerar token para todos os testes
        $this->user = User::factory()->create();
        $this->family = Family::factory()->create(['user_id' => $this->user->id]);
        $this->token = $this->user->createToken('test-token')->plainTextToken;

        // Criar um produto para o usuário
        $this->product = Product::factory()->create([
            'families_id' => $this->family->id
        ]);
    }

    public function test_user_can_update_their_product()
    {
        $updateData = [
            'nome' => 'Arroz Atualizado',
            'preco' => 15.75,
            'quantidade_estoque' => 10,
            'foto' => 'https://exemplo.com/arroz-atualizado.jpg',
            'local_compra' => 'Supermercado Y',
            'departamento' => 'Alimentos'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/product/{$this->product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'nome',
                'preco',
                'quantidade_estoque',
                'foto',
                'local_compra',
                'departamento',
                'families_id',
                'created_at',
                'updated_at'
            ])
            ->assertJson([
                'nome' => 'Arroz Atualizado',
                'preco' => 15.75,
                'quantidade_estoque' => 10,
                'foto' => 'https://exemplo.com/arroz-atualizado.jpg',
                'local_compra' => 'Supermercado Y',
                'departamento' => 'Alimentos',
                'families_id' => $this->family->id
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'nome' => 'Arroz Atualizado',
            'preco' => 15.75,
            'quantidade_estoque' => 10,
            'foto' => 'https://exemplo.com/arroz-atualizado.jpg',
            'local_compra' => 'Supermercado Y',
            'departamento' => 'Alimentos',
            'families_id' => $this->family->id
        ]);
    }

    public function test_user_cannot_update_other_users_product()
    {
        $otherUser = User::factory()->create();
        $otherFamily = Family::factory()->create(['user_id' => $otherUser->id]);
        $otherProduct = Product::factory()->create([
            'families_id' => $otherFamily->id
        ]);

        $updateData = [
            'nome' => 'Produto Atualizado',
            'quantidade_estoque' => 5
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/product/{$otherProduct->id}", $updateData);

        $response->assertStatus(422);
    }

    public function test_unauthenticated_user_cannot_update_product()
    {
        $updateData = [
            'nome' => 'Produto Atualizado',
            'quantidade_estoque' => 5
        ];

        $response = $this->putJson("/api/product/{$this->product->id}", $updateData);

        $response->assertStatus(401);
    }

    public function test_cannot_update_product_with_invalid_data()
    {
        $invalidData = [
            'nome' => '', // nome é obrigatório
            'preco' => -10, // preço não pode ser negativo
            'quantidade_estoque' => -5 // quantidade não pode ser negativa
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/product/{$this->product->id}", $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'preco', 'quantidade_estoque']);
    }

    public function test_can_update_product_partially()
    {
        $partialData = [
            'nome' => 'Arroz Atualizado',
            'quantidade_estoque' => 5
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/product/{$this->product->id}", $partialData);

        $response->assertStatus(200)
            ->assertJson([
                'nome' => 'Arroz Atualizado',
                'quantidade_estoque' => 5,
                'families_id' => $this->family->id
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'nome' => 'Arroz Atualizado',
            'quantidade_estoque' => 5,
            'families_id' => $this->family->id
        ]);
    }
}
