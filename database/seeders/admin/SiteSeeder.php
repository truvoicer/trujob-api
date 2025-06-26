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
use App\Models\PriceType;
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
use App\Models\ProductCategory;
use App\Models\ProductFollow;
use App\Models\ProductReview;
use Exception;
use Illuminate\Database\Eloquent\Factories\Sequence;

class SiteSeeder extends Seeder
{

    public function sequence(Sequence $sequence, bool $isDefault = false): array{
        $priceTypeCount = PriceType::count();
        if ($priceTypeCount < 1) {
            throw new Exception('No price types found.');
        }
        $randomPriceType = PriceType::all()->random();
        if (!$randomPriceType) {
            throw new Exception('Required price type not found.');
        }
        $priceType = PriceType::where('name', 'one_time')->first();
        // if ($sequence->index == 0) {

        // dd($sequence->index == 0 ? $priceType->id : $randomPriceType->id);
        // }
        if (!$priceType) {
            throw new Exception('Required price type not found.');
        }
        return [
            'price_type_id' => $isDefault ? $priceType->id : $randomPriceType->id,
            'is_default' => $isDefault ? true : false,
        ];
    }

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
                                        ->state(new Sequence(
                                            fn (Sequence $sequence) => $this->sequence($sequence, true)
                                        ))
                                        ->count(1)
                                )
                                ->has(
                                    Price::factory()
                                        ->state(new Sequence(
                                            fn (Sequence $sequence) => $this->sequence($sequence)
                                        ))
                                        ->count(2)
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
                $product->productCategories()->attach(
                    ProductCategory::all()->random(1)
                        ->pluck('id')
                );
            }
        }
    }
}
