<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => sprintf(
                '+1 (%d) %d-%04d',
                fake()->numberBetween(200, 999),
                fake()->numberBetween(200, 999),
                fake()->numberBetween(0, 9999)
            ),
            'address' => fake()->address(),
        ];
    }
}
