<?php

namespace Database\Seeders\admin;

use App\Enums\Media\FileSystemType;
use App\Enums\Media\MediaType;
use App\Enums\Media\Types\Image\ImageCategory;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Feature;
use App\Models\Media;
use App\Models\MessagingGroup;
use App\Models\MessagingGroupMessage;
use App\Models\Price;
use App\Models\Site;
use App\Models\User;
use App\Models\UserFollow;
use App\Models\UserMedia;
use App\Models\UserProfile;
use App\Models\UserReview;
use App\Models\UserReward;
use App\Models\UserSetting;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductColor;
use App\Models\ProductFeature;
use App\Models\ProductFollow;
use App\Models\ProductProductType;
use App\Models\ProductReview;
use App\Models\ProductType;

class SiteSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $siteData = include(database_path('data/SiteData.php'));
        if (!$siteData) {
            throw new \Exception('Error reading SiteData.php file ' . database_path('data/SiteData.php'));
        }

        foreach ($siteData as $item) {
            Site::factory()
                ->count(1)
                ->has(
                    Media::factory()
                        ->count(1)
                        ->state(function (array $attributes, Site $site) {
                            $randomNumberBetween = random_int(1, 100);
                            return [
                                'type' => MediaType::IMAGE,
                                'filesystem' => FileSystemType::EXTERNAL,
                                'category' => ImageCategory::LOGO,
                                'alt' => fake()->text(20),
                                'url' => "https://picsum.photos/id/{$randomNumberBetween}/640/480",
                            ];
                        })
                )
                ->has(
                    User::factory()
                        ->count(10)
                        ->has(
                            Product::factory()
                                ->count(5)
                                ->has(ProductReview::factory()->count(5))
                                ->has(ProductFollow::factory()->count(5))
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
                                ->has(
                                    Price::factory()
                                        ->count(3)
                                )
                        )
                        ->has(UserFollow::factory()->count(5))
                        ->has(UserProfile::factory()->count(1))
                        ->has(UserReview::factory()->count(5))
                        ->has(UserReward::factory()->count(5))
                        ->has(UserSetting::factory()->count(1))
                        ->has(UserMedia::factory()->count(1))
                        ->has(
                            MessagingGroup::factory()
                                ->has(MessagingGroupMessage::factory()->count(5))
                                ->count(5)
                        )
                )
                ->create($item);
        }
        foreach (User::all() as $user) {
            if ($user->products->count() == 0) {
                continue;
            }
            foreach ($user->products as $product) {
                $product->categories()->attach(
                    Category::all()->random(1)
                        ->pluck('id')
                );
                $product->brands()->attach(
                    Brand::all()->random(1)
                        ->pluck('id')
                );
                $product->colors()->attach(
                    Color::all()->random(1)
                        ->pluck('id')
                );
                $product->features()->attach(
                    Feature::all()->random(1)
                        ->pluck('id')
                );
                $product->productTypes()->attach(
                    ProductType::all()->random(1)
                        ->pluck('id')
                );
            }
        }
    }
}
