<?php

namespace Database\Seeders\product;

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
        $data = include(database_path('data/ProductTypeData.php'));
        if (!$data) {
            throw new \Exception('Error reading ProductTypeData.php file ' . database_path('data/ProductTypeData.php'));
        }
        foreach ($data as $index => $item) {
            if (empty($item['name'])) {
                throw new \Exception(
                    sprintf(
                        'Error at %d, name is required | ProductTypeData.php',
                        $index
                    )
                );
            }
            if (empty($item['label'])) {
                $item['label'] = ucfirst($item['name']);
            }
            $create = ProductType::query()->updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
