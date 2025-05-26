<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\UpdateInventoryRequest;
use App\Http\Services\Inventory\InventoryService;
use Illuminate\Http\JsonResponse;

class UpdateInventoryController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function __invoke(UpdateInventoryRequest $request): JsonResponse
    {
        try {
            $inventory = $this->inventoryService->update($request->all());
            return response()->json($inventory, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Produto não encontrado'], 422);
        }
    }
}
