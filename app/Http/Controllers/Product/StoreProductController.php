<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Services\Product\ProductService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Request;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class StoreProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function __invoke(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->store($request->toArray());

            return response()->json([
                'message' => 'Product criado com sucesso!',
                'Product' => $product,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
