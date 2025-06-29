<?php

namespace Database\Factories\product;

use App\Enums\Product\ProductCategory;
use App\Enums\Product\ProductType;
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
        ];
    }
}
