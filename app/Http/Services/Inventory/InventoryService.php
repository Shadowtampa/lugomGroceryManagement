<?php

namespace App\Http\Services\Inventory;

use App\Http\Services\Service;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class InventoryService extends Service
{
    public function index()
    {
        $user = auth()->user();
        $family = $user->family;

        if ($family === null) {
            return null;
        }

        return $family->inventories()->get();
    }

    public function update(array $request): Inventory
    {
        $user = auth()->user();
        $family = $user->family;

        $inventory = Inventory::where('family_id', $family->id)
            ->where('product_id', $request['product_id'])
            ->firstOrFail();

        $inventory->fill(array_filter($request, fn($value, $key) => in_array($key, [
            'stock',
            'desirable_stock',
        ]), ARRAY_FILTER_USE_BOTH));

        $inventory->save();

        return $inventory;
    }

    public function shoppingList(): String | null
    {
        $user = auth()->user();
        $family = $user->family;

        if ($family === null) {
            return null;
        }

        $inventories = $family->inventories()
            ->with('product') // Eager loading para evitar N+1 queries
            ->get();

        $message = "";

        foreach($inventories as $inventory) {
            $needToBuy = abs($inventory->stock - $inventory->desirable_stock);

            if ($needToBuy === 0) {
                continue;
            }

            $productName = $inventory->product->nome;
            $productMeasureUnit = $inventory->product->unidade_medida->label();

            if ($needToBuy > 1) {
                $productMeasureUnit .= "s";
            }

            $message .= "Precisa comprar $needToBuy $productMeasureUnit de $productName\n";
        }


        return $message;
    }

}
