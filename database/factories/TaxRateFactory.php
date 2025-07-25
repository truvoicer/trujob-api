<?php

namespace Database\Factories;

use App\Enums\Order\Tax\TaxRateAmountType;
use App\Enums\Order\Tax\TaxRateType;
use App\Enums\Order\Tax\TaxScope;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaxRate>
 */
class TaxRateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'label' => $this->faker->word,
            'type' => $this->faker->randomElement(
                TaxRateType::cases()
            )->value,
            'amount_type' => $this->faker->randomElement(
                TaxRateAmountType::cases()
            )->value,
            'rate' => $this->faker->randomFloat(2, 0, 100),
            'amount' => $this->faker->randomFloat(2, 0, 100),
            'is_active' => $this->faker->boolean(),
            'scope' => $this->faker->randomElement(
                TaxScope::cases()
            )->value
        ];
    }
}
