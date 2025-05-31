<?php

namespace Database\Factories\product;

use App\Models\Color;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Color>
 */
class ColorFactory extends Factory
{
    protected $model = Color::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $data = [
            ['name' => 'apple-green', 'label' => 'Apple Green'],
            ['name' => 'black', 'label' => 'Black'],
            ['name' => 'blue', 'label' => 'Blue'],
            ['name' => 'sky-blue', 'label' => 'Sky Blue'],
            ['name' => 'washed-blue', 'label' => 'Washed Blue'],
        ];
        $dataKey = fake()->randomKey($data);
        return [
            'name' => $data[$dataKey]['name'],
            'label' => $data[$dataKey]['label']
        ];
    }
}
