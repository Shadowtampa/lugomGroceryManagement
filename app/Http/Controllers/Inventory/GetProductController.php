<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Services\Product\ProductService;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class GetProductController extends Controller
{
    public function __construct(private ProductService $todoService) {}

    public function __invoke( int $id): JsonResponse
    {
        $todos = $this->todoService->get($id);

        return response()->json($todos);
    }
}
