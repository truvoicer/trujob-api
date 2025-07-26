<?php

namespace Database\Factories;

use App\Enums\Order\Shipping\ShippingRateType;
use App\Enums\Order\Shipping\ShippingUnit;
use App\Enums\Order\Shipping\ShippingWeightUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShippingRate>
 */
class ShippingRateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(
                ShippingRateType::cases()
            )->value,
            'name' => $this->faker->word,
            'label' => $this->faker->word,
            'description' => $this->faker->sentence,
            'is_active' => $this->faker->boolean,
            'has_max_dimension' => $this->faker->boolean,
            'max_dimension' => $this->faker->numberBetween(1, 100),
            'max_dimension_unit' => $this->faker->randomElement(
                ShippingUnit::cases()
            )->value,
            'has_weight' => $this->faker->boolean,
            'has_height' => $this->faker->boolean,
            'has_width' => $this->faker->boolean,
            'has_depth' => $this->faker->boolean,
            'weight_unit' => $this->faker->randomElement(
                ShippingWeightUnit::cases()
            )->value,
            'height_unit' => $this->faker->randomElement(
                ShippingUnit::cases()
            )->value,
            'width_unit' => $this->faker->randomElement(
                ShippingUnit::cases()
            )->value,
            'depth_unit' => $this->faker->randomElement(
                ShippingUnit::cases()
            )->value,
            'max_weight' => $this->faker->numberBetween(1, 100),
            'max_height' => $this->faker->numberBetween(1, 100),
            'max_width' => $this->faker->numberBetween(1, 100),
            'max_depth' => $this->faker->numberBetween(1, 100),
            'amount' => $this->faker->numberBetween(1, 100),
            'dimensional_weight_divisor' => $this->faker->numberBetween(1, 100),
        ];
    }
}
