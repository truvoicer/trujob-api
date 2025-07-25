<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppMenuItem>
 */
class AppMenuItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'initial_screen' => $this->faker->boolean(),
            'label' => $this->faker->word(),
            'screen' => $this->faker->word(),
            'type' => $this->faker->word(),
            'active' => $this->faker->boolean(),
            'show_in_menu' => $this->faker->boolean(),
            'icon' => $this->faker->word(),
        ];
    }
}
