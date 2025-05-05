<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;

class UpdateProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function __invoke(UpdateProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->update($request->all());
            return response()->json($product, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Produto não encontrado'], 422);
        }
    }
}
