<?php

namespace Database\Seeders\listing;

use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data = [
            ['name' => 't-shirts', 'label' => 'T-Shirts'],
            ['name' => 'jumper', 'label' => 'Jumper'],
            ['name' => 'shorts', 'label' => 'Shorts'],
            ['name' => 'cardigans', 'label' => 'Cardigans'],
            ['name' => 'coats', 'label' => 'Coats'],
            ['name' => 'jackets', 'label' => 'Jackets'],
            ['name' => 'jeans', 'label' => 'Jeans'],
        ];
        foreach ($data as $item) {
            ProductType::create($item);
        }
    }
}
