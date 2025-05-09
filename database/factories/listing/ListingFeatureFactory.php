<?php

namespace Database\Factories\listing;

use App\Models\Feature;
use App\Models\ListingFeature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingFeature>
 */
class ListingFeatureFactory extends Factory
{
    protected $model = ListingFeature::class;
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
