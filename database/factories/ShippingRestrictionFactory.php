<?php

namespace Database\Factories;

use App\Enums\Order\Shipping\ShippingRestrictionAction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShippingRestriction>
 */
class ShippingRestrictionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'action' => $this->faker->randomElement(
                ShippingRestrictionAction::cases()
            )->value,
        ];
    }
}
