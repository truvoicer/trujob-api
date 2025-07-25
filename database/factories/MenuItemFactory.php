<?php

namespace Database\Factories;

use App\Enums\MenuItemType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MenuItem>
 */
class MenuItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'active' => $this->faker->boolean,
            'label' => $this->faker->word,
            'type' => $this->faker->randomElement(MenuItemType::cases())->value,
            'url' => $this->faker->url,
            'target' => $this->faker->randomElement(['_self', '_blank']),
            'icon' => $this->faker->word,
            'li_class' => $this->faker->word,
            'a_class' => $this->faker->word,
            'order' => $this->faker->numberBetween(1, 100),
        ];
    }
}
