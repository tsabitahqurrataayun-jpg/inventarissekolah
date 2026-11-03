<?php

namespace Database\Factories;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inventory>
 */
class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition(): array
    {
        return [
            'code' => 'INV-' . $this->faker->unique()->numerify('###'),
            'name' => $this->faker->words(3, true),
            'category' => $this->faker->randomElement(['Elektronik', 'Multimedia', 'Audio', 'Laboratorium']),
            'description' => $this->faker->sentence(),
            'total_stock' => 10,
            'available_stock' => 10,
            'condition' => 'Baik',
        ];
    }
}
