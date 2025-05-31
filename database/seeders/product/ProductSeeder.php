<?php

namespace Database\Seeders\product;

use App\Enums\Media\FileSystemType;
use App\Enums\Media\MediaType;
use App\Enums\Media\Types\Image\ImageCategory;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductColor;
use App\Models\ProductFeature;
use App\Models\ProductFollow;
use App\Models\ProductProductType;
use App\Models\ProductReview;
use App\Models\Media;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::factory()
            ->count(5)
            ->has(ProductFeature::factory()->count(1))
            ->has(ProductFeature::factory()->count(1))
            ->has(ProductReview::factory()->count(5))
            ->has(ProductFollow::factory()->count(5))
            ->has(ProductBrand::factory()->count(1))
            ->has(ProductColor::factory()->count(1))
            ->has(
                Media::factory()
                    ->count(1)
                    ->state(function (array $attributes, Product $product) {
                        $randomNumberBetween = random_int(1, 100);
                        return [
                            'type' => MediaType::IMAGE,
                            'filesystem' => FileSystemType::EXTERNAL,
                            'category' => ImageCategory::THUMBNAIL,
                            'alt' => fake()->text(20),
                            'url' => "https://picsum.photos/id/{$randomNumberBetween}/700/700",
                        ];
                    })
            )
            ->has(Media::factory()->count(5))
            ->has(ProductCategory::factory()->count(5))
            ->has(ProductProductType::factory()->count(5));
    }
}
