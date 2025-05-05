<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use App\Http\Services\Family\FamilyService;
use Illuminate\Http\JsonResponse;

class GetFamilyController extends Controller
{
    public function __construct(private FamilyService $familyService) {}

    public function __invoke(int $id): JsonResponse
    {
        try {
            $family = $this->familyService->get($id);
            return response()->json($family, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Família não encontrada'], 404);
        }
    }
}
