<?php

namespace Database\Factories;

use App\Enums\Subscription\SubscriptionIntervalUnit;
use App\Enums\Subscription\SubscriptionTenureType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PriceSubscriptionItem>
 */
class PriceSubscriptionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'frequency_interval_unit' => SubscriptionIntervalUnit::MONTH->value,
            'frequency_interval_count' => $this->faker->numberBetween(1, 12),
            'tenure_type' => SubscriptionTenureType::REGULAR->value,
            'sequence' => $this->faker->numberBetween(1, 100),
            'total_cycles' => $this->faker->numberBetween(1, 12),
            'price_value' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}
