<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Services\Inventory\InventoryService;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;

class IndexInventoryController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function __invoke(): JsonResponse
    {
        $inventories = $this->inventoryService->index();

        if ($inventories === null) {
            return response()->json([
                'message' => 'Família não encontrada'
            ], 404);
        }

        return response()->json($inventories);
    }
}
