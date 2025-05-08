<?php

namespace Database\Seeders\listing;

use App\Enums\Media\FileSystemType;
use App\Enums\Media\MediaType;
use App\Enums\Media\Types\Image\ImageCategory;
use App\Models\Listing;
use App\Models\ListingBrand;
use App\Models\ListingCategory;
use App\Models\ListingColor;
use App\Models\ListingFeature;
use App\Models\ListingFollow;
use App\Models\ListingProductType;
use App\Models\ListingReview;
use App\Models\Media;
use Illuminate\Database\Seeder;

class ListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Listing::factory()
            ->count(5)
            ->has(ListingFeature::factory()->count(1))
            ->has(ListingFeature::factory()->count(1))
            ->has(ListingReview::factory()->count(5))
            ->has(ListingFollow::factory()->count(5))
            ->has(ListingBrand::factory()->count(1))
            ->has(ListingColor::factory()->count(1))
            ->has(
                Media::factory()
                    ->count(1)
                    ->state(function (array $attributes, Listing $listing) {
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
            ->has(ListingCategory::factory()->count(5))
            ->has(ListingProductType::factory()->count(5));
    }
}
