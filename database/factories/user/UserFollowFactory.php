<?php

namespace Database\Factories\user;

use App\Models\User;
use App\Models\UserFollow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserFollow>
 */
class UserFollowFactory extends Factory
{
    protected $model = UserFollow::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (UserFollow $userFollow) {
            if ($userFollow->follow_user_id === $userFollow->user_id) {
                $newUser = User::where('id', '<>', $userFollow->user_id)->first();

                if ($newUser) {
                    $userFollow->user_id = $newUser->id;
                }
            }
            return $userFollow;
        });
    }
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $userIds = array_map(function ($user) {
            return $user['id'];
        }, User::all()->toArray());

        return [
            'follow_user_id' => fake()->randomElement($userIds)
        ];
    }
}
