<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PageBlock>
 */
class PageBlockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'properties' => [],
            'has_sidebar' => $this->faker->boolean,
            'order' => $this->faker->randomNumber(),
            'default' => $this->faker->boolean,
            'nav_title' => $this->faker->sentence,
            'title' => $this->faker->sentence,
            'subtitle' => $this->faker->sentence,
            'background_image' => $this->faker->imageUrl(),
            'background_color' => $this->faker->hexColor(),
            'pagination' => $this->faker->boolean,
            'pagination_type' => $this->faker->word,
            'pagination_scroll_type' => $this->faker->word,
            'content' => $this->faker->paragraph,
        ];
    }
}
