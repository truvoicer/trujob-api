<?php

namespace Database\Factories\user;

use App\Models\Country;
use App\Models\UserSetting;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserSetting>
 */
class UserSettingFactory extends Factory
{
    protected $model = UserSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $country = Country::where('iso2', 'GB')->first();
        if (!$country) {
            throw new Exception('Required country not found.');
        }
        $currency = $country->currency()->where('code', 'GBP')->first();
        if (!$currency) {
            throw new Exception('Required currency not found.');
        }
        return [
            'app_theme' => fake()->randomElement(['light', 'dark']),
            'push_notification' => fake()->boolean(),
            'currency_id' => $currency->id, // Assuming currency_id can be null initially
            'country_id' => $country->id, // Assuming country_id can be null initially
            'language_id' => null, // Assuming language_id can be null initially
        ];
    }
}
