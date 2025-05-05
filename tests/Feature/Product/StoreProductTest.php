<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\User;
use App\Models\Family;
use App\Enums\UnidadeMedida;
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
            'local_compra' => 'Supermercado',
            'local_casa' => 'Armario Preto',
            'departamento' => 'Alimentos',
            'unidade_medida' => UnidadeMedida::KG->value
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
                    'local_casa',
                    'departamento',
                    'unidade_medida',
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
                    'local_compra' => 'Supermercado',
                    'local_casa' => 'Armario Preto',
                    'departamento' => 'Alimentos',
                    'unidade_medida' => UnidadeMedida::KG->value,
                    'families_id' => $this->family->id
                ]
            ]);

        $this->assertDatabaseHas('products', [
            'nome' => 'Arroz',
            'preco' => 10.50,
            'quantidade_estoque' => 5,
            'foto' => 'https://exemplo.com/arroz.jpg',
            'local_compra' => 'Supermercado',
            'local_casa' => 'Armario Preto',
            'departamento' => 'Alimentos',
            'unidade_medida' => UnidadeMedida::KG->value,
            'families_id' => $this->family->id
        ]);
    }

    public function test_unauthenticated_user_cannot_create_product()
    {
        $productData = [
            'nome' => 'Arroz',
            'preco' => 10.50,
            'quantidade_estoque' => 5,
            'unidade_medida' => UnidadeMedida::KG->value
        ];

        $response = $this->postJson('/api/product', $productData);

        $response->assertStatus(401);
    }

    public function test_cannot_create_product_with_invalid_data()
    {
        $invalidData = [
            'nome' => '', // nome é obrigatório
            'preco' => 'não é um número', // preço deve ser numérico
            'quantidade_estoque' => -1, // quantidade deve ser positiva
            'unidade_medida' => 'INVALIDO' // unidade de medida inválida
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/product', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'preco', 'quantidade_estoque', 'unidade_medida']);
    }

    public function test_can_create_product_with_minimal_data()
    {
        $minimalData = [
            'nome' => 'Arroz',
            'quantidade_estoque' => 5,
            'unidade_medida' => UnidadeMedida::KG->value
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
                    'unidade_medida',
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
                    'unidade_medida' => UnidadeMedida::KG->value,
                    'families_id' => $this->family->id
                ]
            ]);

        $this->assertDatabaseHas('products', [
            'nome' => 'Arroz',
            'quantidade_estoque' => 5,
            'unidade_medida' => UnidadeMedida::KG->value,
            'families_id' => $this->family->id
        ]);
    }
}
