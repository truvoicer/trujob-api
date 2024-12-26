<?php

namespace Database\Seeders\listing;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
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
            ['name' => 'winter-wear', 'label' => 'Winter Wear'],
            ['name' => 'summer-wear', 'label' => 'Summer Wear'],
            ['name' => 'spring-wear', 'label' => 'Spring Wear'],
            ['name' => 'on-sale', 'label' => 'On Sale'],
            ['name' => 'black-friday', 'label' => 'Black Friday'],
        ];
        foreach ($data as $item) {
            Category::create($item);
        }
    }
}
