<?php

namespace Tests\Feature\Product;

use App\Models\User;
use App\Models\Family;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_product_from_their_family()
    {
        $family = Family::create([
            'nome' => 'Test Family',
            'foto' => null
        ]);

        $user = User::factory()->create([
            'families_id' => $family->id
        ]);

        $product = Product::create([
            'nome' => 'Original Product',
            'preco' => 10.99,
            'foto' => null,
            'local_compra' => 'Supermarket',
            'local_casa' => 'Kitchen',
            'departamento' => 'Food',
            'unidade_medida' => 'UN'
        ]);

        Inventory::create([
            'family_id' => $family->id,
            'product_id' => $product->id,
            'stock' => 10,
            'desirable_stock' => 20
        ]);

        $updateData = [
            'product_id' => $product->id,
            'nome' => 'Updated Product',
            'preco' => 15.99,
            'local_compra' => 'New Supermarket',
            'local_casa' => 'Pantry',
            'departamento' => 'Groceries',
            'unidade_medida' => 'KG'
        ];

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/product/{$product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'nome' => 'Updated Product',
                'preco' => 15.99,
                'local_compra' => 'New Supermarket',
                'local_casa' => 'Pantry',
                'departamento' => 'Groceries',
                'unidade_medida' => 'KG'
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'nome' => 'Updated Product',
            'preco' => 15.99,
            'local_compra' => 'New Supermarket',
            'local_casa' => 'Pantry',
            'departamento' => 'Groceries',
            'unidade_medida' => 'KG'
        ]);
    }

    public function test_user_cannot_update_product_from_another_family()
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
            'nome' => 'Original Product',
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

        $updateData = [
            'product_id' => $product->id,
            'nome' => 'Updated Product',
            'preco' => 15.99
        ];

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/product/{$product->id}", $updateData);

        $response->assertStatus(422);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'nome' => 'Original Product',
            'preco' => 10.99
        ]);
    }

    public function test_user_cannot_update_nonexistent_product()
    {
        $family = Family::create([
            'nome' => 'Test Family',
            'foto' => null
        ]);

        $user = User::factory()->create([
            'families_id' => $family->id
        ]);

        $updateData = [
            'product_id' => 999,
            'nome' => 'Updated Product',
            'preco' => 15.99
        ];

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/product/999', $updateData);

        $response->assertStatus(422);
    }
}
