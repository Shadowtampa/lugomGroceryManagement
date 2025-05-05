<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use App\Http\Requests\Family\UpdateFamilyRequest;
use App\Http\Services\Family\FamilyService;
use Illuminate\Http\JsonResponse;

class UpdateFamilyController extends Controller
{
    public function __construct(
        private FamilyService $familyService
    ) {}

    public function __invoke(UpdateFamilyRequest $request): JsonResponse
    {
        try {
            $family = $this->familyService->update($request->all());
            return response()->json($family, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Família não encontrada'], 404);
        }
    }
}
