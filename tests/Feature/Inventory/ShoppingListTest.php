<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use App\Models\Family;
use App\Models\Product;
use App\Models\Inventory;
use App\Enums\UnidadeMedida;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShoppingListTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_shopping_list_from_their_family()
    {
        $family = Family::create([
            'nome' => 'Test Family',
            'foto' => null
        ]);

        $user = User::factory()->create([
            'families_id' => $family->id
        ]);

        $product1 = Product::create([
            'nome' => 'Arroz',
            'preco' => 10.99,
            'foto' => null,
            'local_compra' => 'Supermarket',
            'local_casa' => 'Kitchen',
            'departamento' => 'Food',
            'unidade_medida' => UnidadeMedida::KG
        ]);

        $product2 = Product::create([
            'nome' => 'Feijão',
            'preco' => 8.99,
            'foto' => null,
            'local_compra' => 'Supermarket',
            'local_casa' => 'Kitchen',
            'departamento' => 'Food',
            'unidade_medida' => UnidadeMedida::KG
        ]);

        Inventory::create([
            'family_id' => $family->id,
            'product_id' => $product1->id,
            'stock' => 1,
            'desirable_stock' => 5
        ]);

        Inventory::create([
            'family_id' => $family->id,
            'product_id' => $product2->id,
            'stock' => 2,
            'desirable_stock' => 3
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/list');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => "Precisa comprar 4 Quilogramas de Arroz\nPrecisa comprar 1 Quilograma de Feijão\n"
            ]);
    }

    public function test_user_without_family_cannot_get_shopping_list()
    {
        $user = User::factory()->create([
            'families_id' => null
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/list');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Família não encontrada'
            ]);
    }
}
