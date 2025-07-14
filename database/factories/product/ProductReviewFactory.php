<?php

namespace Database\Factories\product;

use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductReview>
 */
class ProductReviewFactory extends Factory
{
    protected $model = ProductReview::class;
    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (ProductReview $productReview) {
            $product = $productReview->product()->first();
            $user = $product->user()->first();
            if ($user->id === $productReview->user_id) {
                $newUser = User::where('id', '<>', $productReview->user_id)->first();
                if (!$newUser) {
                    $newUser = User::factory()->create([
                        'email' => fake()->unique()->safeEmail()
                    ]);
                }
                $productReview->user_id = $newUser->id;
            }
            return $productReview;
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
