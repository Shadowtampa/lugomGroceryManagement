<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_their_products()
    {
        // Criar um usuário
        $user = User::factory()->create();

        // Criar alguns produtos para o usuário
        $products = Product::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        // Criar produtos para outro usuário (não devem aparecer na listagem)
        Product::factory()->count(2)->create();

        // Fazer a requisição autenticada
        $response = $this->actingAs($user)
            ->getJson('/api/product');

        // Verificar se a resposta foi bem sucedida
        $response->assertStatus(200)
            ->assertJsonCount(3) // Deve retornar apenas os 3 produtos do usuário
            ->assertJsonStructure([
                '*' => [
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
                ]
            ]);
    }

    public function test_unauthenticated_user_cannot_list_products()
    {
        $response = $this->getJson('/api/product');

        $response->assertStatus(401);
    }
}
