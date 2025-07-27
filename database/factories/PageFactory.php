<?php

namespace Database\Factories;

use App\Enums\ViewType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'view' => $this->faker->randomElement(ViewType::cases())->value,
            'name' => $this->faker->unique()->word,
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'permalink' => $this->faker->slug,
            'is_active' => $this->faker->boolean,
            'is_home' => $this->faker->boolean,
            'is_featured' => $this->faker->boolean,
            'is_protected' => $this->faker->boolean,
            'has_sidebar' => $this->faker->boolean,
            'settings' => [
                'seo_title' => $this->faker->sentence,
                'seo_description' => $this->faker->paragraph,
                'seo_keywords' => implode(',', $this->faker->words(3)),
            ],
        ];
    }
}
