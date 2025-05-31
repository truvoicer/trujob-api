<?php

namespace Database\Seeders\product;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
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
            ['name' => 'apple-green', 'label' => 'Apple Green'],
            ['name' => 'black', 'label' => 'Black'],
            ['name' => 'blue', 'label' => 'Blue'],
            ['name' => 'sky-blue', 'label' => 'Sky Blue'],
            ['name' => 'washed-blue', 'label' => 'Washed Blue'],
        ];
        foreach ($data as $item) {
            Color::create($item);
        }
    }
}
