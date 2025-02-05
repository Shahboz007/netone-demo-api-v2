<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompletedOrder>
 */
class CompletedOrderFactory extends Factory
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
            'order_id' => Order::inRandomOrder()->value('id'),
            'status_id' => 5,
            'total_cost_price' => fake()->numberBetween(100000, 10000000),
            'total_sale_price' => fake()->numberBetween(100000, 10000000),
            'customer_old_balance' => fake()->numberBetween(100000, 10000000),
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
