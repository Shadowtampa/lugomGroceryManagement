<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Family\{AddUserToFamilyController, GetFamilyController, StoreFamilyController, UpdateFamilyController};
use App\Http\Controllers\Product\{IndexProductController, GetProductController, StoreProductController, UpdateProductController, DeleteProductController, GetProductStockController};
use App\Http\Controllers\Inventory\{IndexInventoryController, GetInventoryController, StoreInventoryController, UpdateInventoryController, DeleteInventoryController};
use App\Http\Controllers\List\{ GetShoppingListController};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/', function () {
    return "HELLO WORLD";
});

Route::post('/register', [AuthController::class, 'register']);
Route::get('/me', [AuthController::class, 'me']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::prefix('product')->middleware('auth:sanctum')->group(function () {
    Route::get('/', IndexProductController::class); // Listar produtos
    Route::get('{id}', GetProductController::class); // Exibir um produto específico
    Route::post('/', StoreProductController::class); //  Criar novo produto
    Route::put('{id}', UpdateProductController::class); // Atualizar produto
    Route::delete('{id}', DeleteProductController::class); // Deletar produto

    Route::get('{id}/stock', GetProductStockController::class); // Exibir um produto específico
});

Route::prefix('family')->middleware('auth:sanctum')->group(function () {
    Route::get('', GetFamilyController::class); // recuperar família do usuário requisitante
    Route::post('/', StoreFamilyController::class); // Criar família e adicionar o usuário solicitante à família
    Route::put('{id}', UpdateFamilyController::class); // Atualizar dados da família
    Route::post('/{user_id}', AddUserToFamilyController::class); // Adicionar um user_id à família do usuário solicitante.
});

Route::prefix('inventory')->middleware('auth:sanctum')->group(function () {
    Route::get('/', IndexInventoryController::class); // Listar produtos
    Route::put('{id}', UpdateInventoryController::class); // Atualizar inventário do produto
});

Route::prefix('list')->middleware('auth:sanctum')->group(function () {
    Route::get('/', GetShoppingListController::class); // Listar produtos
});
