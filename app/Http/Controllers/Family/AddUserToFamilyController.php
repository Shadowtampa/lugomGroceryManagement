<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use App\Http\Services\Family\FamilyService;
use Illuminate\Http\JsonResponse;

class AddUserToFamilyController extends Controller
{
    public function __construct(
        private FamilyService $familyService
    ) {}

    public function __invoke(int $user_id): JsonResponse
    {
        try {
            $user = $this->familyService->addUserToFamily($user_id);
            return response()->json($user, 200);
        } catch (\Exception $e) {
            $status = match($e->getMessage()) {
                'Usuário não encontrado' => 404,
                'Família não encontrada' => 404,
                'Usuário já pertence a uma família' => 422,
                default => 500
            };

            return response()->json(['message' => $e->getMessage()], $status);
        }
    }
}
