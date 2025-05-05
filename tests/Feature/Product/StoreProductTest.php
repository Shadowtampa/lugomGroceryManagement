<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\User;
use App\Models\Family;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreProductTest extends TestCase
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

    public function test_user_can_create_product()
    {
        $productData = [
            'nome' => 'Arroz',
            'preco' => 10.50,
            'quantidade_estoque' => 5,
            'foto' => 'https://exemplo.com/arroz.jpg',
            'local_compra' => 'Supermercado X',
            'departamento' => 'Alimentos'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/product', $productData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'Product' => [
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
                ]
            ])
            ->assertJson([
                'message' => 'Product criado com sucesso!',
                'Product' => [
                    'nome' => 'Arroz',
                    'preco' => 10.50,
                    'quantidade_estoque' => 5,
                    'foto' => 'https://exemplo.com/arroz.jpg',
                    'local_compra' => 'Supermercado X',
                    'departamento' => 'Alimentos',
                    'families_id' => $this->family->id
                ]
            ]);

        $this->assertDatabaseHas('products', [
            'nome' => 'Arroz',
            'preco' => 10.50,
            'quantidade_estoque' => 5,
            'foto' => 'https://exemplo.com/arroz.jpg',
            'local_compra' => 'Supermercado X',
            'departamento' => 'Alimentos',
            'families_id' => $this->family->id
        ]);
    }

    public function test_unauthenticated_user_cannot_create_product()
    {
        $productData = [
            'nome' => 'Arroz',
            'preco' => 10.50,
            'quantidade_estoque' => 5
        ];

        $response = $this->postJson('/api/product', $productData);

        $response->assertStatus(401);
    }

    public function test_cannot_create_product_with_invalid_data()
    {
        $invalidData = [
            'nome' => '', // nome é obrigatório
            'preco' => -10, // preço não pode ser negativo
            'quantidade_estoque' => -5 // quantidade não pode ser negativa
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/product', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'preco', 'quantidade_estoque']);
    }

    public function test_can_create_product_with_minimal_data()
    {
        $minimalData = [
            'nome' => 'Arroz',
            'quantidade_estoque' => 5
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/product', $minimalData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'Product' => [
                    'id',
                    'nome',
                    'quantidade_estoque',
                    'families_id',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'message' => 'Product criado com sucesso!',
                'Product' => [
                    'nome' => 'Arroz',
                    'quantidade_estoque' => 5,
                    'families_id' => $this->family->id
                ]
            ]);

        $this->assertDatabaseHas('products', [
            'nome' => 'Arroz',
            'quantidade_estoque' => 5,
            'families_id' => $this->family->id
        ]);
    }
}
