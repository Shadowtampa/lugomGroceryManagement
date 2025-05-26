<?php

namespace Tests\Feature\Product;

use App\Models\User;
use App\Models\Family;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_product()
    {
        $family = Family::create([
            'nome' => 'Test Family',
            'foto' => null
        ]);

        $user = User::factory()->create([
            'families_id' => $family->id
        ]);

        $productData = [
            'nome' => 'New Product',
            'preco' => 15.99,
            'foto' => null,
            'local_compra' => 'Supermarket',
            'local_casa' => 'Kitchen',
            'departamento' => 'Food',
            'unidade_medida' => 'UN',
        ];

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/product', $productData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Product criado com sucesso!',
                'Product' => [
                    'nome' => 'New Product',
                    'preco' => 15.99,
                    'foto' => null,
                    'local_compra' => 'Supermarket',
                    'local_casa' => 'Kitchen',
                    'departamento' => 'Food',
                    'unidade_medida' => 'UN'
                ]
            ]);

        $this->assertDatabaseHas('products', [
            'nome' => 'New Product',
            'preco' => 15.99,
            'foto' => null,
            'local_compra' => 'Supermarket',
            'local_casa' => 'Kitchen',
            'departamento' => 'Food',
            'unidade_medida' => 'UN'
        ]);
    }

    public function test_user_cannot_create_product_without_required_fields()
    {
        $family = Family::create([
            'nome' => 'Test Family',
            'foto' => null
        ]);

        $user = User::factory()->create([
            'families_id' => $family->id
        ]);

        $productData = [
            'preco' => 15.99,
            'foto' => null,
            'local_compra' => 'Supermarket',
            'local_casa' => 'Kitchen',
            'departamento' => 'Food',
            'unidade_medida' => 'UN',
            'stock' => 10,
            'desirable_stock' => 20
        ];

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/product', $productData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome']);
    }

    public function test_user_cannot_create_product_with_invalid_data()
    {
        $family = Family::create([
            'nome' => 'Test Family',
            'foto' => null
        ]);

        $user = User::factory()->create([
            'families_id' => $family->id
        ]);

        $productData = [
            'nome' => 'New Product',
            'preco' => 'invalid_price',
            'foto' => null,
            'local_compra' => 'Supermarket',
            'local_casa' => 'Kitchen',
            'departamento' => 'Food',
            'unidade_medida' => 'UN',
        ];

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/product', $productData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['preco']);
    }

    public function test_user_can_create_product_and_inventory_is_created()
    {
        $family = Family::create([
            'nome' => 'Test Family',
            'foto' => null
        ]);

        $user = User::factory()->create([
            'families_id' => $family->id
        ]);

        $productData = [
            'nome' => 'Test Product',
            'preco' => 10.99,
            'foto' => null,
            'local_compra' => 'Supermarket',
            'local_casa' => 'Kitchen',
            'departamento' => 'Food',
            'unidade_medida' => 'UN'
        ];

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/product', $productData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Product criado com sucesso!',
                'Product' => [
                    'nome' => 'Test Product',
                    'preco' => 10.99,
                    'foto' => null,
                    'local_compra' => 'Supermarket',
                    'local_casa' => 'Kitchen',
                    'departamento' => 'Food',
                    'unidade_medida' => 'UN'
                ]
            ]);

        $product = Product::where('nome', 'Test Product')->first();

        $this->assertDatabaseHas('inventories', [
            'family_id' => $family->id,
            'product_id' => $product->id,
            'stock' => 0,
            'desirable_stock' => 0
        ]);
    }

    public function test_user_without_family_cannot_create_product()
    {
        $user = User::factory()->create([
            'families_id' => null
        ]);

        $productData = [
            'nome' => 'Test Product',
            'preco' => 10.99,
            'foto' => null,
            'local_compra' => 'Supermarket',
            'local_casa' => 'Kitchen',
            'departamento' => 'Food',
            'unidade_medida' => 'UN'
        ];

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/product', $productData);

        $response->assertStatus(500)
            ->assertJsonFragment([
                'message' => 'Família não encontrada'
            ]);
    }
}
