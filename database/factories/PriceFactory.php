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

        $country = Country::where('iso2', 'GB')->first();
        if (!$country) {
            throw new Exception('Required country not found.');
        }
        $currency = Currency::where('code', 'GBP')->first();
        if (!$currency) {
            throw new Exception('Required currency not found.');
        }
        $userAdminService = app(UserAdminService::class);
        $testUserData = DefaultData::TEST_USER_DATA;
        $user = $userAdminService->getUserRepository()->findOneBy(
            [['email', '=', $testUserData['email']]]
        );
        if (!$user instanceof User) {
            throw new \Exception("Error finding user");
        }
        return [
            'created_by_user_id' => $user->id,
            'currency_id' => $currency->id,
            'country_id' => $country->id,
            'price_type' => PriceType::ONE_TIME->value,
            'valid_from' => now(),
            'valid_to' => now()->addDays(30),
            'is_active' => true,
            'amount' => fake()->randomFloat(2, 10, 1000),
        ];
    }
}
