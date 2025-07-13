<?php

namespace Database\Factories\product;

use App\Enums\Product\ProductCategory;
use App\Enums\Product\ProductType;
use App\Enums\Product\ProductUnit;
use App\Enums\Product\ProductWeightUnit;
use App\Models\Product;
use App\Services\HelperService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $fake = fake();
        $title = $fake->text(20);
        return [
            'type' => $this->faker->randomElement(
                array_map(fn($type) => $type->value, ProductType::cases())
            ),
            "name" => HelperService::toSlug($title),
            "title" => $title,
            "description" => $fake->text(100),
            "active" => $fake->boolean(),
            "allow_offers" => $fake->boolean(),
            "sku" => $fake->unique()->word() . '-' . $fake->numberBetween(1000, 9999),
            "has_weight" => $fake->boolean(),
            "has_height" => $fake->boolean(),
            "has_width" => $fake->boolean(),
            "has_depth" => $fake->boolean(),
            "weight_unit" => $this->faker->randomElement(
                array_map(fn($unit) => $unit->value, ProductWeightUnit::cases())
            ),
            "height_unit" => $this->faker->randomElement(
                array_map(fn($unit) => $unit->value, ProductUnit::cases())
            ),
            "width_unit" => $this->faker->randomElement(
                array_map(fn($unit) => $unit->value, ProductUnit::cases())
            ),
            "depth_unit" => $this->faker->randomElement(
                array_map(fn($unit) => $unit->value, ProductUnit::cases())
            ),
            "weight" => $fake->randomFloat(2, 0, 100),
            "height" => $fake->randomFloat(2, 0, 100),
            "width" => $fake->randomFloat(2, 0, 100),
            "depth" => $fake->randomFloat(2, 0, 100),
        ];
    }
}
