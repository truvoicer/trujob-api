<?php

namespace Database\Factories\user;

use App\Models\Country;
use App\Models\Role;
use App\Models\User;
use App\Services\User\UserAdminService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $roleIds = array_map(function (array $role) {
            return $role['id'];
        }, Role::all()->toArray());

        $firstname = fake()->firstName();
        $lastname = fake()->lastName();
        return [
            'first_name' => $firstname,
            'last_name' => $lastname,
            'username' => fake()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => fake()->date('Y-m-d H:i:s'),
            'password' => Hash::make('Deelite4'), // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
