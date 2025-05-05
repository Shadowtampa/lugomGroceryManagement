<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Family;
use App\Enums\UnidadeMedida;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->word,
            'preco' => $this->faker->randomFloat(2, 1, 100),
            'quantidade_estoque' => $this->faker->numberBetween(0, 100),
            'estoque_desejavel' => $this->faker->numberBetween(10, 50),
            'foto' => $this->faker->imageUrl(),
            'local_compra' => $this->faker->company,
            'local_casa' => $this->faker->randomElement([
                'Armário da Cozinha',
                'Geladeira',
                'Freezer',
                'Dispensa',
                'Armário do Banheiro',
                'Armário da Sala',
                'Quarto',
                'Lavanderia',
                'Garagem',
                'Área de Serviço'
            ]),
            'departamento' => $this->faker->word,
            'unidade_medida' => $this->faker->randomElement(array_column(UnidadeMedida::cases(), 'value')),
            'families_id' => Family::factory()
        ];
    }
}
