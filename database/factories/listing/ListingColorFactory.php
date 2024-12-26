<?php

namespace Database\Factories\listing;

use App\Models\Color;
use App\Models\ListingColor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingColor>
 */
class ListingColorFactory extends Factory
{
    protected $model = ListingColor::class;
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
