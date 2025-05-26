<?php

namespace App\Http\Controllers\List;

use App\Http\Controllers\Controller;
use App\Http\Services\Inventory\InventoryService;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;

class GetShoppingListController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function __invoke(): JsonResponse
    {
        $inventories = $this->inventoryService->shoppingList();

        if ($inventories === null) {
            return response()->json([
                'message' => 'Família não encontrada'
            ], 404);
        }

        return response()->json([
            'message' => $inventories
        ], 200);
    }
}
