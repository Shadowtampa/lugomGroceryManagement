<?php

namespace Tests\Feature\Family;

use App\Models\User;
use App\Models\Family;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetFamilyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_their_family()
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
            ->getJson('/api/family');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $family->id,
                'nome' => 'Test Family',
                'foto' => null
            ]);
    }

}
