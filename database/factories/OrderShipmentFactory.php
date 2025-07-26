<?php

namespace Database\Factories;

use App\Enums\Order\Shipping\OrderShipmentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderShipment>
 */
class OrderShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tracking_number' => $this->faker->uuid(),
            'status' => $this->faker->randomElement(
                OrderShipmentStatus::cases()
            )->value,
            'shipping_cost' => $this->faker->randomFloat(2, 0, 100),
            'estimated_delivery_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'actual_delivery_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'ship_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'weight' => $this->faker->randomFloat(2, 0, 100),
            'dimensions' => $this->faker->randomElement(['small', 'medium', 'large']),
            'notes' => $this->faker->sentence(),
        ];
    }
}
