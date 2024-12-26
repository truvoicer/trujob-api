<?php

namespace Database\Factories\user;

use App\Models\Country;
use App\Models\Currency;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $country = Country::where('iso2', 'GB')->first();
        $currency = Currency::where('code', 'GBP')->first();
        return [
            'country_id' => $country->id,
            'currency_id' => $currency->id,
            'rating' => fake()->randomElement([1, 2, 3, 4, 5]),
            'dob' => fake()->date('Y-m-d H:i:s'),
        ];
    }
}
