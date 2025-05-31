<?php

namespace Database\Factories\product;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    protected $model = Brand::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $data = [
            ['name' => 'armani', 'label' => 'Armani'],
            ['name' => 'nike', 'label' => 'Nike'],
            ['name' => 'adidas', 'label' => 'Adidas'],
            ['name' => 'fila', 'label' => 'Fila'],
            ['name' => 'elesse', 'label' => 'Elesse'],
            ['name' => 'calvin_klein', 'label' => 'Calvin Klein'],
            ['name' => 'tommy', 'label' => 'Tommy'],
        ];
        $dataKey = fake()->randomKey($data);
        return [
            'name' => $data[$dataKey]['name'],
            'label' => $data[$dataKey]['label']
        ];
    }
}
