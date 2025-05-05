<?php

namespace App\Http\Services\Product;

use App\Http\Services\Service;
use App\Models\Product;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ProductService extends Service
{
    public function index()
    {
        $user = auth()->user();
        return Product::whereHas('family', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
    }

    public function store($request): Product
    {
        return Product::create($request);
    }

    public function update(array $request): Product
    {
        $user = auth()->user();
        $product = Product::whereHas('family', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($request['product_id']);

        $product->fill(array_filter($request, fn($value, $key) => in_array($key, [
            'nome',
            'preco',
            'quantidade_estoque',
            'foto',
            'local_compra',
            'local_casa',
            'departamento'
        ]), ARRAY_FILTER_USE_BOTH));

        $product->save();

        return $product;
    }

    public function get(int $id): Product
    {
        $user = auth()->user();
        return Product::whereHas('family', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($id);
    }

}
