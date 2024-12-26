<?php

namespace Database\Factories\listing;

use App\Models\ListingProductType;
use App\Models\ProductType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingProductType>
 */
class ListingProductTypeFactory extends Factory
{
    protected $model = ListingProductType::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ids = array_map(function ($item) {
            return $item['id'];
        }, ProductType::all()->toArray());
        return [
            'product_type_id' => fake()->randomElement($ids),
        ];
    }
}
