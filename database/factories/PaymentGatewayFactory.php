<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentGateway>
 */
class PaymentGatewayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $label = $this->faker->unique()->word;
        return [
            'name' => Str::slug($label),
            'label' => $label,
            'description' => $this->faker->sentence,
            'icon' => $this->faker->word,
            'is_default' => $this->faker->boolean,
            'is_active' => $this->faker->boolean,
            'settings' => ['key' => $this->faker->word, 'value' => $this->faker->word],
            'required_fields' => ['field1' => $this->faker->word, 'field2' => $this->faker->word],
        ];
    }
}
