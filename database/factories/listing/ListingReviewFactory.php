<?php

namespace Database\Factories\listing;

use App\Models\ListingReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingReview>
 */
class ListingReviewFactory extends Factory
{
    protected $model = ListingReview::class;
    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (ListingReview $listingReview) {
            $listing = $listingReview->listing()->first();
            $user = $listing->user()->first();
            if ($user->id === $listingReview->user_id) {
                $newUser = User::where('id', '<>', $listingReview->user_id)->first();
                $listingReview->user_id = $newUser->id;
            }
            return $listingReview;
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

        $fake = fake();
        return [
            'user_id' => fake()->randomElement($userIds),
            'rating' => $fake->numberBetween(1, 5),
            'review' => $fake->text(200)
        ];
    }
}
