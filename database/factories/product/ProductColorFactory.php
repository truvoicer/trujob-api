<?php

namespace Database\Factories\product;

use App\Models\Color;
use App\Models\ProductColor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductColor>
 */
class ProductColorFactory extends Factory
{
    protected $model = ProductColor::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ids = array_map(function ($item) {
            return $item['id'];
        }, Color::all()->toArray());
        return [
            'color_id' => fake()->randomElement($ids),
        ];
    }
}
