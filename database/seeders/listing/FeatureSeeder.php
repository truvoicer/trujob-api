<?php

namespace Database\Seeders\listing;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $data = [
            ['name' => 'used', 'label' => 'Used'],
            ['name' => 'new', 'label' => 'New'],
            ['name' => 'like_new', 'label' => 'Like New'],
        ];
        foreach ($data as $item) {
            Feature::create($item);
        }
    }
}
