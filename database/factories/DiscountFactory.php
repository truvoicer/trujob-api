<?php

namespace Database\Factories;

use App\Enums\Order\Discount\DiscountAmountType;
use App\Enums\Order\Discount\DiscountScope;
use App\Enums\Order\Discount\DiscountType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discount>
 */
class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'label' => $this->faker->word,
            'description' => $this->faker->sentence,
            'type' => $this->faker->randomElement(DiscountType::cases())->value,
            'amount_type' => $this->faker->randomElement(DiscountAmountType::cases())->value,
            'amount' => $this->faker->randomFloat(2, 0, 100),
            'rate' => $this->faker->randomFloat(2, 0, 100),
            'starts_at' => $this->faker->dateTimeBetween('now', '+1 year'),
            'ends_at' => $this->faker->dateTimeBetween('+1 year', '+2 years'),
            'is_active' => $this->faker->boolean,
            'usage_limit' => $this->faker->numberBetween(1, 100),
            'per_user_limit' => $this->faker->numberBetween(1, 10),
            'min_order_amount' => $this->faker->randomFloat(2, 0, 100),
            'min_items_quantity' => $this->faker->numberBetween(1, 10),
            'scope' => $this->faker->randomElement(DiscountScope::cases())->value,
            'code' => $this->faker->word,
            'is_code_required' => $this->faker->boolean,
        ];
    }
}
