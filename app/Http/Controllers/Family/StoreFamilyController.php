<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use App\Http\Requests\Family\StoreFamilyRequest;
use App\Http\Services\Family\FamilyService;
use Illuminate\Http\JsonResponse;

class StoreFamilyController extends Controller
{
    public function __construct(
        private FamilyService $familyService
    ) {}

    public function __invoke(StoreFamilyRequest $request): JsonResponse
    {
        $family = $this->familyService->store($request->all());
        return response()->json($family, 201);
    }
}
