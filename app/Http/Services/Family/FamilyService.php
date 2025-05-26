<?php

namespace App\Http\Services\Family;

use App\Http\Services\Service;
use App\Models\Family;
use App\Models\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Support\Facades\Log;


class FamilyService extends Service
{
    public function store(array $data): Family
    {
        $user = auth()->user();

        $family = Family::create($data);

        $user->update([
            'families_id' => $family->id
        ]);

        return $family;
    }

    public function update(array $data): Family
    {
        $family = Family::where('id', $data['family_id'])
            ->firstOrFail();

        $family->update([
            'nome' => $data['nome'],
            'foto' => $data['foto'] ?? null
        ]);

        return $family->fresh();
    }

    public function get(): Family | null
    {
        try {
            $user = auth()->user();

            return $user->family;
        } catch (\Throwable $th) {
            Log::error($th);

            return null;


        }
    }

    public function addUserToFamily(int $user_id): User
    {
        $user = auth()->user();
        $family = $user->family;

        if (!$family) {
            throw new \Exception('Família não encontrada');
        }

        $userToBeAdded = User::findOrFail($user_id);

        if ($userToBeAdded->families_id) {
            throw new \Exception('Usuário já pertence a uma família');
        }

        $userToBeAdded->update([
            'families_id' => $family->id
        ]);

        return $userToBeAdded->fresh();
    }
}
