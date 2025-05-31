<?php

namespace Database\Factories\product;

use App\Models\ProductType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductType>
 */
class ProductTypeFactory extends Factory
{
    protected $model = ProductType::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $data = [
            ['name' => 't-shirts', 'label' => 'T-Shirts'],
            ['name' => 'jumper', 'label' => 'Jumper'],
            ['name' => 'shorts', 'label' => 'Shorts'],
            ['name' => 'cardigans', 'label' => 'Cardigans'],
            ['name' => 'coats', 'label' => 'Coats'],
            ['name' => 'jackets', 'label' => 'Jackets'],
            ['name' => 'jeans', 'label' => 'Jeans'],
        ];
        $dataKey = fake()->randomKey($data);
        return [
            'name' => $data[$dataKey]['name'],
            'label' => $data[$dataKey]['label']
        ];
    }
}
