<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'icon' => $this->faker->imageUrl(),
            'is_default' => $this->faker->boolean,
            'is_active' => $this->faker->boolean,
            'settings' => $this->faker->randomElements(['setting1', 'setting2'], 2),
        ];
    }
}
