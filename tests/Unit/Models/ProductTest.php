<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use App\Models\Family;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_method_returns_correct_stock_for_family()
    {
        // Criar uma família
        $family = Family::create([
            'nome' => 'Test Family',
            'foto' => null
        ]);

        // Criar um produto
        $product = Product::create([
            'nome' => 'Test Product',
            'preco' => 10.99,
            'foto' => null,
            'local_compra' => 'Supermarket',
            'local_casa' => 'Kitchen',
            'departamento' => 'Food',
            'unidade_medida' => 'UN'
        ]);

        // Criar um inventário para o produto na família
        Inventory::create([
            'family_id' => $family->id,
            'product_id' => $product->id,
            'stock' => 15,
            'desirable_stock' => 20
        ]);

        // Testar se o método stock retorna o valor correto
        $this->assertEquals(15, $product->stock($family->id));
    }

    public function test_stock_method_returns_zero_when_no_inventory_exists()
    {
        // Criar uma família
        $family = Family::create([
            'nome' => 'Test Family',
            'foto' => null
        ]);

        // Criar um produto
        $product = Product::create([
            'nome' => 'Test Product',
            'preco' => 10.99,
            'foto' => null,
            'local_compra' => 'Supermarket',
            'local_casa' => 'Kitchen',
            'departamento' => 'Food',
            'unidade_medida' => 'UN'
        ]);

        // Testar se o método stock retorna 0 quando não existe inventário
        $this->assertEquals(0, $product->stock($family->id));
    }

    public function test_stock_method_returns_zero_for_different_family()
    {
        // Criar duas famílias
        $family1 = Family::create([
            'nome' => 'Test Family 1',
            'foto' => null
        ]);

        $family2 = Family::create([
            'nome' => 'Test Family 2',
            'foto' => null
        ]);

        // Criar um produto
        $product = Product::create([
            'nome' => 'Test Product',
            'preco' => 10.99,
            'foto' => null,
            'local_compra' => 'Supermarket',
            'local_casa' => 'Kitchen',
            'departamento' => 'Food',
            'unidade_medida' => 'UN'
        ]);

        // Criar um inventário para o produto na família 1
        Inventory::create([
            'family_id' => $family1->id,
            'product_id' => $product->id,
            'stock' => 15,
            'desirable_stock' => 20
        ]);

        // Testar se o método stock retorna 0 para a família 2
        $this->assertEquals(0, $product->stock($family2->id));
    }
}
