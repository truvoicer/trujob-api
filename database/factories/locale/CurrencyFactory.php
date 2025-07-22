<?php

namespace Database\Factories\locale;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{

    protected $model = Currency::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'name_plural' => $this->faker->word . 's',
            'code' => $this->faker->currencyCode,
            'symbol' => $this->faker->currencyCode,
            'is_active' => $this->faker->boolean,
        ];
    }
}
