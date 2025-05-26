<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use App\Http\Services\Family\FamilyService;
use Illuminate\Http\JsonResponse;

class GetFamilyController extends Controller
{
    public function __construct(private FamilyService $familyService) {}

    public function __invoke(): JsonResponse
    {
        $family = $this->familyService->get();

        if ($family === null){
            return response()->json([
                'message' => 'Família não encontrada'
             ],404);
        }

        return response()->json($family);
    }
}
