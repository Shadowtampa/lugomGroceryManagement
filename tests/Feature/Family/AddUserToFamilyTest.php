<?php

namespace Tests\Feature\Family;

use App\Models\User;
use App\Models\Family;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddUserToFamilyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_another_user_to_their_family()
    {
        $family = Family::create([
            'nome' => 'Test Family',
            'foto' => null
        ]);

        $user = User::factory()->create([
            'families_id' => $family->id
        ]);

        $userToBeAdded = User::factory()->create([
            'families_id' => null
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/family/{$userToBeAdded->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $userToBeAdded->id,
                'families_id' => $family->id
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $userToBeAdded->id,
            'families_id' => $family->id
        ]);
    }

    public function test_user_cannot_add_nonexistent_user_to_family()
    {
        $family = Family::create([
            'nome' => 'Test Family',
            'foto' => null
        ]);

        $user = User::factory()->create([
            'families_id' => $family->id
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/family/999');

        $response->assertStatus(500);
    }

    public function test_user_cannot_add_user_that_already_belongs_to_a_family()
    {
        $family1 = Family::create([
            'nome' => 'Test Family 1',
            'foto' => null
        ]);

        $family2 = Family::create([
            'nome' => 'Test Family 2',
            'foto' => null
        ]);

        $user = User::factory()->create([
            'families_id' => $family1->id
        ]);

        $userToBeAdded = User::factory()->create([
            'families_id' => $family2->id
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/family/{$userToBeAdded->id}");

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Usuário já pertence a uma família'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $userToBeAdded->id,
            'families_id' => $family2->id
        ]);
    }

    public function test_user_without_family_cannot_add_another_user()
    {
        // Criar uma família temporária para o usuário que será adicionado
        $tempFamily = Family::create([
            'nome' => 'Temp Family',
            'foto' => null
        ]);

        $user = User::factory()->create([
            'families_id' => null
        ]);

        $userToBeAdded = User::factory()->create([
            'families_id' => $tempFamily->id
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/family/{$userToBeAdded->id}");

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Família não encontrada'
            ]);
    }
}
