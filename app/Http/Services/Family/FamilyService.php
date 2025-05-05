<?php

namespace App\Http\Services\Family;

use App\Http\Services\Service;
use App\Models\Family;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Collection;

class FamilyService extends Service
{
    public function index(): Collection
    {
        return Family::where('user_id', auth()->id())->get();
    }

    public function store(array $data): Family
    {
        return Family::create($data);
    }

    public function update(array $data): Family
    {
        $family = Family::where('id', $data['family_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $family->update([
            'nome' => $data['nome'],
            'foto' => $data['foto'] ?? null
        ]);

        return $family->fresh();
    }

    public function get(int $id): Family
    {
        return Family::where('user_id', auth()->id())->findOrFail($id);
    }
}
