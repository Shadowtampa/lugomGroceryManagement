<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use App\Http\Services\Family\FamilyService;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Family;
use Illuminate\Http\JsonResponse;

class IndexFamilyController extends Controller
{
    public function __construct(private FamilyService $familyService) {}

    public function __invoke(): JsonResponse
    {
        $families = $this->familyService->index();

        return response()->json($families);
    }
}