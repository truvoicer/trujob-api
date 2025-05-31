<?php

namespace Database\Factories\product;

use App\Models\Product;
use App\Models\ProductFollow;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductFollow>
 */
class ProductFollowFactory extends Factory
{
    protected $model = ProductFollow::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (ProductFollow $productFollow) {
            $product = $productFollow->product()->first();
            $user = $product->user()->first();
            if ($user->id === $productFollow->user_id) {
                $newUser = User::where('id', '<>', $productFollow->user_id)->first();
                $productFollow->user_id = $newUser->id;
            }
            return $productFollow;
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
