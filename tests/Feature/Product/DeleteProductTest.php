<?php

namespace Tests\Feature\Product;

use App\Models\User;
use App\Models\Family;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_product()
    {
        $family = Family::create([
            'nome' => 'Test Family',
            'foto' => null
        ]);

        $user = User::factory()->create([
            'families_id' => $family->id
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
            'family_id' => $family->id,
            'product_id' => $product->id,
            'stock' => 10,
            'desirable_stock' => 20
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/product/{$product->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }

    public function test_user_cannot_delete_product_from_another_family()
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
            ->deleteJson("/api/product/{$product->id}");

        $response->assertStatus(404);

        $this->assertDatabaseHas('products', [
            'id' => $product->id
        ]);
    }
}
