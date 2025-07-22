<?php

namespace Database\Factories;

use App\Enums\Transaction\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => $this->faker->randomElement(TransactionStatus::cases())->value,
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'currency_code' => $this->faker->currencyCode,
        ];
    }
}
