<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;

class DeleteProductController extends Controller
{
    public function __construct(private ProductService $productService ){}

    public function __invoke(int $id): JsonResponse
    {
        $this->productService->destroy($id);

        return response()->json(null, 200);
    }

}