<?php

namespace Database\Factories\listing;

use App\Models\Category;
use App\Models\ListingCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingCategory>
 */
class ListingCategoryFactory extends Factory
{
    protected $model = ListingCategory::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ids = array_map(function ($item) {
            return $item['id'];
        }, Category::all()->toArray());
        return [
            'category_id' => fake()->randomElement($ids),
        ];
    }
}
