<?php

namespace Database\Factories;

use App\Enums\Price\PriceType;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Price;
use App\Models\User;
use App\Services\Data\DefaultData;
use App\Services\User\UserAdminService;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Price>
 */
class PriceFactory extends Factory
{
    protected $model = Price::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        return [
            'price_type' => PriceType::ONE_TIME->value,
            'valid_from' => now(),
            'valid_to' => now()->addDays(30),
            'is_active' => true,
            'amount' => fake()->randomFloat(2, 100, 1000),
        ];
    }
}
