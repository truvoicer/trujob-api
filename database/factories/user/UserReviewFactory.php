<?php

namespace Database\Factories\user;

use App\Models\UserReview;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserReview>
 */
class UserReviewFactory extends Factory
{
    protected $model = UserReview::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $fake = fake();
        return [
            'rating' => $fake->numberBetween(1, 5),
            'review' => $fake->text(200)
        ];
    }
}
