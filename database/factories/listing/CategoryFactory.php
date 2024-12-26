<?php

namespace Database\Factories\listing;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $data = [
            ['name' => 'winter-wear', 'label' => 'Winter Wear'],
            ['name' => 'summer-wear', 'label' => 'Summer Wear'],
            ['name' => 'spring-wear', 'label' => 'Spring Wear'],
            ['name' => 'on-sale', 'label' => 'On Sale'],
            ['name' => 'black-friday', 'label' => 'Black Friday'],
        ];
        $dataKey = fake()->randomKey($data);
        return [
            'name' => $data[$dataKey]['name'],
            'label' => $data[$dataKey]['label']
        ];
    }
}
