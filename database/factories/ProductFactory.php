<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Family;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->word,
            'preco' => $this->faker->randomFloat(2, 1, 1000),
            'quantidade_estoque' => $this->faker->numberBetween(0, 100),
            'foto' => $this->faker->imageUrl(),
            'local_compra' => $this->faker->company,
            'departamento' => $this->faker->randomElement(['Alimentos', 'Bebidas', 'Limpeza', 'Higiene', 'Outros']),
            'families_id' => Family::factory()
        ];
    }
}
