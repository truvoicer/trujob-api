<?php

namespace Database\Seeders\product;

use App\Enums\Product\ProductCategory as ProductCategoryEnum;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (ProductCategoryEnum::cases() as $item) {

            $create = ProductCategory::query()->updateOrCreate(
                ['name' => $item->value],
                [
                    'name' => $item->value,
                    'label' => $item->label(),
                    'description' => $item->description(),
                    'active' => true,
                ]
            );
        }
    }
}
