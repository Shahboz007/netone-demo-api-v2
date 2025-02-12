<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-1 years', 'now');

        return [
            'user_id' => 2,
            'customer_id' => Customer::inRandomOrder()->value('id'),
            'status_id' => 5,
            'total_cost_price' => fake()->numberBetween(100000, 10000000),
            'total_sale_price' => fake()->numberBetween(100000, 10000000),
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
