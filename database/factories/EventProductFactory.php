<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventProduct>
 */
class EventProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'notes' => $this->faker->text,
            'latitude' => $this->faker->latitude,
            'longitude ' => $this->faker->longitude,
            'start_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'status' => $this->faker->word,
            'is_public' => $this->faker->boolean,
            'is_all_day' => $this->faker->boolean,
            'is_recurring' => $this->faker->boolean,
        ];
    }
}
