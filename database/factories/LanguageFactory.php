<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Language>
 */
class LanguageFactory extends Factory
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
            'iso639_1' => $this->faker->languageCode(),
            'iso639_2' => $this->faker->languageCode(),
        ];
    }
}
