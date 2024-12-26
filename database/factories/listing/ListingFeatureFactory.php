<?php

namespace Database\Factories\listing;

use App\Models\ListingFeature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingFeature>
 */
class ListingFeatureFactory extends Factory
{
    protected $model = ListingFeature::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $data = [
            ['value' => 'Used', 'label' => 'Condition'],
            ['value' => 'New', 'label' => 'Condition'],
            ['value' => 'Like New', 'label' => 'Condition'],
        ];
        $dataKey = fake()->randomKey($data);
        return [
            'label' => $data[$dataKey]['label'],
            'value' => $data[$dataKey]['value'],
        ];
    }
}
