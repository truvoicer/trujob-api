<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Region>
 */
class RegionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->country,
            'is_active' => $this->faker->boolean,
            'admin_name' => $this->faker->name,
            'toponym_name' => $this->faker->name,
            'category' => $this->faker->word,
            'description' => $this->faker->text,
            'lng' => $this->faker->longitude,
            'lat' => $this->faker->latitude,
            'population' => $this->faker->numberBetween(1000, 1000000),
        ];
    }
}
