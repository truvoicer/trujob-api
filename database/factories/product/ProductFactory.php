<?php

namespace Database\Factories\product;

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
        $data = include(database_path('data/ProductTypeData.php'));
        if (!$data) {
            throw new \Exception('Error reading ProductTypeData.php file ' . database_path('data/ProductTypeData.php'));
        }

        $fake = fake();
        $title = $fake->text(20);
        return [
            'product_type_id' => $this->faker->numberBetween(1, count($data)),
            "name" => HelperService::toSlug($title),
            "title" => $title,
            "description" => $fake->text(100),
            "active" => $fake->boolean(),
            "allow_offers" => $fake->boolean(),
        ];
    }
}
