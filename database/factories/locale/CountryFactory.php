<?php

namespace Database\Factories\locale;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    protected $model = Country::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->country,
            'iso2' => $this->faker->countryCode,
            'iso3' => $this->faker->countryISOAlpha3,
            'phone_code' => '+' . $this->faker->numberBetween(1, 999),
            'is_active' => $this->faker->boolean,
        ];
    }
}
