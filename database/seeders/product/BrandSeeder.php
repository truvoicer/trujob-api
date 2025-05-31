<?php

namespace Database\Seeders\product;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $data = [
            ['name' => 'armani', 'label' => 'Armani'],
            ['name' => 'nike', 'label' => 'Nike'],
            ['name' => 'adidas', 'label' => 'Adidas'],
            ['name' => 'fila', 'label' => 'Fila'],
            ['name' => 'ellesse', 'label' => 'Ellesse'],
            ['name' => 'calvin_klein', 'label' => 'Calvin Klein'],
            ['name' => 'tommy', 'label' => 'Tommy'],
        ];
        foreach ($data as $item) {
            Brand::create($item);
        }
    }
}
