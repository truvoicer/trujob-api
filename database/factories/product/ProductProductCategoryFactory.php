<?php

namespace Database\Factories\product;

use App\Models\ProductCategory;
use App\Models\ProductProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductProductType>
 */
class ProductProductCategoryFactory extends Factory
{
    protected $model = ProductProductCategory::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ids = array_map(function ($item) {
            return $item['id'];
        }, ProductCategory::all()->toArray());
        return [
            'product_category_id' => fake()->randomElement($ids),
        ];
    }
}
