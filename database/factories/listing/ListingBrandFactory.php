<?php

namespace Database\Factories\listing;

use App\Models\Brand;
use App\Models\ListingBrand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingBrand>
 */
class ListingBrandFactory extends Factory
{
    protected $model = ListingBrand::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ids = array_map(function ($item) {
            return $item['id'];
        }, Brand::all()->toArray());
        return [
            'brand_id' => fake()->randomElement($ids),
        ];
    }
}
