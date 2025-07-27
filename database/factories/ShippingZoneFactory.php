<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShippingZone>
 */
class ShippingZoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $label = $this->faker->unique()->word;
        $name = Str::slug($label);
        return [
            'name' => $name,
            'label' => $label,
            'description' => $this->faker->sentence,
            'is_active' => $this->faker->boolean,
            'all' => $this->faker->boolean,
            'order' => $this->faker->numberBetween(1, 100),
        ];
    }
}
