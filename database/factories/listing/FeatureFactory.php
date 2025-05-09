<?php

namespace Database\Factories\listing;

use App\Models\Feature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class FeatureFactory extends Factory
{
    protected $model = Feature::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $data = [
            ['name' => 'Used', 'label' => 'Condition'],
            ['name' => 'New', 'label' => 'Condition'],
            ['name' => 'Like New', 'label' => 'Condition'],
        ];
        $dataKey = fake()->randomKey($data);
        return [
            'label' => $data[$dataKey]['label'],
            'name' => $data[$dataKey]['name'],
        ];
    }
}
