<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Family\{GetFamilyController, IndexFamilyController, StoreFamilyController, UpdateFamilyController};
use App\Http\Controllers\Product\{IndexProductController, GetProductController, StoreProductController, UpdateProductController};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/', function () {
    return "HELLO WORLD";
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::prefix('product')->middleware('auth:sanctum')->group(function () {
    Route::get('/', IndexProductController::class); // Listar todos os todos
    Route::get('{id}', GetProductController::class); // Exibir um todo específico
    Route::post('/', StoreProductController::class); //  Criar novo todo
    Route::put('{id}', UpdateProductController::class); // Atualizar todo
});

Route::prefix('family')->middleware('auth:sanctum')->group(function () {
    Route::get('/', IndexFamilyController::class);
    Route::get('{id}', GetFamilyController::class);
    Route::post('/', StoreFamilyController::class);
    Route::put('{id}', UpdateFamilyController::class);
});
