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
        return Product::where("user_id", auth()->user()->id)->get();
    }

    // public function store($request): Todo
    // {
    //     return Todo::create($request);
    // }

    // public function update(array $request): Todo
    // {
    //     $todo = Todo::where('id', $request['todo_id'])
    //                 ->where('user_id', $request['user_id'])
    //                 ->firstOrFail();

    //     $todo->fill(array_filter($request, fn($value, $key) => in_array($key, ['title', 'description', 'status']), ARRAY_FILTER_USE_BOTH));

    //     $todo->save();

    //     return $todo;
    // }

    // public function get(int $id) : Todo
    // {
    //     return Todo::findOrFail($id);
    // }

}