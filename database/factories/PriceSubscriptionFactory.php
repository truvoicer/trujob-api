<?php

namespace Database\Factories;

use App\Enums\Subscription\SubscriptionSetupFeeFailureAction;
use App\Enums\Subscription\SubscriptionType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PriceSubscription>
 */
class PriceSubscriptionFactory extends Factory
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
            'label' => $label,
            'description' => $this->faker->sentence,
            'type' => SubscriptionType::FIXED->value,
            'start_time' => $this->faker->dateTime(),
            'has_setup_fee' => $this->faker->boolean(),
            'setup_fee_value' => $this->faker->randomFloat(2, 0, 100),
            'auto_bill_outstanding' => $this->faker->boolean(),
            'setup_fee_failure_action' => SubscriptionSetupFeeFailureAction::CANCEL->value,
            'payment_failure_threshold' => $this->faker->numberBetween(0, 999),
        ];
    }
}
