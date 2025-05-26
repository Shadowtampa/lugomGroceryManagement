<?php

namespace App\Http\Services\Product;

use App\Http\Services\Service;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ProductService extends Service
{
    public function index()
    {
        $user = auth()->user();
        $family = $user->family;

        return $family->products()->get();
    }

    public function store($request): Product
    {
        $user = auth()->user();
        $family = $user->family;

        if (!$family) {
            throw new \Exception('Família não encontrada');
        }

        $product = Product::create($request);

        Inventory::create([
            'family_id' => $family->id,
            'product_id' => $product->id,
            'stock' => 0,
            'desirable_stock' => 0
        ]);

        return $product;
    }

    public function update(array $request): Product
    {
        $user = auth()->user();
        $family = $user->family;

        $product = $family->products()->findOrFail($request['product_id']);

        $product->fill(array_filter($request, fn($value, $key) => in_array($key, [
            'nome',
            'preco',
            'foto',
            'local_compra',
            'local_casa',
            'departamento',
            'unidade_medida'
        ]), ARRAY_FILTER_USE_BOTH));

        $product->save();

        return $product;
    }

    public function get(int $id): Product
    {
        $user = auth()->user();
        $family = $user->family;

        return $family->products()->findOrFail($id);
    }

    public function getStock(int $id): int
    {
        $user = auth()->user();
        $family = $user->family;

        $product = $family->products()->findOrFail($id);

        return $product->stock($family->id);
    }

    public function destroy(int $id)
    {
        $user = auth()->user();
        $family = $user->family;

        $product = $family->products()->findOrFail($id);
        $product->delete();
    }

}
