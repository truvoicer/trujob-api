<?php

namespace Database\Factories;

use App\Enums\Order\OrderStatus;
use App\Enums\Price\PriceType;
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
        return [
            'price_type' => $this->faker->randomElement(
                PriceType::cases()
            )->value,
            'status' => $this->faker->randomElement(
                OrderStatus::cases()
            )->value,
        ];
    }
}
