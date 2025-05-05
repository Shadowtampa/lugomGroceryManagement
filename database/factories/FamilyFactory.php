<?php

namespace Database\Factories;

use App\Models\Family;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FamilyFactory extends Factory
{
    protected $model = Family::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->company,
            'foto' => $this->faker->imageUrl(),
            'user_id' => User::factory()
        ];
    }
}
