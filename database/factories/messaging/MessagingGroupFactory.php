<?php

namespace Database\Factories\messaging;

use App\Models\Product;
use App\Models\MessagingGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MessagingGroup>
 */
class MessagingGroupFactory extends Factory
{
    protected $model = MessagingGroup::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (MessagingGroup $messagingGroup) {
            $product = $messagingGroup->product()->first();
            if ($product->id === $messagingGroup->product_id) {
                $newProduct = Product::where('id', '<>', $messagingGroup->product_id)->first();
                if (!$newProduct) {

                    $newProduct = Product::factory()->create([
                        'user_id' => User::factory()->create()->id,
                    ]);
                }
                $messagingGroup->product_id = $newProduct->id;
            }
            return $messagingGroup;
        });
    }
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $productIds = array_map(function ($user) {
            return $user['id'];
        }, Product::all()->toArray());

        return [
            'product_id' => fake()->randomElement($productIds)
        ];
    }
}
