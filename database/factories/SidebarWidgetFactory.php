<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SidebarWidget>
 */
class SidebarWidgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(2),
            'icon' => $this->faker->word,
            'order' => $this->faker->numberBetween(1, 10),
            'has_container' => $this->faker->boolean,
            'properties' => json_encode($this->faker->words(3)),
        ];
    }
}
