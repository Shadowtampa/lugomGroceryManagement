<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use App\Models\Family;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_inventories_from_their_family()
    {
        $family = Family::create([
            'nome' => 'Test Family',
            'foto' => null
        ]);

        $user = User::factory()->create([
            'families_id' => $family->id
        ]);

        $product1 = Product::create([
            'nome' => 'Test Product 1',
            'preco' => 10.99,
            'foto' => null,
            'local_compra' => 'Supermarket',
            'local_casa' => 'Kitchen',
            'departamento' => 'Food',
            'unidade_medida' => 'UN'
        ]);

        $product2 = Product::create([
            'nome' => 'Test Product 2',
            'preco' => 15.99,
            'foto' => null,
            'local_compra' => 'Supermarket',
            'local_casa' => 'Kitchen',
            'departamento' => 'Food',
            'unidade_medida' => 'UN'
        ]);

        $inventory1 = Inventory::create([
            'family_id' => $family->id,
            'product_id' => $product1->id,
            'stock' => 10,
            'desirable_stock' => 20
        ]);

        $inventory2 = Inventory::create([
            'family_id' => $family->id,
            'product_id' => $product2->id,
            'stock' => 15,
            'desirable_stock' => 25
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/inventory');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJson([
                [
                    'id' => $inventory1->id,
                    'family_id' => $family->id,
                    'product_id' => $product1->id,
                    'stock' => 10,
                    'desirable_stock' => 20
                ],
                [
                    'id' => $inventory2->id,
                    'family_id' => $family->id,
                    'product_id' => $product2->id,
                    'stock' => 15,
                    'desirable_stock' => 25
                ]
            ]);
    }

    public function test_user_cannot_list_inventories_from_another_family()
    {
        $family1 = Family::create([
            'nome' => 'Test Family 1',
            'foto' => null
        ]);

        $family2 = Family::create([
            'nome' => 'Test Family 2',
            'foto' => null
        ]);

        $user = User::factory()->create([
            'families_id' => $family1->id
        ]);

        $product = Product::create([
            'nome' => 'Test Product',
            'preco' => 10.99,
            'foto' => null,
            'local_compra' => 'Supermarket',
            'local_casa' => 'Kitchen',
            'departamento' => 'Food',
            'unidade_medida' => 'UN'
        ]);

        Inventory::create([
            'family_id' => $family2->id,
            'product_id' => $product->id,
            'stock' => 10,
            'desirable_stock' => 20
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/inventory');

        $response->assertStatus(200)
            ->assertJsonCount(0);
    }

    public function test_user_without_family_cannot_list_inventories()
    {
        $user = User::factory()->create([
            'families_id' => null
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/inventory');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Família não encontrada'
            ]);
    }
}
