<?php

namespace Database\Factories\listing;

use App\Models\Listing;
use App\Models\ListingFollow;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingFollow>
 */
class ListingFollowFactory extends Factory
{
    protected $model = ListingFollow::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (ListingFollow $listingFollow) {
            $listing = $listingFollow->listing()->first();
            $user = $listing->user()->first();
            if ($user->id === $listingFollow->user_id) {
                $newUser = User::where('id', '<>', $listingFollow->user_id)->first();
                $listingFollow->user_id = $newUser->id;
            }
            return $listingFollow;
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
            'user_id' => fake()->randomElement($userIds)
        ];
    }
}
