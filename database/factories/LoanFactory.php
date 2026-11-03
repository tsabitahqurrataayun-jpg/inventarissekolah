<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'inventory_id' => Inventory::factory(),
            'quantity' => 1,
            'borrowed_at' => now()->subDays(3),
            'due_at' => now()->addDays(5),
            'returned_at' => null,
            'status' => 'pending',
            'notes' => $this->faker->sentence(),
        ];
    }
}
