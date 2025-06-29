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
        return [
            'dob' => fake()->date('Y-m-d H:i:s'),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
