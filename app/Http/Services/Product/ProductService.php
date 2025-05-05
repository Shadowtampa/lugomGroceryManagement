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

    // public function update(array $request): Todo
    // {
    //     $todo = Todo::where('id', $request['todo_id'])
    //                 ->where('user_id', $request['user_id'])
    //                 ->firstOrFail();

    //     $todo->fill(array_filter($request, fn($value, $key) => in_array($key, ['title', 'description', 'status']), ARRAY_FILTER_USE_BOTH));

    //     $todo->save();

    //     return $todo;
    // }

    public function get(int $id): Product
    {
        $user = auth()->user();
        return Product::whereHas('family', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($id);
    }

}
