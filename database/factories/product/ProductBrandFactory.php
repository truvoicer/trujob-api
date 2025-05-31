<?php

namespace Database\Factories\product;

use App\Models\Brand;
use App\Models\ProductBrand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductBrand>
 */
class ProductBrandFactory extends Factory
{
    protected $model = ProductBrand::class;
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
