<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;

class GetProductStockController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function __invoke(int $id): JsonResponse
    {
        try {
            $stock = $this->productService->getStock($id);
            return response()->json([
                'stock' => $stock
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }
    }
}
