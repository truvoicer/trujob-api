<?php

namespace Database\Factories\product;

use App\Models\Feature;
use App\Models\ProductFeature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductFeature>
 */
class ProductFeatureFactory extends Factory
{
    protected $model = ProductFeature::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ids = array_map(function ($item) {
            return $item['id'];
        }, Feature::all()->toArray());
        return [
            'feature_id' => fake()->randomElement($ids),
        ];
    }
}
